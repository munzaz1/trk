<?

class StatsMethods {

  public static function getYearStatsData($db, $userId, $year) {
    $statsDataByMonthByType = array();
    $statsDataByType        = array();
    for ($month = 1; $month <= 12; $month ++) {
      $statsDataByMonthByType[$month] = array();
    }
    $activitiesRows = DBMethods::getActivitiesRows(
      $db,
      $userId,
      mktime(0, 0, 0, 1, 1, $year),
      mktime(0, 0, 0, 12, 31, $year) + TimeConstants::SECONDS_IN_DAY
    );
    foreach ($activitiesRows as $activityRow) {
      $month = date('n', $activityRow['startTime']);
      if (!key_exists($activityRow['type'], $statsDataByMonthByType[$month])) {
        $statsDataByMonthByType[$month][$activityRow['type']] = array(
          'count'         => 0,
          'distance'      => 0,
          'time'          => 0,
          'elevationGain' => 0,
        );
      }
      $statsDataByMonthByType[$month][$activityRow['type']]['count']         += 1;
      $statsDataByMonthByType[$month][$activityRow['type']]['distance']      += $activityRow['distance'];
      $statsDataByMonthByType[$month][$activityRow['type']]['time']          += $activityRow['movingTime'];
      $statsDataByMonthByType[$month][$activityRow['type']]['elevationGain'] += $activityRow['elevationGain'];

      if (!key_exists($activityRow['type'], $statsDataByType)) {
        $statsDataByType[$activityRow['type']] = array(
          'count'         => 0,
          'distance'      => 0,
          'time'          => 0,
          'elevationGain' => 0,
        );
      }
      $statsDataByType[$activityRow['type']]['count']         += 1;
      $statsDataByType[$activityRow['type']]['distance']      += $activityRow['distance'];
      $statsDataByType[$activityRow['type']]['time']          += $activityRow['movingTime'];
      $statsDataByType[$activityRow['type']]['elevationGain'] += $activityRow['elevationGain'];
    }
    return array($statsDataByMonthByType, $statsDataByType);
  }

}

?>
