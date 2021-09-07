<?

class Handler_ACTIVITIES {

  public static function summaryHandler($pageRequest) {
    list($minYear, $maxYear) = DBMethods::getMinMaxActivityYears($pageRequest->db, $pageRequest->sessionData->userId);
    $years = AuxiliaryMethods::getYearsListFromMinMax($minYear, $maxYear);

    // handle year parameter {{{
    if (count($years) > 0) {
      $currentYear = end($years);
    } else {
      $currentYear = NULL;
    }
    if ((isset($_GET['year'])) && (is_numeric($_GET['year']))) {
      if (in_array($_GET['year'], $years)) {
        $currentYear = $_GET['year'];
      }
    }
    // }}}

    $activitiesRows = DBMethods::getActivitiesRows(
      $pageRequest->db,
      $pageRequest->sessionData->userId,
      mktime(0, 0, 0, 1, 1, $currentYear),
      mktime(0, 0, 0, 12, 31, $currentYear) + TimeConstants::SECONDS_IN_DAY
    );

    $tp = array();
    $tp['ACTIVITIES_ROWS']  = $activitiesRows;
    $tp['YEARS']            = $years;
    $tp['CURRENT_YEAR']     = $currentYear;

    if (count($years) == 0) {
      $page = new Page($pageRequest, array('pages/noData.php'), $tp, t::m('PAGE_ACTIVITIES_TITLE'));
    } else {
      $page = new Page($pageRequest, array('pages/summary.php'), $tp, t::m('PAGE_ACTIVITIES_TITLE'));
    }
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function activityHandler($pageRequest) {
    $activityRow = NULL;
    $polylineRow = NULL;

    if (isset($_GET['id'])) {
      if (is_numeric($_GET['id'])) {
        $activityId = $_GET['id'];
        $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $activityId);
      }
    } else {
      $activityRow = DBMethods::getLastActivityRow($pageRequest->db, $pageRequest->sessionData->userId);

      // no activity {{{
      if (!$activityRow) {
        AuxiliaryMethods::temporaryRedirect($pageRequest, './diary');
      }
      // }}}

      $activityId = $activityRow['id'];
    }

    if (($activityRow === NULL) || ($activityRow['userId'] != $pageRequest->sessionData->userId)) {
      return HandlersConstants::HS_NOT_FOUND;
    }

    // remove activity {{{
    if ((isset($_GET['remove'])) && (is_numeric($_GET['remove']))) {
      DBMethods::removeActivity($pageRequest->db, $pageRequest->sessionData->userId, $_GET['remove']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './activity');
    }
    // }}}

    $photosRows = DBMethods::getPhotosForActivity($pageRequest->db, $activityId);

    $polylineRow = DBMethods::getActivityPolylineRow($pageRequest->db, $activityId);
    if ($activityRow['gearId'] !== NULL) {
      $gearRow = DBMethods::getGearRow($pageRequest->db, $pageRequest->sessionData->userId, $activityRow['gearId']);
    } else {
      $gearRow = NULL;
    }

    // data for calendar {{{
    list($previousActivityRow, $nextActivityRow) = DBMethods::getPreviousAndNextActivities($pageRequest->db, $pageRequest->sessionData->userId, $activityRow['startTime']);

    $month  = date('n', $activityRow['startTime']);
    $year   = date('Y', $activityRow['startTime']);

    list($minYearA, $maxYearA) = DBMethods::getMinMaxActivityYears($pageRequest->db, $pageRequest->sessionData->userId);
    list($minYearD, $maxYearD) = DBMethods::getMinMaxDiaryYears($pageRequest->db, $pageRequest->sessionData->userId);
    $years = AuxiliaryMethods::getYearsListFromMinMax(min($minYearA, $minYearD), max($maxYearA, $maxYearD));

    $monthActivities = DBMethods::getMonthActivitiesByMonthYear($pageRequest->db, $pageRequest->sessionData->userId, $month, $year);
    $monthDiaryData  = DBMethods::getDiaryDataByMonthYear($pageRequest->db, $pageRequest->sessionData->userId, $month, $year);
    // }}}

    $tp = array();
    $tp['ACTIVITY_ROW']     = $activityRow;
    $tp['GEAR_ROW']         = $gearRow;
    $tp['PHOTOS_ROWS']      = $photosRows;
    $tp['MONTH_ACTIVITIES'] = $monthActivities;
    $tp['MONTH_DIARY_DATA'] = $monthDiaryData;
    $tp['MONTH']            = $month;
    $tp['YEAR']             = $year;
    $tp['YEARS']            = $years;
    $tp['CURRENT_MONTH']    = date('n');
    $tp['CURRENT_YEAR']     = date('Y');
    $tp['PREVIOUS_ACTIVITY']= $previousActivityRow;
    $tp['NEXT_ACTIVITY']    = $nextActivityRow;

    if (($activityRow['startLatitude'] !== NULL) && ($activityRow['startLongitude'] !== NULL)) {
      $tp['START_POSITION'] = array($activityRow['startLatitude'], $activityRow['startLongitude']);
    } else {
      $tp['START_POSITION'] = NULL;
    }

    if ($polylineRow !== NULL) {
      $tp['COORDINTES']     = self::_pairPoints(self::_decodePolyline($polylineRow['polyline']));
      if ($tp['START_POSITION'] === NULL) {
        $tp['START_POSITION'] = $tp['COORDINTES'][0];
      }
    } else {
      $tp['COORDINTES']     = NULL;
    }



    $page = new Page($pageRequest, array('pages/activity.php'), $tp, t::m('PAGE_ACTIVITY_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function addOrEditActivityHandler($pageRequest) {
    $activityRow = NULL;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && ($_GET['id'] != -1)) {
      $activityId = $_GET['id'];
      $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $activityId);
      if ($activityRow === NULL) return HandlersConstants::HS_NOT_FOUND;
    }

    // add activity {{{
    if ($activityRow === NULL) {
      $actionAdd              = True;
      $submitButtonTitle      = t::m('LABEL_CREATE');
      $pageHeader             = t::m('PAGE_ADD_ACTIVITY_TITLE');
      $canBeRemoved           = False;

      $fActivityId            = -1;
      $fTitle                 = '';
      $fType                  = ActivitiesConstants::DEFAULT_ACTIVITY_TYPE;
      $fGearId                = Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX'];
      $fDescription           = '';
      $fStartTime             = TextFormattingMethods::formatDateTime(time());
      $fEsType                = ExternalSourcesConstants::EST_NONE;
      $fEsId                  = '';
      // stats {{{
      $fDistance              = '';
      $fMovingTime            = '';
      $fElapsedTime           = '';
      $fElevationGain         = '';
      $fStartLatitudeLongitude= '';
      $fLocationCity          = '';
      $fLocationDistrict      = '';
      $fLocationCountry       = '';
      $fMaxSpeed              = '';
      $fAverageTemperature    = '';
      $fAverageHeartrate      = '';
      $fMaxHeartrate          = '';
      $fAverageCadence        = '';
      $fAverageWatts          = '';
      $fMaxWatts              = '';
      $fKilocalories          = '';
      $fDeviceName            = '';
      $fWeatherTemperature    = '';
      $fWeatherCode           = '';
      $fWeatherWindSpeed      = '';
      $fWeatherWindDeg        = '';
      // }}}
    // }}}
    // edit activity {{{
    } else {
      $actionAdd              = False;
      $submitButtonTitle      = t::m('LABEL_SAVE');
      $pageHeader             = t::m('PAGE_EDIT_ACTIVITY_TITLE');
      $canBeRemoved           = DBMethods::canBeActivityRemoved($pageRequest->db, $pageRequest->sessionData->userId, $activityRow['id']);

      $fActivityId            = $activityRow['id'];
      $fTitle                 = $activityRow['title'];
      $fType                  = $activityRow['type'];
      $fGearId                = $activityRow['gearId'];
      $fDescription           = $activityRow['description'];
      $fStartTime             = TextFormattingMethods::formatDateTime($activityRow['startTime']);
      $fEsType                = $activityRow['esType'];
      $fEsId                  = $activityRow['esId'];
      // stats {{{
      $fDistance              = TextFormattingMethods::formatDistance($activityRow['distance'], False);
      $fMovingTime            = TextFormattingMethods::secondsToFromattedTimeUnits($activityRow['movingTime']);
      $fElapsedTime           = TextFormattingMethods::secondsToFromattedTimeUnits($activityRow['elapsedTime']);
      $fElevationGain         = TextFormattingMethods::formatElevationGain($activityRow['elevationGain'], False);
      if (($activityRow['startLatitude']) && ($activityRow['startLongitude'])) {
        $fStartLatitudeLongitude = $activityRow['startLatitude'] . ', ' . $activityRow['startLongitude'];
      } else {
        $fStartLatitudeLongitude = '';
      }
      $fLocationCity          = $activityRow['locationCity'];
      $fLocationDistrict      = $activityRow['locationDistrict'];
      $fLocationCountry       = $activityRow['locationCountry'];
      $fMaxSpeed              = TextFormattingMethods::formatSpeed($activityRow['maxSpeed'], False);
      $fAverageTemperature    = TextFormattingMethods::formatNumber($activityRow['averageTemperature']);
      $fAverageHeartrate      = TextFormattingMethods::formatNumber($activityRow['averageHeartrate']);
      $fMaxHeartrate          = $activityRow['maxHeartrate'];
      $fAverageCadence        = TextFormattingMethods::formatNumber($activityRow['averageCadence']);
      $fAverageWatts          = TextFormattingMethods::formatNumber($activityRow['averageWatts']);
      $fMaxWatts              = TextFormattingMethods::formatNumber($activityRow['maxWatts']);
      $fKilocalories          = TextFormattingMethods::formatNumber($activityRow['kilocalories']);
      $fDeviceName            = $activityRow['deviceName'];
      $fWeatherTemperature    = $activityRow['weatherTemperature'];
      $fWeatherCode           = $activityRow['weatherCode'];
      $fWeatherWindSpeed      = $activityRow['weatherWindSpeed'];
      $fWeatherWindDeg        = $activityRow['weatherWindDeg'];
      // }}}
    }
    // }}}

    $form = new Form('fEditActivity', './activity-ae?id=' . $fActivityId, $submitButtonTitle, array(
      new Field_String(
        'fTitle',                                                                                       // $name
        t::m('PAGE_EDIT_TITLE_GENERATED_IF_EMPTY'),                                                     // $label
        False,                                                                                          // $isRequired
        $fTitle                                                                                         // $initialValue
      ),
      new Field_Select(
        'fType',                                                                                        // $name
        t::m('PAGE_ACTIVITY_TYPE'),                                                                     // $label
        True,                                                                                           // $isRequired
        $fType,                                                                                         // $initialValue
        FormMethods::getActivityTypeItems()                                                             // $optionsDict
      ),
      new Field_Select(
        'fGearId',                                                                                      // $name
        t::m('PAGE_ACTIVITY_GEAR_NAME'),                                                                // $label
        False,                                                                                          // $isRequired
        $fGearId,                                                                                       // $initialValue
        DBMethods::getGearDictForSelect($pageRequest->db, $pageRequest->sessionData->userId)            // $optionsDict
      ),
      new Field_String(
        'fDescription',                                                                                 // $name
        t::m('PAGE_EDIT_ACTIVITY_DESCRIPTION'),                                                         // $label
        False,                                                                                          // $isRequired
        $fDescription                                                                                   // $initialValue
      ),
      new Field_DateTime(
        'fStartTime',                                                                                   // $name
        t::m('PAGE_EDIT_ACTIVITY_START_TIME'),                                                          // $label
        True,                                                                                           // $isRequired
        $fStartTime                                                                                     // $initialValue
      ),
      // stats {{{
      new Field_Float(
        'fDistance',                                                                                    // $name
        t::m('LABEL_DISTANCE') . ' [' . t::m('LABEL_UNIT_DISTANECE') . ']',                             // $label
        False,                                                                                          // $isRequired
        $fDistance                                                                                      // $initialValue
      ),

      new Field_String(
        'fMovingTime',                                                                                  // $name
        t::m('LABEL_TIME'),                                                                             // $label
        False,                                                                                          // $isRequired
        $fMovingTime                                                                                    // $initialValue
      ),
      new Field_String(
        'fElapsedTime',                                                                                 // $name
        t::m('LABEL_TOTAL_TIME'),                                                                       // $label
        False,                                                                                          // $isRequired
        $fElapsedTime                                                                                   // $initialValue
      ),
      new Field_Float(
        'fElevationGain',                                                                               // $name
        t::m('PAGE_ACTIVITY_ELEVATION_GAIN') . ' [' . t::m('LABEL_UNIT_ELEVATION_GAIN') . ']',          // $label
        False,                                                                                          // $isRequired
        $fElevationGain                                                                                 // $initialValue
      ),
      new Field_String(
        'fStartLatitudeLongitude',                                                                      // $name
        t::m('PAGE_EDIT_ACTIVITY_LATITUDE_LONGITUDE'),                                                  // $label
        False,                                                                                          // $isRequired
        $fStartLatitudeLongitude                                                                        // $initialValue
      ),
      new Field_String(
        'fLocationCity',                                                                                // $name
        t::m('PAGE_EDIT_ACTIVITY_LOCATION_CITY'),                                                       // $label
        False,                                                                                          // $isRequired
        $fLocationCity                                                                                  // $initialValue
      ),
      new Field_String(
        'fLocationDistrict',                                                                            // $name
        t::m('PAGE_EDIT_ACTIVITY_LOCATION_DISTRICT'),                                                   // $label
        False,                                                                                          // $isRequired
        $fLocationDistrict                                                                              // $initialValue
      ),
      new Field_String(
        'fLocationCountry',                                                                             // $name
        t::m('PAGE_EDIT_ACTIVITY_LOCATION_COUNTRY'),                                                    // $label
        False,                                                                                          // $isRequired
        $fLocationCountry                                                                               // $initialValue
      ),
      new Field_Float(
        'fMaxSpeed',                                                                                    // $name
        t::m('PAGE_ACTIVITY_MAX_SPEED') . ' [' . t::m('LABEL_UNIT_SPEED') . ']',                        // $label
        False,                                                                                          // $isRequired
        $fMaxSpeed                                                                                      // $initialValue
      ),
      new Field_Float(
        'fAverageTemperature',                                                                          // $name
        t::m('PAGE_EDIT_ACTIVITY_AVERAGE_TEMPERATURE') . ' [' . t::m('LABEL_UNIT_CELSIUS_DEGREE') . ']', // $label
        False,                                                                                          // $isRequired
        $fAverageTemperature                                                                            // $initialValue
      ),
      new Field_Float(
        'fAverageHeartrate',                                                                            // $name
        t::m('PAGE_EDIT_ACTIVITY_AVERAGE_HEARTRATE') . ' [' . t::m('LABEL_UNIT_HEART_RATE_IN_MINUTE') . ']', // $label
        False,                                                                                          // $isRequired
        $fAverageHeartrate                                                                              // $initialValue
      ),
      new Field_Integer(
        'fMaxHeartrate',                                                                                // $name
        t::m('PAGE_EDIT_ACTIVITY_MAX_HEARTRATE') . ' [' . t::m('LABEL_UNIT_HEART_RATE_IN_MINUTE') . ']',   // $label
        False,                                                                                          // $isRequired
        $fMaxHeartrate                                                                                  // $initialValue
      ),
      new Field_Float(
        'fAverageCadence',                                                                              // $name
        t::m('PAGE_EDIT_ACTIVITY_AVERAGE_CADENCE') . ' [' . t::m('LABEL_UNIT_HEART_RATE_IN_MINUTE') . ']', // $label
        False,                                                                                          // $isRequired
        $fAverageCadence                                                                                // $initialValue
      ),
      new Field_Float(
        'fAverageWatts',                                                                                // $name
        t::m('PAGE_EDIT_ACTIVITY_AVERAGE_WATTS') . ' [' . t::m('LABEL_UNIT_WATTS') . ']',               // $label
        False,                                                                                          // $isRequired
        $fAverageWatts                                                                                  // $initialValue
      ),
      new Field_Float(
        'fMaxWatts',                                                                                    // $name
        t::m('PAGE_EDIT_ACTIVITY_MAX_WATTS') . ' [' . t::m('LABEL_UNIT_WATTS') . ']',                   // $label
        False,                                                                                          // $isRequired
        $fMaxWatts                                                                                      // $initialValue
      ),
      new Field_Float(
        'fKilocalories',                                                                                // $name
        t::m('PAGE_EDIT_ACTIVITY_KILOCALORIES') . ' [' . t::m('LABEL_UNIT_KILOCAL') . ']',              // $label
        False,                                                                                          // $isRequired
        $fKilocalories                                                                                  // $initialValue
      ),
      new Field_String(
        'fDeviceName',                                                                                  // $name
        t::m('PAGE_ACTIVITY_DEVICE_NAME'),                                                              // $label
        False,                                                                                          // $isRequired
        $fDeviceName                                                                                    // $initialValue
      ),
      new Field_Float(
        'fWeatherTemperature',                                                                          // $name
        t::m('PAGE_EDIT_ACTIVITY_WEATHER_TEMPERATURE') . ' [' . t::m('LABEL_UNIT_CELSIUS_DEGREE') . ']',  // $label
        False,                                                                                          // $isRequired
        $fWeatherTemperature,                                                                           // $initialValue
        -100,                                                                                           // $minValue
        100                                                                                             // $maxValue
      ),
      new Field_Select(
        'fWeatherCode',                                                                                 // $name
        t::m('PAGE_EDIT_ACTIVITY_WEATHER_CODE'),                                                        // $label
        False,                                                                                          // $isRequired
        $fWeatherCode,                                                                                  // $initialValue
        FormMethods::getWeatherCodesItems()                                                             // $optionsDict
      ),
      new Field_Float(
        'fWeatherWindSpeed',                                                                            // $name
        t::m('PAGE_EDIT_ACTIVITY_WEATHER_WIND_SPEED') . ' [' . t::m('LABEL_UNIT_METERS_PER_SECOND') . ']', // $label
        False,                                                                                          // $isRequired
        $fWeatherWindSpeed                                                                              // $initialValue
      ),
      new Field_Integer(
        'fWeatherWindDeg',                                                                              // $name
        t::m('PAGE_EDIT_ACTIVITY_WEATHER_WIND_DEG') . ' [' . t::m('LABEL_UNIT_DEG') . ']',              // $label
        False,                                                                                          // $isRequired
        $fWeatherWindDeg                                                                                // $initialValue
      ),
      // }}}
      new Field_Select(
        'fEsType',                                                                                      // $name
        t::m('PAGE_GEAR_ES_TYPE'),                                                                      // $label
        False,                                                                                          // $isRequired
        $fEsType,                                                                                       // $initialValue
        FormMethods::getESTypeItems()                                                                   // $optionsDict
      ),
      new Field_String(
        'fEsId',                                                                                       // $name
        t::m('PAGE_GEAR_ES_ID'),                                                                       // $label
        False,                                                                                         // $isRequired
        $fEsId                                                                                         // $initialValue
      ),
      new Field_HiddenId(
        'fActivityId',                                                                                  // $name
        $fActivityId                                                                                    // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();

      if (count($form->errorMessagesByField) == 0) {
        $activityStats = new ActivityStatsStorage();
        $activityId = $form->getFieldValue('fActivityId');

        $fGearId                            = $form->getFieldValue('fGearId');
        if (!is_numeric($fGearId)) $fGearId = NULL;
        $fType                              = $form->getFieldValue('fType');
        $fTitle                             = $form->getFieldValue('fTitle');
        if ($fTitle == '') $fTitle          = t::m('ACTIVITY_NAME__' . ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$fType]);
        $fDescription                       = $form->getFieldValue('fDescription');
        $fStartTime                         = strtotime($form->getFieldValue('fStartTime'));
        $fEsType                            = $form->getFieldValue('fEsType');
        $fEsId                              = $form->getFieldValue('fEsId');

        if (Settings::$S['METRIC_UNITS']) {
          $activityStats->distance          = ConversionMethods::kilometersToMeters($form->getFieldValueOrNULLIfEmpty('fDistance'));
          $activityStats->elevationGain     = $form->getFieldValueOrNULLIfEmpty('fElevationGain');
          $activityStats->maxSpeed          = ConversionMethods::kilometersPerHourToMetersPerSecond($form->getFieldValueOrNULLIfEmpty('fMaxSpeed'));
        } else {
          $activityStats->distance          = ConversionMethods::milesToMeters($form->getFieldValueOrNULLIfEmpty('fDistance'));
          $activityStats->elevationGain     = ConversionMethods::feetsToMeters($form->getFieldValueOrNULLIfEmpty('fElevationGain'));
          $activityStats->maxSpeed          = ConversionMethods::milesPerHourToMetersPerSecond($form->getFieldValueOrNULLIfEmpty('fMaxSpeed'));
        }

        $fMovingTime                        = $form->getFieldValueOrNULLIfEmpty('fMovingTime');
        if ($fMovingTime === NULL) {
          $activityStats->movingTime        = NULL;
        } else {
          $activityStats->movingTime        = ConversionMethods::timeUnitsStringToSeconds($fMovingTime);
        }

        $fElapsedTime                       = $form->getFieldValueOrNULLIfEmpty('fElapsedTime');
        if ($fElapsedTime === NULL) {
          if ($fMovingTime !== NULL) {
            $activityStats->elapsedTime     = $activityStats->movingTime;
          } else {
            $activityStats->elapsedTime     = NULL;
          }
        } else {
          $activityStats->elapsedTime       = ConversionMethods::timeUnitsStringToSeconds($fElapsedTime);
        }

        list($latitude, $longitude) = ConversionMethods::stringToLatitudeLongintude($form->getFieldValueOrNULLIfEmpty('fStartLatitudeLongitude'));
        $activityStats->startLatitude       = $latitude;
        $activityStats->startLongitude      = $longitude;

        $activityStats->locationCity        = $form->getFieldValueOrNULLIfEmpty('fLocationCity');
        $activityStats->locationDistrict    = $form->getFieldValueOrNULLIfEmpty('fLocationDistrict');
        $activityStats->locationCountry     = $form->getFieldValueOrNULLIfEmpty('fLocationCountry');
        $activityStats->averageTemperature  = $form->getFieldValueOrNULLIfEmpty('fAverageTemperature');
        $activityStats->averageHeartrate    = $form->getFieldValueOrNULLIfEmpty('fAverageHeartrate');
        $activityStats->maxHeartrate        = $form->getFieldValueOrNULLIfEmpty('fMaxHeartrate');
        $activityStats->averageCadence      = $form->getFieldValueOrNULLIfEmpty('fAverageCadence');
        $activityStats->averageWatts        = $form->getFieldValueOrNULLIfEmpty('fAverageWatts');
        $activityStats->maxWatts            = $form->getFieldValueOrNULLIfEmpty('fMaxWatts');
        $activityStats->kilocalories        = $form->getFieldValueOrNULLIfEmpty('fKilocalories');
        $activityStats->deviceName          = $form->getFieldValueOrNULLIfEmpty('fDeviceName');
        $activityStats->weatherTemperature  = $form->getFieldValueOrNULLIfEmpty('fWeatherTemperature');
        $activityStats->weatherCode         = $form->getFieldValueOrNULLIfEmpty('fWeatherCode');
        $activityStats->weatherWindSpeed    = $form->getFieldValueOrNULLIfEmpty('fWeatherWindSpeed');
        $activityStats->weatherWindDeg      = $form->getFieldValueOrNULLIfEmpty('fWeatherWindDeg');

        // determine location if needed {{{
        if ((Settings::$S['USE_LOCATION_SERVICE_TO_FILL_DATA']) && ($activityStats->locationCity === NULL) &&
            ($activityStats->locationDistrict === NULL) && ($activityStats->locationCountry === NULL)) {
          try {
            $activityStats->setLocationDetailsByCoordinates();
          } catch (Exception $e) {
            $pageRequest->sessionData->addErrorMessage($e->getMessage());
          }
        }
        // }}}

        if ($activityId == -1) {
          $activityId = DBMethods::registerActivity(
            $pageRequest->db,                     // $db
            $pageRequest->sessionData->userId,    // $userId
            $fGearId,                             // gearId
            $fType,                               // type
            $fTitle,                              // $title
            $fDescription,                        // $description
            $fStartTime,                          // $startTime
            '',                                   // $gpxFileName
            $fEsType,                             // $esType
            $fEsId,                               // $esId
            $activityStats,                       // $activityStats
            NULL                                  // $polyline
          );
        } else {
          DBMethods::updateActivity(
            $pageRequest->db,                     // $db
            $pageRequest->sessionData->userId,    // $userId
            $activityId,                          // $activityId
            $fGearId,                             // gearId
            $fType,                               // type
            $fTitle,                              // $title
            $fDescription,                        // $description
            $fStartTime,                          // $startTime
            '',                                   // $gpxFileName
            $fEsType,                             // $esType
            $fEsId,                               // $esId
            $activityStats                        // $activityStats
          );
        }

        AuxiliaryMethods::temporaryRedirect($pageRequest, './activity?id=' . $activityId);
      }
    }
    // }}}

    $tp = array();
    $tp['FORM']           = $form;
    $tp['PAGE_HEADER']    = $pageHeader;
    $tp['ACTIVITY_ID']    = $fActivityId;
    $tp['CAN_BE_REMOVED'] = $canBeRemoved;

    $page = new Page($pageRequest, array('pages/addOrEditActivity.php'), $tp, $pageHeader);
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function fetchActivityLocationHandler($pageRequest) {
    $activityRow = NULL;

    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
      $activityId = $_GET['id'];
      $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $activityId);
    }

    if (($activityRow === NULL) || ($activityRow['userId'] != $pageRequest->sessionData->userId)) {
      return HandlersConstants::HS_NOT_FOUND;
    }

    try {
      $activityStats = new ActivityStatsStorage();
      $activityStats->setDataFromDBRow($activityRow);
      $activityStats->setLocationDetailsByCoordinates();

      //$activityStats->setWeatherDataFromDescription($activityRow['description']);

      DBMethods::updateActivityStats($pageRequest->db, $pageRequest->sessionData->userId, $activityId, $activityStats);

      $pageRequest->sessionData->addInfoMessage(t::m('PAGE_ACTIVITY_LOCATION_HAS_BEEN_SET'));
      AuxiliaryMethods::temporaryRedirect($pageRequest, './activity?id=' . $activityId);
    } catch (Exception $e) {
      $pageRequest->sessionData->addErrorMessage($e->getMessage());
    }


    $tp = array();
    $tp['RESULT_TEXT'] = t::m('ERROR_UNSUCCESSFUL_LOCATION_DATA_FETCH');

    $page = new Page($pageRequest, array('pages/resultError.php'), $tp, t::m('LABEL_ERROR'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function fetchActivityPhotosFromStravaHandler($pageRequest) {
    $activityRow = NULL;

    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
      $activityId = $_GET['id'];
      $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $activityId);
    }

    if (($activityRow === NULL) || ($activityRow['userId'] != $pageRequest->sessionData->userId)) {
      return HandlersConstants::HS_NOT_FOUND;
    }

    try {
      $stravaConnector = new StravaConnector();
      if (!$stravaConnector->tokenIsSet) {
        $pageRequest->sessionData->setCustomData('synchronize-strava-auth-back-uri', './fetch-activity-photos-from-strava?id=' . $activityId);
        AuxiliaryMethods::temporaryRedirect($pageRequest, './synchronize-strava-auth');
      }

      $stravaPhotos = $stravaConnector->getPhotosByStravaId($activityRow['esId']);

      if (count($stravaPhotos) > 0) {
        PhotosMethods::downloadAndRegisterStravaPhotos($stravaPhotos, $pageRequest, $activityRow);
      }

      $pageRequest->sessionData->addInfoMessage(t::m('PAGE_ACTIVITY_PHOTOS_HAVE_BEEN_FETCHED'));
      AuxiliaryMethods::temporaryRedirect($pageRequest, './activity?id=' . $activityId);
    } catch (Exception $e) {
      $pageRequest->sessionData->addErrorMessage($e->getMessage());
    }

    $tp = array();
    $tp['RESULT_TEXT'] = t::m('ERROR_UNSUCCESSFUL_PHOTOS_FETCH');

    $page = new Page($pageRequest, array('pages/resultError.php'), $tp, t::m('LABEL_ERROR'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function synchronizeStravaHandler($pageRequest) {
    $errorMessagesByField     = array();
    $registeredActivitiesRows = array();
    $synchronizationDone      = FALSE;
    $skippedCount             = 0;
    $registeredCount          = 0;
    $registerdActivitiesIds   = array();

    // handle form submit {{{
    if (isset($_POST['fLoadSend'])) {
      $fTimeFrom = AuxiliaryMethods::getValueOrDefault($_POST['fTimeFrom'], '');
      $fTimeTo   = AuxiliaryMethods::getValueOrDefault($_POST['fTimeTo'], '');

      // validate data {{{
      if ($fTimeFrom == '') {
        FormMethods::addFormErrorMessage($errorMessagesByField, 'fTimeFrom', t::m('FORM_FIELD_IS_REQUIRED'));
      }
      if (($fTimeFrom != '') && ((strtotime($fTimeFrom) === FALSE))) {
        FormMethods::addFormErrorMessage($errorMessagesByField, 'fTimeFrom', t::m('FORM_FIELD_BAD_FORMAT'));
      }
      if ($fTimeTo == '') {
        FormMethods::addFormErrorMessage($errorMessagesByField, 'fTimeTo', t::m('FORM_FIELD_IS_REQUIRED'));
      }
      if (($fTimeTo != '') && ((strtotime($fTimeTo) === FALSE))) {
        FormMethods::addFormErrorMessage($errorMessagesByField, 'fTimeTo', t::m('FORM_FIELD_BAD_FORMAT'));
      }
      // }}}

      if (count($errorMessagesByField) == 0) {
        $timeFrom = strtotime($fTimeFrom);
        $timeTo   = strtotime($fTimeTo) + TimeConstants::SECONDS_IN_DAY;

        $stravaConnector = new StravaConnector();
        if (!$stravaConnector->tokenIsSet) {
          $pageRequest->sessionData->setCustomData('synchronize-strava-auth-back-uri', './synchronize-strava');
          AuxiliaryMethods::temporaryRedirect($pageRequest, './synchronize-strava-auth');
        }

        try {
          $stravaActivityIds = $stravaConnector->getActivityIdsFromTime($timeFrom, $timeTo);
          $esIds             = DBMethods::getESIdsFromTime(
            $pageRequest->db,
            $pageRequest->sessionData->userId,
            ExternalSourcesConstants::EST_STRAVA,
            $timeFrom,
            $timeTo
          );

          foreach ($stravaActivityIds as $stravaActivityId) {
            if (in_array($stravaActivityId, $esIds)) {
              $skippedCount ++;
              continue;
            }

            list($type, $gearId, $esType, $esId, $startTime, $title, $description, $activityStats, $polyline)
              = $stravaConnector->getActivityDataByStravaId($pageRequest->db, $pageRequest->sessionData->userId, $stravaActivityId);

            //$stravaPhotos = $stravaConnector->getPhotosByStravaId($stravaActivityId);

            if (Settings::$S['USE_LOCATION_SERVICE_TO_FILL_DATA']) {
              try {
                $activityStats->setLocationDetailsByCoordinates();
              } catch (Exception $e) {
                $pageRequest->sessionData->addErrorMessage($e->getMessage());
              }
            }

            if (Settings::$S['GET_WEATHER_DATA_FROM_DESCRIPTION']) {
              $activityStats->setWeatherDataFromDescription($description);
            }

            $activityId = DBMethods::registerActivity(
              $pageRequest->db,                     // $db
              $pageRequest->sessionData->userId,    // $userId
              $gearId,                              // gearId
              $type,                                // type
              $title,                               // $title
              $description,                         // $description
              $startTime,                           // $startTime
              '',                                   // $gpxFileName
              $esType,                              // $esType
              $esId,                                // $esId
              $activityStats,                       // $activityStats
              $polyline                             // $polyline
            );

            $registerdActivitiesIds[] = $activityId;
            $registeredCount ++;

            $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $activityId);
            $stravaPhotos = $stravaConnector->getPhotosByStravaId($esId);
            if (count($stravaPhotos) > 0) {
              PhotosMethods::downloadAndRegisterStravaPhotos($stravaPhotos, $pageRequest, $activityRow);
            }
          }

          $pageRequest->sessionData->setCustomData('synchronize-strava', array($fTimeFrom, $fTimeTo, $registeredCount, $skippedCount, $registerdActivitiesIds));
          AuxiliaryMethods::temporaryRedirect($pageRequest, './synchronize-strava');
        } catch (ErrorTooManyActivities $e) {
          $pageRequest->sessionData->addErrorMessage($e->getMessage());
        }
      }
    }
    // }}}
    // determine values of time for the form {{{
    else {
      // try to get time values from the session
      $synchornizationResultingData = $pageRequest->sessionData->popCustomData('synchronize-strava');
      if ($synchornizationResultingData !== NULL) {
        $synchronizationDone = TRUE;
        list($fTimeFrom, $fTimeTo, $registeredCount, $skippedCount, $registerdActivitiesIds) = $synchornizationResultingData;
      } else {
        $registerdActivitiesIds = array();
        $activityRow = DBMethods::getLastActivityRow($pageRequest->db, $pageRequest->sessionData->userId);
        $fTimeFrom  = TextFormattingMethods::formatDate($activityRow['startTime']);
        $fTimeTo    = TextFormattingMethods::formatDate(time());
      }
    }

    if (count($registerdActivitiesIds) > 0) {
      $registeredActivitiesRows = DBMethods::getActivitiesRowsByIds($pageRequest->db, $pageRequest->sessionData->userId, $registerdActivitiesIds);
    }

    $tp = array();
    $tp['SYNCHRONIZATION_DONE']       = $synchronizationDone;
    $tp['VALUE_BY_FIELD']             = array(
      'fTimeFrom'                     => $fTimeFrom,
      'fTimeTo'                       => $fTimeTo,
    );
    $tp['REGISTERED_COUNT']           = $registeredCount;
    $tp['SKIPPED_COUNT']              = $skippedCount;
    $tp['REGISTERED_ACTIVITIES_ROWS'] = $registeredActivitiesRows;
    $tp['ERROR_MESSAGES_BY_FIELD']    = $errorMessagesByField;


    $page = new Page($pageRequest, array('pages/synchronizeStrava.php'), $tp, t::m('PAGE_SYNCHRONIZE_STRAVA_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function synchronizeStravaAuthHandler($pageRequest) {

    $stravaConnector = new StravaConnector();

    if (isset($_GET['action']) && ($_GET['action'] == 'refresh')) {
      if ($stravaConnector->tokenIsSet) {
        $stravaConnector->refreshToken();
        $backURI = $pageRequest->sessionData->popCustomData('synchronize-strava-auth-back-uri', './synchronize-strava');
        AuxiliaryMethods::temporaryRedirect($pageRequest, $backURI);
      } else {
        $stravaConnector->authorize();
      }
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'deauthorize')) {
      $stravaConnector->deauthorize();
    }

    if (isset($_GET['code'])) {
      $stravaConnector->tokenExchange($_GET['code']);
      $backURI = $pageRequest->sessionData->popCustomData('synchronize-strava-auth-back-uri', './synchronize-strava');
      AuxiliaryMethods::temporaryRedirect($pageRequest, $backURI);
    }

    if ($stravaConnector->tokenIsSet) {
      $backURI = $pageRequest->sessionData->popCustomData('synchronize-strava-auth-back-uri', './synchronize-strava');
      AuxiliaryMethods::temporaryRedirect($pageRequest, $backURI);
    } else {
      $stravaConnector->authorize();
    }

    return HandlersConstants::HS_OK;
  }


  public static function importCSVHandler($pageRequest) {
    $registeredActivitiesRows = array();

    // handle activities CSV file {{{
    if ((isset($_POST['fUpload'])) && (isset($_FILES['fActivitiesCSVFile']))) {
      if ($_FILES["fActivitiesCSVFile"]["error"] > 0) {
        $pageRequest->sessionData->addErrorMessage(t::m('PAGE_IMPORT_CSV_ACTIVITIES_ERROR_UPLOAD') . ' (' . t::m('FILE_UPLOAD_ERRORS')[$_FILES["fActivitiesCSVFile"]["error"]] . ')');
      } else {
        $csvAsArray = AuxiliaryMethods::csvToArray($_FILES['fActivitiesCSVFile']['tmp_name']);
        if ($csvAsArray === FALSE) {
          $pageRequest->sessionData->addErrorMessage(t::m('PAGE_IMPORT_CSV_ACTIVITIES_ERROR_FORMAT'));
        } else {
          $skippedCSVRowsMessages = array();
          $csvDataToRegister      = array();
          $defaultMonth           = NULL;
          $defaultYear            = NULL;
          $defaultHour            = NULL;
          $defaultMinute          = NULL;
          foreach ($csvAsArray as $rowIndex => $activityCSVRow) {
            $activityStats = new ActivityStatsStorage();
            $fGearId = NULL;

            $formatErrorFields = array();

            if (is_numeric($activityCSVRow['gearId'])) {
              if (DBMethods::getGearRow($pageRequest->db, $pageRequest->sessionData->userId, $activityCSVRow['gearId']) !== NULL) {
                $fGearId = $activityCSVRow['gearId'];
              } else {
                $formatErrorFields[] = 'gearId';
              }
            }

            $fType = array_search($activityCSVRow['type'], ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES);
            if ($fType === FALSE) {
              $formatErrorFields[] = 'type';
              $fType = ActivitiesConstants::AT_OTHER;
            }

            $fTitle                             = $activityCSVRow['title'];
            if ($fTitle == '') $fTitle          = t::m('ACTIVITY_NAME__' . ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$fType]);

            $fDescription                       = $activityCSVRow['description'];

            $fStartTime                         = strtotime($activityCSVRow['startTime']);
            if ($fStartTime === FALSE) {
              // try to add default values {{{
              if (is_numeric($activityCSVRow['startTime'])) {
                $startTimeStr = $activityCSVRow['startTime'] . '.' . $defaultMonth . '.'. $defaultYear . ' ' . $defaultHour . ':' . $defaultMinute;
                $fStartTime = strtotime($startTimeStr);
                if ($fStartTime === FALSE) {
                  $formatErrorFields[] = 'startTime';
                }
              // }}}
              } else {
                $formatErrorFields[] = 'startTime';
              }
            } else {
              $defaultMonth   = date('n', $fStartTime);
              $defaultYear    = date('Y', $fStartTime);
              $defaultHour    = date('H', $fStartTime);
              $defaultMinute  = date('i', $fStartTime);
            }

            if ($activityCSVRow['distance'] != '') {
              $activityCSVRow['distance'] = str_replace(',', '.', $activityCSVRow['distance']);
              if (is_numeric($activityCSVRow['distance'])) {
                if (Settings::$S['METRIC_UNITS']) {
                  $activityStats->distance = ConversionMethods::kilometersToMeters($activityCSVRow['distance']);
                } else {
                  $activityStats->distance = ConversionMethods::milesToMeters($activityCSVRow['distance']);
                }
              } else {
                $formatErrorFields[] = 'distance';
              }
            }

            if ($activityCSVRow['movingTime'] != '') {
              $activityCSVRow['movingTime'] = str_replace('+', ':', $activityCSVRow['movingTime']);
              $activityStats->movingTime = ConversionMethods::timeUnitsStringToSeconds($activityCSVRow['movingTime']);
              if ($activityStats->movingTime === NULL) {
                $formatErrorFields[] = 'movingTime';
              }
            }

            if ($activityCSVRow['elapsedTime'] == '') {
              if ($activityStats->movingTime !== NULL) {
                $activityStats->elapsedTime = $activityStats->movingTime;
              } else {
                $activityStats->elapsedTime = NULL;
              }
            } else {
              $activityStats->elapsedTime = ConversionMethods::timeUnitsStringToSeconds($activityCSVRow['elapsedTime']);
              if ($activityStats->elapsedTime === NULL) {
                $formatErrorFields[] = 'elapsedTime';
              }
            }

            if ($activityCSVRow['elevationGain'] != '') {
              if (is_numeric($activityCSVRow['elevationGain'])) {
                $activityStats->elevationGain = round($activityCSVRow['elevationGain']);
              } else {
                $formatErrorFields[] = 'elevationGain';
              }
            }

            if ($activityCSVRow['startLatitudeLongitude'] != '') {
              list($latitude, $longitude) = ConversionMethods::stringToLatitudeLongintude($activityCSVRow['startLatitudeLongitude']);
              $activityStats->startLatitude     = $latitude;
              $activityStats->startLongitude    = $longitude;
              if ($activityStats->startLatitude === NULL) {
                $formatErrorFields[] = 'startLatitudeLongitude';
              }
            }

            if ($activityCSVRow['averageHeartrate'] != '') {
              $activityCSVRow['averageHeartrate'] = str_replace(',', '.', $activityCSVRow['averageHeartrate']);
              if (is_numeric($activityCSVRow['averageHeartrate'])) {
                $activityStats->averageHeartrate = $activityCSVRow['averageHeartrate'];
              } else {
                $formatErrorFields[] = 'averageHeartrate';
              }
            }

            if ($activityCSVRow['maxHeartrate'] != '') {
              if (is_numeric($activityCSVRow['maxHeartrate'])) {
                $activityStats->maxHeartrate = round($activityCSVRow['maxHeartrate']);
              } else {
                $formatErrorFields[] = 'maxHeartrate';
              }
            }

            if ($activityCSVRow['weatherTemperature'] != '') {
              $activityCSVRow['weatherTemperature'] = str_replace(',', '.', $activityCSVRow['weatherTemperature']);
              if (is_numeric($activityCSVRow['weatherTemperature'])) {
                $activityStats->weatherTemperature = $activityCSVRow['weatherTemperature'];
              } else {
                $formatErrorFields[] = 'weatherTemperature';
              }
            }

            if ($activityCSVRow['weatherCode'] != '') {
              if (key_exists($activityCSVRow['weatherCode'], WeatherConstants::$WEATHER_INTERNAL_NAMES)) {
                $activityStats->weatherCode     = $activityCSVRow['weatherCode'];
              } else {
                $formatErrorFields[] = 'weatherCode';
              }
            }

            // determine location if needed {{{
            if ((Settings::$S['USE_LOCATION_SERVICE_TO_FILL_DATA']) && ($activityStats->locationCity === NULL) &&
                ($activityStats->locationDistrict === NULL) && ($activityStats->locationCountry === NULL)) {
              try {
                $activityStats->setLocationDetailsByCoordinates();
              } catch (Exception $e) {
                $pageRequest->sessionData->addErrorMessage($e->getMessage());
              }
            }
            // }}}

            if (count($formatErrorFields) > 0) {
               $skippedCSVRowsMessages[] = t::m('LABEL_ROW') . ' ' . ($rowIndex + 1) . ': ' . implode(',', $formatErrorFields);
            } else {
              $csvDataToRegister[] = array($fGearId, $fType, $fTitle, $fDescription, $fStartTime, $activityStats);
            }
          }

          if (count($skippedCSVRowsMessages) == 0) {
            $registerdActivitiesIds = array();
            foreach ($csvDataToRegister as $csvDataRow) {
              list($gearId, $type, $title, $description, $startTime, $activityStats) = $csvDataRow;
              $activityId = DBMethods::registerActivity(
                $pageRequest->db,                     // $db
                $pageRequest->sessionData->userId,    // $userId
                $gearId,                              // gearId
                $type,                                // type
                $title,                               // $title
                $description,                         // $description
                $startTime,                           // $startTime
                '',                                   // $gpxFileName
                ExternalSourcesConstants::EST_NONE,   // $esType
                '',                                   // $esId
                $activityStats,                       // $activityStats
                NULL                                  // $polyline
              );
              $registerdActivitiesIds[] = $activityId;
            }
            $pageRequest->sessionData->setCustomData('import-csv', array($registerdActivitiesIds));
            AuxiliaryMethods::temporaryRedirect($pageRequest, './import-csv');
          } else {
            $pageRequest->sessionData->addErrorMessage(t::m('PAGE_IMPORT_CSV_ACTIVITIES_ERROR_FORMAT') . ': <br />' .
                                                       implode('<br />', $skippedCSVRowsMessages));
          }
        }
      }
    }
    // }}}

    // results {{{
    $resultingData = $pageRequest->sessionData->popCustomData('import-csv');
    if ($resultingData !== NULL) {
      list($registerdActivitiesIds) = $resultingData;
      $registeredActivitiesRows = DBMethods::getActivitiesRowsByIds($pageRequest->db, $pageRequest->sessionData->userId, $registerdActivitiesIds);
    }
    // }}}

    $tp = array();
    $tp['REGISTERED_ACTIVITIES_ROWS'] = $registeredActivitiesRows;

    $page = new Page($pageRequest, array('pages/importCSV.php'), $tp, t::m('PAGE_IMPORT_CSV_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  private static function _decodePolyline($string) {
      $points = array();
      $index = $i = 0;
      $previous = array(0,0);
      while ($i < strlen($string)) {
          $shift = $result = 0x00;
          do {
              $bit = ord(substr($string, $i++)) - 63;
              $result |= ($bit & 0x1f) << $shift;
              $shift += 5;
          } while ($bit >= 0x20);
          $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
          $number = $previous[$index % 2] + $diff;
          $previous[$index % 2] = $number;
          $index++;
          $points[] = $number * 1 / pow(10, 5);
      }
      return $points;
    }


    private static function _pairPoints($list) {
      return is_array($list) ? array_chunk($list, 2) : array();
    }

}

?>
