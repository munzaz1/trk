<?

class TextFormattingMethods {

  public static function formatDateTime($time, $withSeconds = FALSE, $withoutYear = FALSE) {
    if ($withSeconds) {
      return date(Settings::$S['DATE_TIME_FORMAT_SECONDS'], $time);
    } elseif ($withoutYear) {
      return date(Settings::$S['DATE_TIME_FORMAT_WITHOUT_YEAR'], $time);
    } else {
      return date(Settings::$S['DATE_TIME_FORMAT'], $time);
    }
  }


  public static function formatDate($time, $withoutYear = FALSE) {
    if ($withoutYear) {
      return date(Settings::$S['DATE_FORMAT_WITHOUT_YEAR'], $time);
    } else {
      return date(Settings::$S['DATE_FORMAT'], $time);
    }
  }


  public static function formatNumber($number, $decimals = 2) {
    if (!is_numeric($number)) {
      return '';
    }
    $formattedString = number_format($number, $decimals, Settings::$S['DECIMAL_DELIMITER'], Settings::$S['THOUSANDS_DELIMITER']);
    return $formattedString;
  }


  public static function formatDistance($distanceInMeters, $useUnits = True) {
    if (Settings::$S['METRIC_UNITS']) {
      $kilometers = ConversionMethods::metersToKilometers($distanceInMeters);
      if ($kilometers >= 2) {
        return TextFormattingMethods::formatNumber($kilometers) . ($useUnits ? ' ' . t::m('LABEL_UNIT_KILOMETERS') : '');
      } else {
        return TextFormattingMethods::formatNumber($distanceInMeters) . ($useUnits  ? ' ' . t::m('LABEL_UNIT_METERS') : '');
      }
    } else {
      $miles = ConversionMethods::metersToMiles($distanceInMeters);
      return TextFormattingMethods::formatNumber($miles) . ($useUnits ? ' ' . t::m('LABEL_UNIT_MILES') : '');
    }
  }


  public static function formatSpeed($speedInMetersPerSecond, $useUnits = True)  {
    if (Settings::$S['METRIC_UNITS']) {
      $speed = ConversionMethods::metersPerSecondToKilometersPerHour($speedInMetersPerSecond);
    } else {
      $speed = ConversionMethods::metersPerSecondToMilesPerHour($speedInMetersPerSecond);
    }

    if (!$useUnits) {
      $unitStr = '';
    } else {
      $unitStr = ' ' . t::m('LABEL_UNIT_SPEED');
    }

    return TextFormattingMethods::formatNumber($speed) . $unitStr;
  }


  public static function formatPace($movingTime, $distance, $useUnits = True) {
    if (Settings::$S['METRIC_UNITS']) {
      $inputSeconds = $movingTime / (ConversionMethods::metersToKilometers($distance));
      $unitStr      = ' ' . t::m('LABEL_UNIT_MINUTES_PER_KILOMETER');
    } else {
      $inputSeconds = $movingTime / (ConversionMethods::metersToMiles($distance));
      $unitStr      = ' ' . t::m('LABEL_UNIT_MINUTES_PER_MILE');
    }

    if (!$useUnits) {
      $unitStr = '';
    }

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;

    // extract minutes
    $minuteSeconds = $inputSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    return sprintf('%02d:%02d'.$unitStr, $minutes, $seconds);
  }


  public static function formatAverageSpeedOrPace($activityType, $distance, $movingTime, $useUnits = True) {
    if (($distance === NULL) || ($movingTime === NULL) || ($distance <= 0) || ($movingTime <= 0)) {
      return '-';
    }
    $formattedParts = array();
    if (in_array($activityType, ActivitiesConstants::$ACTIVITIES_WITH_AVERAGE_SPEED)) {
      $formattedParts[] = TextFormattingMethods::formatSpeed($distance / $movingTime, $useUnits);
    }
    if (in_array($activityType, ActivitiesConstants::$ACTIVITIES_WITH_AVERAGE_PACE)) {
      $formattedParts[] = TextFormattingMethods::formatPace($movingTime, $distance, $useUnits);
    }
    if (count($formattedParts) > 0) {
      return implode(' ', $formattedParts);
    } else {
      return '-';
    }
  }


  public static function formatElevationGain($meters, $useUnits = True) {
    if (Settings::$S['METRIC_UNITS']) {
      return TextFormattingMethods::formatNumber($meters, $decimals = 0) . ($useUnits ? ' ' . t::m('LABEL_UNIT_METERS') : '');
    } else {
      $feets = ConversionMethods::metersToFeets($meters);
      return TextFormattingMethods::formatNumber($feets, $decimals = 0) . ($useUnits ? ' ' . t::m('LABEL_UNIT_FOOT') : '');
    }
  }


  public static function formatWeightAsNumber($inputGrams) {
    $kilograms = ($inputGrams / 1000);
    return $kilograms;
  }


  public static function formatWeight($inputGrams) {
    $kilograms = TextFormattingMethods::formatWeightAsNumber($inputGrams);
    return TextFormattingMethods::formatNumber($kilograms) . ' ' . t::m('LABEL_UNIT_KILOGRAM');
  }


  public static function secondsToFromattedTimeUnits($inputSeconds) {
    if ($inputSeconds === NULL) {
      return '';
    }

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    $formattedString = '';
    if ($days > 0) {
      $formattedString .= $days . 'd ';
    }
    $formattedString .= sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    return $formattedString;
  }


  public static function getActivityLinksByIds($activitiesRows) {
    $links = array();
    foreach ($activitiesRows as $activityRow) {
      $links[] = '<a href="./activity?id=' . $activityRow['id'] . '">' . $activityRow['title'] . '</a>';
    }
    return $links;
  }


}

?>
