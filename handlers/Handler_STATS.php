<?

class Handler_STATS {

  public static function statsHandler($pageRequest) {
    list($minYear, $maxYear) = DBMethods::getMinMaxActivityYears($pageRequest->db, $pageRequest->sessionData->userId);
    $years = AuxiliaryMethods::getYearsListFromMinMax($minYear, $maxYear);

    $statsDataByYearByMonthByType = array();
    $statsDataByYearByType   = array();

    foreach ($years as $year) {
      list($statsDataByMonthByType, $statsDataByType) = StatsMethods::getYearStatsData($pageRequest->db, $pageRequest->sessionData->userId, $year);

      $statsDataByYearByMonthByType[$year]  = $statsDataByMonthByType;
      $statsDataByYearByType[$year]         = $statsDataByType;
    }
    $tp = array();
    $tp['STATS_DATA_BY_YEAR_BY_MONTH_BY_TYPE']  = $statsDataByYearByMonthByType;
    $tp['STATS_DATA_BY_YEAR_BY_TYPE']           = $statsDataByYearByType;

    $page = new Page($pageRequest, array('pages/stats.php'), $tp, t::m('PAGE_STATS_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function cumulativeStatsHandler($pageRequest) {
    $month        = date('n');
    $activityType = ActivitiesConstants::AT_ANY;
    $yAxis        = 0;  // 0 - time, 1 - distance

    if (isset($_GET['month'])) {
      if ((is_numeric($_GET['month'])) && ($_GET['month'] >= 1) && ($_GET['month'] <= 12)) {
        $month = intval($_GET['month']);
      }
    }
    if (isset($_GET['type'])) {
      if ((is_numeric($_GET['type'])) && (key_exists($_GET['type'], ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES))) {
        $activityType = intval($_GET['type']);
      }
    }
    if (isset($_GET['yAxis'])) {
      if ((is_numeric($_GET['yAxis'])) && (in_array(intval($_GET['yAxis']), array(0, 1)))) {
        $yAxis = intval($_GET['yAxis']);
      }
    }

    $usedActivitiesTypes = array();
    $monthActivitiesByYear = array();
    list($minYear, $maxYear) = DBMethods::getMinMaxActivityYears($pageRequest->db, $pageRequest->sessionData->userId);
    $years = AuxiliaryMethods::getYearsListFromMinMax($minYear, $maxYear);
    foreach ($years as $year) {
      $monthActivities = DBMethods::getMonthActivitiesByMonthYear($pageRequest->db, $pageRequest->sessionData->userId, $month, $year, ActivitiesConstants::AT_ANY);
      if (count($monthActivities) > 0) {
        $monthActivitiesByYear[$year] = array();
        foreach ($monthActivities as $day => $activitiesRows) {
          foreach ($activitiesRows as $activityRow) {
            if (($activityRow['type'] == $activityType) || ($activityType == ActivitiesConstants::AT_ANY)) {
              if (!key_exists($day, $monthActivitiesByYear[$year])) $monthActivitiesByYear[$year][$day] = array();
              $monthActivitiesByYear[$year][$day][] = $activityRow;
            }
            if (!in_array($activityRow['type'], $usedActivitiesTypes)) {
              $usedActivitiesTypes[] = $activityRow['type'];
            }
          }
        }
      }
    }

    $tp = array();
    $tp['MONTH']                    = $month;
    $tp['ACTIVITY_TYPE']            = $activityType;
    $tp['MONTH_ACTIVITIES_BY_YEAR'] = $monthActivitiesByYear;
    $tp['USED_ACTIVITIES_TYPES']    = $usedActivitiesTypes;
    $tp['Y_AXIS']                   = $yAxis;

    $page = new Page($pageRequest, array('pages/cumulativeStats.php'), $tp, t::m('PAGE_CUMULATIVE_STATS_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }

}

?>
