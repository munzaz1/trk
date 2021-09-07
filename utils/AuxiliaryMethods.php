<?

class AuxiliaryMethods {

  public static function temporaryRedirect($pageRequest, $location) {
    $_SESSION[AboutConstants::APPLICATION_NAME . 'sessionData'] = serialize($pageRequest->sessionData);
    $pageRequest->db->close();
    header('Location: ' . $location, true, 302);
    exit;
  }


  public static function TemporaryRedirectSimple($location) {
    header('Location: ' . $location, true, 302);
    exit;
  }


  public static function getValueOfProperty($item, $propertyName, $defaultValue) {
    if (property_exists($item, $propertyName)) {
      return $item->{$propertyName};
    } else {
      return $defaultValue;
    }
  }


  public static function getValueOrDefault(&$value, $defaultValue) {
    if (isset($value)) {
      return $value;
    } else {
      return $defaultValue;
    }
  }


  public static function sumarizeSecondDimensionInArray($array, $valueKey) {
    $sum = 0;
    foreach($array as $key => $values) {
      $sum += $values[$valueKey];
    }
    return $sum;
  }


  public static function getYearsListFromMinMax($minYear, $maxYear) {
    $years = array();
    if ($minYear !== NULL) {
      for ($year = $minYear; $year <= $maxYear; $year ++) {
        $years[] = $year;
      }
    }
    return $years;
  }


  public static function csvToArray($filename, $delimiter = ';') {
    if (!file_exists($filename) || !is_readable($filename)) {
      return FALSE;
    }

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
        if (!$header) {
          $header = $row;
        } else {
          if (count($header) != count($row)) {
            fclose($handle);
            return FALSE;
          }
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }
    return $data;
  }


  public static function getColumnFromRows($columnKey, $dataRows) {
    $column = array();
    foreach ($dataRows as $row) {
      $column[] = $row[$columnKey];
    }
    return $column;
  }


  function shortText($text, $maxLenth) {
    $text = substr($text, 0, $maxLenth);
    if (strlen($text) == $maxLenth) {
      $text = substr($text, 0, strrpos($text, ' ')) . '...';
    }
    return $text;
  }

}

?>
