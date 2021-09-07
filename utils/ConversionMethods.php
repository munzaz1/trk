<?

class ConversionMethods {

  public static function timeUnitsStringToSeconds($timeUnitsString) {
    // Format must be 'h' or 'h:m' or 'h:m:s', returns NULL if not valid.

    if ($timeUnitsString === NULL) {
      return NULL;
    }

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;

    $parts = explode(':', $timeUnitsString);
    if (count($parts) == 1) {
      $hours    = trim($parts[0]);
      $minutes  = 0;
      $seconds  = 0;
    } elseif (count($parts) == 2) {
      $hours    = trim($parts[0]);
      $minutes  = trim($parts[1]);
      $seconds  = 0;
    } elseif (count($parts) == 3) {
      $hours    = trim($parts[0]);
      $minutes  = trim($parts[1]);
      $seconds  = trim($parts[2]);
    } else {
      return NULL;
    }

    if ((is_numeric($hours)) && (is_numeric($minutes)) && (is_numeric($seconds))) {
      return round(($hours * $secondsInAnHour) + ($minutes * $secondsInAMinute) + $seconds);
    } else {
      return NULL;
    }
  }


  public static function stringToLatitudeLongintude($latLonString) {
    if ($latLonString === NULL) {
      return array(NULL, NULL);
    }

    $latitude   = NULL;
    $longitude  = NULL;

    $LatLonParts = explode(',', $latLonString);
    if (count($LatLonParts) == 2) {
      $tmpLat = trim($LatLonParts[0]);
      if (!is_numeric(substr($tmpLat, -1))) {
        if (strtoupper(substr($tmpLat, -1)) == 'S') {
          $tmpLat = '-' . $tmpLat;
        }
        $tmpLat = substr($tmpLat, 0, -1);
      }
      $tmpLon = trim($LatLonParts[1]);
      if (!is_numeric(substr($tmpLon, -1))) {
        if (strtoupper(substr($tmpLon, -1)) == 'W') {
          $tmpLon = '-' . $tmpLon;
        }
        $tmpLon = substr($tmpLon, 0, -1);
      }

      if ((is_numeric($tmpLat)) && (is_numeric($tmpLon))) {
        $latitude   = floatval($tmpLat);
        $longitude  = floatval($tmpLon);
      }
    }
    return array($latitude, $longitude);
  }


  public static function kilometersToMeters($kilometers) {
     if ($kilometers === NULL) {
       return NULL;
     } else {
       return round($kilometers * 1000);
     }
   }


   public static function milesToMeters($kilometers) {
     if ($kilometers === NULL) {
       return NULL;
     } else {
       return round($kilometers * 1609.344);
     }
   }


  public static function metersToKilometers($meters) {
     if ($meters === NULL) {
       return NULL;
     } else {
       return $meters / 1000;
     }
   }


   public static function metersToMiles($meters) {
     if ($meters === NULL) {
       return NULL;
     } else {
       return $meters * 0.000621371192;
     }
   }


   public static function metersPerSecondToKilometersPerHour($metersPerSecond) {
     if ($metersPerSecond === NULL) {
       return NULL;
     } else {
       return $metersPerSecond * 3.6;
     }
   }


   public static function metersPerSecondToMilesPerHour($metersPerSecond) {
     if ($metersPerSecond === NULL) {
       return NULL;
     } else {
       return $metersPerSecond * 2.24;
     }
   }


   public static function kilometersPerHourToMetersPerSecond($kilometersPerHour) {
     if ($kilometersPerHour === NULL) {
       return NULL;
     } else {
       return $kilometersPerHour / 3.6;
     }
   }


   public static function milesPerHourToMetersPerSecond($milesPerHour) {
     if ($milesPerHour === NULL) {
       return NULL;
     } else {
       return $milesPerHour / 2.24;
     }
   }


   public static function metersToFeets($meters) {
     if ($meters === NULL) {
       return NULL;
     } else {
       return $meters * 3.2808399;
     }
   }


   public static function feetsToMeters($feets) {
     if ($feets === NULL) {
       return NULL;
     } else {
       return $feets / 3.2808399;
     }
   }

}

?>
