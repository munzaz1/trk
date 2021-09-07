<?

class GPXMethods {

  public static function getBaseDataFromGPXFile($gpxFileName) {
    $type       = ActivitiesConstants::AT_OTHER;
    $startTime  = False;
    $title      = False;

    $fileBaseName = pathinfo($gpxFileName, PATHINFO_FILENAME);
    $fileBaseNameParts = explode('-', $fileBaseName);

    foreach ($fileBaseNameParts as $fileBaseNamePart) {
      if (array_key_exists($fileBaseNamePart, ActivitiesConstants::$ACTIVITY_TYPE_BY_FILE_STRING)) {
        $type = ActivitiesConstants::$ACTIVITY_TYPE_BY_FILE_STRING[$fileBaseNamePart];
        break;
      }
    }

    $file = fopen($gpxFileName, 'r');
    if ($file) {
      while (($line = fgets($file)) !== False) {
        if (($startTime === False) && (strpos($line, '<time>') !== False)) {
          $startTimeString = GPXMethods::_getTextBetweenTags($line, 'time');
          $startTime = strtotime($startTimeString);
        }
        if (($title === False) && (strpos($line, '<name>') !== False)) {
          $title = GPXMethods::_getTextBetweenTags($line, 'name');
        }

        if (($startTime !== False) && ($title !== False)) {
          break;
        }
      }
      fclose($file);
    }
    return array($type, $startTime, $title);
  }


  public static function getActivityStatsFromGPX($gpxFileName) {
    require_once 'extensions/GPXIngest.php';
    $gpx = new GPXIngest\GPXIngest();
    $gpx->loadFile($gpxFileName);
    $gpx->ingest();

    /*echo '<pre>';
    print_r($gpx->getJourneyStats());
    echo '</pre>';*/

    $distance   = GPXMethods::_feetsToMeters($gpx->getJourneyStats()->distanceTravelled);
    $movingTime = $gpx->getJourneyStats()->timeMoving;

    $activityStats = new ActivityStatsStorage();
    $activityStats->setData(
      GPXMethods::_feetsToMeters($gpx->getJourneyStats()->distanceTravelled),    // $distance
      $gpx->getJourneyStats()->timeMoving,                                       // $movingTime
      $gpx->getJourneyStats()->recordedDuration,                                 // $elapsedTime
      NULL,                                                                      // $elevationGain,
      NULL,                                                                      // $startLatitude,
      NULL,                                                                      // $startLongitude,
      NULL,                                                                      // $locationCity,
      NULL,                                                                      // $locationDistrict,
      NULL,                                                                      // $locationCountry,
      NULL,                                                                      // $maxSpeed,
      NULL,                                                                      // $averageTemperature,
      NULL,                                                                      // $averageHeartrate,
      NULL,                                                                      // $maxHeartrate,
      NULL,                                                                      // $averageCadence,
      NULL,                                                                      // $averageWatts,
      NULL,                                                                      // $maxWatts,
      NULL,                                                                      // $kilocalories,
      NULL,                                                                      // $deviceName,
      NULL,                                                                      // $weatherTemperature,
      NULL,                                                                      // $weatherCode,
      NULL,                                                                      // $weatherWindSpeed,
      NULL                                                                       // $weatherWindDeg
    );

    return $activityStats;
  }


  private static function _getTextBetweenTags($string, $tagname) {
    $pattern = "/<$tagname>(.*?)<\/$tagname>/";
    preg_match($pattern, $string, $matches);
    return $matches[1];
 }


  private static function _feetsToMeters($feets){
    $feets = intval($feets);
    $meters = ($feets * 0.3048);
    return $meters;
  }

}

?>
