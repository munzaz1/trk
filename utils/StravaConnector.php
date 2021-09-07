<?

class StravaConnector {

  function __construct() {
    require_once 'extensions/StravaApi.php';

    $this->tokenIsSet = False;
    $this->stravaAPI = new Iamstuartwilson\StravaApi(StravaConstants::CLIENT_ID, StravaConstants::CLIENT_SECRET);

    if (!empty($_SESSION['strava_access_token']) && !empty($_SESSION['strava_refresh_token']) && !empty($_SESSION['strava_access_token_expires_at'])) {
        $this->stravaAPI->setAccessToken(
            $_SESSION['strava_access_token'],
            $_SESSION['strava_refresh_token'],
            $_SESSION['strava_access_token_expires_at']
        );
        $this->tokenIsSet = True;
    }
  }


  public function authorize() {
    $callbackUrl = Settings::$S['ABSOLUTE_URL'] . StravaConstants::CALLBACK_ADDRESS;
    header('Location: ' . $this->stravaAPI->authenticationUrl($callbackUrl, 'auto', 'activity:read_all'));
    exit();
  }


  public function refreshToken() {
    $response = $this->stravaAPI->tokenExchangeRefresh();
    $_SESSION['strava_access_token']            = isset($response->access_token) ? $response->access_token : null;
    $_SESSION['strava_refresh_token']           = isset($response->refresh_token) ? $response->refresh_token : null;
    $_SESSION['strava_access_token_expires_at'] = isset($response->expires_at) ? $response->expires_at : null;
  }


  public function deauthorize() {
    $response = $this->stravaAPI->deauthorize();
    unset($_SESSION['strava_access_token']);
    unset($_SESSION['strava_refresh_token']);
    unset($_SESSION['strava_access_token_expires_at']);
    $this->tokenIsSet = False;
  }


  public function tokenExchange($code) {
    $response = $this->stravaAPI->tokenExchange($code);
    $_SESSION['strava_access_token']            = isset($response->access_token) ? $response->access_token : null;
    $_SESSION['strava_refresh_token']           = isset($response->refresh_token) ? $response->refresh_token : null;
    $_SESSION['strava_access_token_expires_at'] = isset($response->expires_at) ? $response->expires_at : null;
  }


  public function getActivityDataByStartTime($db, $userId, $startTime) {
    $stravaActivities = $this->stravaAPI->get('athlete/activities', array(
      'after'     => $startTime - Settings::$S['ACTIVITY_SELECTION_TIME_TOLERANCE'],
      'before'    => $startTime + Settings::$S['ACTIVITY_SELECTION_TIME_TOLERANCE'],
      'page'      => 1,
      'per_page'  => 1,
    ));
    $this->_handleErrors($stravaActivities);

    if (count($stravaActivities) == 0) {
      throw new ErrorActivityNotFound(t::m('ERROR_REMOTE_ACTIVITY_NOT_FOUND') . ' (' . t::m('LABEL_STRAVA') . ')');
    }

    $stravaActivityFromList = $stravaActivities[0];

    return $this->getActivityDataByStravaId($db, $userId, $stravaActivityFromList->id);
  }


  public function getActivityIdsFromTime($timeFrom, $timeTo) {
    $stravaActivityIds = array();

    $maxPages = 100;
    $perPage  = 100;

    $page = 0;
    while ($page < $maxPages) {
      $stravaActivities = $this->stravaAPI->get('athlete/activities', array(
        'after'     => $timeFrom,
        'before'    => $timeTo,
        'page'      => $page + 1,
        'per_page'  => $perPage,
      ));
      $this->_handleErrors($stravaActivities);

      foreach ($stravaActivities as $stravaActivity) {
        $stravaActivityIds[] = $stravaActivity->id;

        if (count($stravaActivityIds) > Settings::$S['MAX_EXTERNAL_ACTIVITIES_TO_FETCH']) {
          throw new ErrorTooManyActivities(t::m('ERROR_TOO_MANY_ACTIVITIES_TO_FETCH'));
        }
      }

      $page ++;

      if (count($stravaActivityIds) < ($page * $perPage)) {
        break;
      }
    }

    return $stravaActivityIds;
  }


  public function getActivityDataByStravaId($db, $userId, $stravaActivityId) {
    $stravaActivity = $this->stravaAPI->get('activities/' . $stravaActivityId, array());
    $this->_handleErrors($stravaActivity);

    // activity type {{{
    $stravaType = $stravaActivity->type;
    if (key_exists($stravaType, ActivitiesConstants::$ACTIVITY_TYPE_BY_FILE_STRING)) {
      $type = ActivitiesConstants::$ACTIVITY_TYPE_BY_FILE_STRING[$stravaType];
    } else {
      $type = ActivitiesConstants::AT_OTHER;
    }
    // }}}

    // gear {{{
    $gearId = NULL;
    $stravaGearId = AuxiliaryMethods::getValueOfProperty($stravaActivity, 'gear_id', NULL);
    if (($stravaGearId !== NULL) && ($stravaGearId)) {
      $gearRow = DBMethods::getGearRowByESTypeESId($db, $userId, ExternalSourcesConstants::EST_STRAVA, $stravaGearId);
      if ($gearRow === NULL) {
        $stravaGear = $this->stravaAPI->get('gear/' . $stravaGearId, array());
        $this->_handleErrors($stravaGear);

        $gearId = DBMethods::registerGear($db,
          $userId,                                                                    // userId
          0,                                                                          // $primary
          AuxiliaryMethods::getValueOfProperty($stravaGear, 'name', ''),              // $name
          AuxiliaryMethods::getValueOfProperty($stravaGear, 'brand_name', ''),        // $brand
          AuxiliaryMethods::getValueOfProperty($stravaGear, 'model_name', ''),        // $model
          AuxiliaryMethods::getValueOfProperty($stravaGear, 'description', ''),       // $description
          GearConstants::GT_OTHER,                                                    // $type
          NULL,                                                                       // $weight
          ExternalSourcesConstants::EST_STRAVA,                                       // $esType
          $stravaGearId                                                               // $esId
        );
      } else {
        $gearId = $gearRow['id'];
      }
    }
    // }}}

    $startTime    = strtotime($stravaActivity->start_date);
    $title        = AuxiliaryMethods::getValueOfProperty($stravaActivity, 'name', '');
    $description  = AuxiliaryMethods::getValueOfProperty($stravaActivity, 'description', '');

    if (isset($stravaActivity->map->polyline)) {
      $polyline = $stravaActivity->map->polyline;
    } else {
      $polyline = NULL;
    }

    $activityStats = new ActivityStatsStorage();
    $activityStats->setData(
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'distance', NULL),              // $distance
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'moving_time', NULL),           // $movingTime
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'elapsed_time', NULL),          // $elapsedTime
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'total_elevation_gain', NULL),  // $elevationGain
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'start_latitude', NULL),        // $startLatitude
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'start_longitude', NULL),       // $startLongitude
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'location_city', NULL),         // $locationCity
      NULL,                                                                                 // $locationDistrict
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'location_country', NULL),      // $locationCountry
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'max_speed', NULL),             // $maxSpeed
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'average_temp', NULL),          // $averageTemperature
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'average_heartrate', NULL),     // $averageHeartrate
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'max_heartrate', NULL),         // $maxHeartrate
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'average_cadence', NULL),       // $averageCadence
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'average_watts', NULL),         // $averageWatts
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'max_watts', NULL),             // $maxWatts
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'calories', NULL),              // $kilocalories
      AuxiliaryMethods::getValueOfProperty($stravaActivity, 'device_name', NULL),           // $deviceName
      NULL,                                                                                 // $weatherTemperature
      NULL,                                                                                 // $weatherCode
      NULL,                                                                                 // $weatherWindSpeed
      NULL                                                                                  // $weatherWindDeg
    );

    /*echo '<pre>';
    print_r($stravaActivity);
    echo '</pre>';
    echo '<br />';
    $activityStats->print();
    exit();*/

    return array($type, $gearId, ExternalSourcesConstants::EST_STRAVA, $stravaActivityId,
                 $startTime, $title, $description, $activityStats, $polyline);
  }


  public function getPhotosByStravaId($stravaActivityId) {
    $stravaPhotos = $this->stravaAPI->get('activities/' . $stravaActivityId . '/photos', array(
      'photo_sources' => True,
      'size'          => 2000,
    ));
    $this->_handleErrors($stravaPhotos);

    $photos = array();
    foreach ($stravaPhotos as $stravaPhoto) {
      $location = AuxiliaryMethods::getValueOfProperty($stravaPhoto, 'location', NULL);
      if ($location === NULL) {
        $latitude   = NULL;
        $longitude  = NULL;
      } else {
        $latitude   = $location[0];
        $longitude  = $location[1];
      }

      $isDefault = AuxiliaryMethods::getValueOfProperty($stravaPhoto, 'default_photo', 0);
      if ($isDefault) $isDefault = 1; else $isDefault = 0;

      $takenTime = AuxiliaryMethods::getValueOfProperty($stravaPhoto, 'created_at', NULL);
      if ($takenTime) {
        $takenTime = strtotime($takenTime);
      }

      $photos[] = array(
        'url'       => AuxiliaryMethods::getValueOfProperty($stravaPhoto->urls, '2000', NULL),
        'latitude'  => $latitude,
        'longitude' => $longitude,
        'takenTime' => $takenTime,
        'isDefault' => $isDefault,
      );
    }

    /*
    echo '<pre>';
    print_r($stravaPhotos);
    echo '</pre>';
    echo '<pre>';
    print_r($photos);
    echo '</pre>';
    exit();
    */

    return $photos;
  }


  private function _handleErrors($stravaResponse) {
    if (is_array($stravaResponse)) {
      return;
    }

    if (property_exists($stravaResponse, 'errors')) {
      throw new ErrorInvalidResponse(t::m('ERROR_INVALID_RESPONSE') .
                                     ' (' . ExternalSourcesConstants::$EXTERNAL_SOURCE_NAMES[ExternalSourcesConstants::EST_STRAVA] . '): ' .
                                     print_r($stravaResponse, True));
    }
  }

}

?>