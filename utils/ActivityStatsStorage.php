<?

class ActivityStatsStorage {

  public $distance;               // (int) Distance in meters
  public $movingTime;             // (int) Moving time in seconds
  public $elapsedTime;            // (int) Elapsed time in seconds
  public $elevationGain;          // (float) The total elevation gain of the activity
  public $startLatitude;          // (float) Latitude of the starting point
  public $startLongitude;         // (float) Longitude of the starting point
  public $locationCity;           // (string) The city to which the activity belongs
  public $locationDistrict;       // (string) The district to which the activity belongs
  public $locationCountry;        // (string) The country to which the activity belongs
  public $maxSpeed;               // (float) Maximum speed in km/h
  public $averageTemperature;     // (float) Average temperature in Celsius degree
  public $averageHeartrate;       // (float) Average heartrate in 1/min
  public $maxHeartrate;           // (int) Maximum heartrate in 1/min
  public $averageCadence;         // (float) Average cadence in 1/min
  public $averageWatts;           // (float) Average power in W
  public $maxWatts;               // (float) Maximum power in W
  public $kilocalories;           // (float) The number of kilocalories consumed during this activity
  public $deviceName;             // (string) Name of the devise the record has_attribute been done
  public $weatherTemperature;     // (float) Celcius
  public $weatherCode;            // (int)
  public $weatherWindSpeed;       // (int) m/s
  public $weatherWindDeg;         // (int)


  function __construct() {
    $this->distance             = NULL;
    $this->movingTime           = NULL;
    $this->elapsedTime          = NULL;
    $this->elevationGain        = NULL;
    $this->startLatitude        = NULL;
    $this->startLongitude       = NULL;
    $this->locationCity         = NULL;
    $this->locationDistrict     = NULL;
    $this->locationCountry      = NULL;
    $this->maxSpeed             = NULL;
    $this->averageTemperature   = NULL;
    $this->averageHeartrate     = NULL;
    $this->maxHeartrate         = NULL;
    $this->averageCadence       = NULL;
    $this->averageWatts         = NULL;
    $this->maxWatts             = NULL;
    $this->kilocalories         = NULL;
    $this->deviceName           = NULL;
    $this->weatherTemperature   = NULL;
    $this->weatherCode          = NULL;
    $this->weatherWindSpeed     = NULL;
    $this->weatherWindDeg       = NULL;
  }


  public function setData(
    $distance,
    $movingTime,
    $elapsedTime,
    $elevationGain,
    $startLatitude,
    $startLongitude,
    $locationCity,
    $locationDistrict,
    $locationCountry,
    $maxSpeed,
    $averageTemperature,
    $averageHeartrate,
    $maxHeartrate,
    $averageCadence,
    $averageWatts,
    $maxWatts,
    $kilocalories,
    $deviceName,
    $weatherTemperature,
    $weatherCode,
    $weatherWindSpeed,
    $weatherWindDeg
  ) {
    $this->distance             = $distance;
    $this->movingTime           = $movingTime;
    $this->elapsedTime          = $elapsedTime;
    $this->elevationGain        = $elevationGain;
    $this->startLatitude        = $startLatitude;
    $this->startLongitude       = $startLongitude;
    $this->locationCity         = $locationCity;
    $this->locationDistrict     = $locationDistrict;
    $this->locationCountry      = $locationCountry;
    $this->maxSpeed             = $maxSpeed;
    $this->averageTemperature   = $averageTemperature;
    $this->averageHeartrate     = $averageHeartrate;
    $this->maxHeartrate         = $maxHeartrate;
    $this->averageCadence       = $averageCadence;
    $this->averageWatts         = $averageWatts;
    $this->maxWatts             = $maxWatts;
    $this->kilocalories         = $kilocalories;
    $this->deviceName           = $deviceName;
    $this->weatherTemperature   = $weatherTemperature;
    $this->weatherCode          = $weatherCode;
    $this->weatherWindSpeed     = $weatherWindSpeed;
    $this->weatherWindDeg       = $weatherWindDeg;
  }


  public function setDataFromDBRow($dbRow) {
    $this->distance             = $dbRow['distance'];
    $this->movingTime           = $dbRow['movingTime'];
    $this->elapsedTime          = $dbRow['elapsedTime'];
    $this->elevationGain        = $dbRow['elevationGain'];
    $this->startLatitude        = $dbRow['startLatitude'];
    $this->startLongitude       = $dbRow['startLongitude'];
    $this->locationCity         = $dbRow['locationCity'];
    $this->locationDistrict     = $dbRow['locationDistrict'];
    $this->locationCountry      = $dbRow['locationCountry'];
    $this->maxSpeed             = $dbRow['maxSpeed'];
    $this->averageTemperature   = $dbRow['averageTemperature'];
    $this->averageHeartrate     = $dbRow['averageHeartrate'];
    $this->maxHeartrate         = $dbRow['maxHeartrate'];
    $this->averageCadence       = $dbRow['averageCadence'];
    $this->averageWatts         = $dbRow['averageWatts'];
    $this->maxWatts             = $dbRow['maxWatts'];
    $this->kilocalories         = $dbRow['kilocalories'];
    $this->deviceName           = $dbRow['deviceName'];
    $this->weatherTemperature   = $dbRow['weatherTemperature'];
    $this->weatherCode          = $dbRow['weatherCode'];
    $this->weatherWindSpeed     = $dbRow['weatherWindSpeed'];
    $this->weatherWindDeg       = $dbRow['weatherWindDeg'];
  }


  public function setLocationDetailsByCoordinates() {
    if (($this->startLatitude === NULL) || ($this->startLongitude === NULL)) {
      return;
    }

    $url = sprintf(Settings::$S['LOCATION_SERVICE_URL'], $this->startLatitude, $this->startLongitude, Settings::$S['LOCATION_SERVICE_LANGUAGE']);

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_USERAGENT, AboutConstants::APPLICATION_NAME . ' ' . AboutConstants::VERSION);
    $json = curl_exec($curlHandle);
    curl_close($curlHandle);

    //echo $json;
    //echo '<br /><br />';

    $locationData = json_decode($json);
    if (($locationData === NULL) || (!property_exists($locationData, 'address'))) {
      throw new ErrorInvalidResponse(t::m('LABEL_LOCATION_SERVICE') . ': ' .
                                     t::m('ERROR_INVALID_RESPONSE') . ' (' . $json . ')' .
                                     ' (' . $url . ') ' . t::m('HELP_LOCATION_SERVICE_CAN_BE_DISABLED'));
    }
    //print_r($locationData);

    if (isset($locationData->address->town)) {
      $this->locationCity       = $locationData->address->town;
    } elseif (isset($locationData->address->city)) {
      $this->locationCity       = $locationData->address->city;
    } elseif (isset($locationData->address->village)) {
      $this->locationCity       = $locationData->address->village;
    }
    if (isset($locationData->address->state)) {
      $this->locationDistrict   = $locationData->address->state;
    }
    if (isset($locationData->address->country)) {
      $this->locationCountry    = $locationData->address->country;
    }
  }


  public function setWeatherDataFromDescription($description) {
    if (!$description) {
      return;
    }

    if (strpos($description, '°') === FALSE) {
      return;
    }

    $weatherTemperature   = NULL;
    $weatherCode          = NULL;
    $weatherWindSpeed     = NULL;
    $weatherWindDeg       = NULL;
    $descriptionParts = explode(',', $description);
    foreach ($descriptionParts as $descriptionPart) {
      $descriptionPart = trim($descriptionPart);
      // weather code {{{
      if ($weatherCode === NULL) {
        foreach (WeatherConstants::$WEATHER_INTERNAL_NAMES as $code => $internaName) {
          $title = t::m('WEATHER__' . $internaName);
          if ($title == $descriptionPart) {
            $weatherCode = $code;
            break;
          }
        }
      }
      // }}}
      // weather temperature {{{
      if ($weatherTemperature === NULL) {
        if (strpos($descriptionPart, '°') !== FALSE) {
          $parts = explode('°', $descriptionPart);
          if (is_numeric($parts[0])) {
            $weatherTemperature = $parts[0];
          }
        }
      }
      // }}}
      // weather wind {{{
      if ($weatherWindSpeed === NULL) {
        if (strpos($descriptionPart, t::m('WEATHER_WIND')) !== FALSE) {
          $partsWind = explode(' ', $descriptionPart);
          foreach ($partsWind as $part) {
            if (strpos($part, 'm/s') !== FALSE) {
              $windSpeedParts = explode('m/s', $part);
              if (is_numeric($windSpeedParts[0])) {
                $weatherWindSpeed = intval($windSpeedParts[0]);
                break;
              }
            }
          }
          foreach ($partsWind as $part) {
            switch ($part) {
              case 'N':   $weatherWindDeg = 0; break;
              case 'NNE': $weatherWindDeg = 22; break;
              case 'NE':  $weatherWindDeg = 45; break;
              case 'ENE': $weatherWindDeg = 67; break;
              case 'E':   $weatherWindDeg = 90; break;
              case 'ESE': $weatherWindDeg = 112; break;
              case 'SE':  $weatherWindDeg = 135; break;
              case 'SSE': $weatherWindDeg = 157; break;
              case 'S':   $weatherWindDeg = 180; break;
              case 'SSW': $weatherWindDeg = 202; break;
              case 'SW':  $weatherWindDeg = 225; break;
              case 'WSW': $weatherWindDeg = 247; break;
              case 'W':   $weatherWindDeg = 270; break;
              case 'WNW': $weatherWindDeg = 292; break;
              case 'NW':  $weatherWindDeg = 315; break;
              case 'NNW': $weatherWindDeg = 337; break;
            }
          }
        }
      }
      // }}}
    }

    // Převážně oblačno, 14°C, Zdánlivá teplota 14°C, Vlhkost vzduchu 65%, Vítr 4m/s z WSW - Klimat.app
    //print($weatherTemperature .'.'.$weatherCode.'.'.$weatherWindSpeed.'.'.$weatherWindDeg);
    //exit;

    $this->weatherTemperature   = $weatherTemperature;
    $this->weatherCode          = $weatherCode;
    $this->weatherWindSpeed     = $weatherWindSpeed;
    $this->weatherWindDeg       = $weatherWindDeg;
  }


  public function print() {
    echo 'distance: '             . $this->distance             . '<br />';
    echo 'movingTime: '           . $this->movingTime           . '<br />';
    echo 'elapsedTime: '          . $this->elapsedTime          . '<br />';
    echo 'elevationGain: '        . $this->elevationGain        . '<br />';
    echo 'startLatitude: '        . $this->startLatitude        . '<br />';
    echo 'startLongitude: '       . $this->startLongitude       . '<br />';
    echo 'locationCity: '         . $this->locationCity         . '<br />';
    echo 'locationDistrict: '     . $this->locationDistrict     . '<br />';
    echo 'locationCountry: '      . $this->locationCountry      . '<br />';
    echo 'maxSpeed: '             . $this->maxSpeed             . '<br />';
    echo 'averageTemperature: '   . $this->averageTemperature   . '<br />';
    echo 'averageHeartrate: '     . $this->averageHeartrate     . '<br />';
    echo 'maxHeartrate: '         . $this->maxHeartrate         . '<br />';
    echo 'averageCadence: '       . $this->averageCadence       . '<br />';
    echo 'averageWatts: '         . $this->averageWatts         . '<br />';
    echo 'maxWatts: '             . $this->maxWatts             . '<br />';
    echo 'kilocalories: '         . $this->kilocalories         . '<br />';
    echo 'deviceName: '           . $this->deviceName           . '<br />';
    echo 'weatherTemperature: '   . $this->weatherTemperature   . '<br />';
    echo 'weatherCode: '          . $this->weatherCode          . '<br />';
    echo 'weatherWindSpeed: '     . $this->weatherWindSpeed     . '<br />';
    echo 'weatherWindDeg: '       . $this->weatherWindDeg       . '<br />';
  }

}

?>
