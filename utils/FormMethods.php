<?

class FormMethods {

  public static function fillValuesFromForm(&$valueByField) {
    foreach ($valueByField as $field => $value) {
      if (isset($_POST[$field])) {
        $valueByField[$field] = $_POST[$field];
      }
    }
  }


  // fields code methods {{{

  public static function getInputCode_text($name, $valueByField, $errorMessagesByField, $isRequired = 0, $class = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    $class = implode(' ', $classes);
    return $errorBox . '<input type="text" name="' . $name . '" value="' . $valueByField[$name] . '" class="' . $class . '" />';
  }


  public static function getInputCode_textArea($name, $valueByField, $errorMessagesByField, $isRequired = 0, $class = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    $class = implode(' ', $classes);
    return $errorBox . '<textarea name="' . $name . '" class="' . $class . '">' . $valueByField[$name] . '</textarea>';
  }


  public static function getInputCode_select($name, $valueByField, $optionsDict, $errorMessagesByField, $isRequired = 0, $class = '', $javaScript = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    $class = implode(' ', $classes);
    $outLines = array();
    $outLines[] = '<select name="' . $name . '" class="' . $class . '" ' . $javaScript . '>';
    foreach ($optionsDict as $optionValue => $optionTitle) {
      if ($valueByField[$name] == $optionValue) {
        $selectedStr = ' selected="selected"';
      } else {
        $selectedStr = '';
      }
      $outLines[] = '<option value="' . $optionValue . '"' . $selectedStr . '>' . $optionTitle . '</option>';
    }
    $outLines[] = '</select>';
    return $errorBox . implode("\n", $outLines);
  }


  public static function getInputCode_checkbox($name, $valueByField, $errorMessagesByField, $isRequired = 0, $class = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    if ($valueByField[$name] == 1) {
      $checkedStr = ' checked="checked"';
    } else {
      $checkedStr = '';
    }
    $class = implode(' ', $classes);
    return $errorBox . '<input type="checkbox" name="' . $name . '" value="1"' . $checkedStr . ' class="' . $class . '" />';
  }


  public static function getInputCode_password($name, $valueByField, $errorMessagesByField, $isRequired = 0, $class = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    $class = implode(' ', $classes);
    return $errorBox . '<input type="password" name="' . $name . '" value="' . $valueByField[$name] . '" class="' . $class . '" />';
  }


  public static function getInputCode_file($name, $errorMessagesByField, $isRequired = 0, $class = '') {
    $classes  = array($class);
    $errorBox = '';
    if (key_exists($name, $errorMessagesByField)) {
      $classes[] = 'error';
      $errorBox = self::getFieldErrorBox($name, $errorMessagesByField);
    }
    if ($isRequired) {
      $classes[] = 'required';
    }
    $class = implode(' ', $classes);
    return $errorBox . '<input type="file" name="' . $name . '" class="' . $class . '" />';
  }


  public static function getInputCode_hidden($name, $valueByField) {
    return '<input type="hidden" name="' . $name . '" value="' . $valueByField[$name] . '" />';
  }

  // }}}


  // auxiliary methods {{{

  public static function getFieldErrorBox($name, $errorMessagesByField) {
    return '<div class="fieldErrorBox"><div class="fieldErrorBoxOut">' . implode('<br />', $errorMessagesByField[$name]) . '</div></div>';
  }


  public static function getInputErrorParagraph($name, $errorMessagesByField) {
    if (array_key_exists($name, $errorMessagesByField)) {
      return '<p class="errorMessage">' .
             implode('<br />', $errorMessagesByField[$name]) .
             '</p>';
    } else {
      return '';
    }
  }


  public static function getStringValue($name, $isRequired) {
    if ($isRequired) {
      return $_POST[$name];
    } else {
      if (isset($_POST[$name])) {
        return $_POST[$name];
      } else {
        return '';
      }
    }
  }


  // }}}


  // form check methods {{{

  public static function checkInput_string($name, $isRequired, &$errorMessagesByField, $minLength = NULL, $maxLength = NULL, $regExpToCheck = NULL) {
    $valid = 1;

    if (($isRequired) && (!isset($_POST[$name]))) {
      $valid = 0;
      self::addFormErrorMessage($errorMessagesByField, $name, t::m('FORM_FIELD_IS_REQUIRED'));
    } else {
      $value = FormMethods::getStringValue($name, $isRequired);

      if ($value == '') {
        if ($isRequired) {
          $valid = 0;
          self::addFormErrorMessage($errorMessagesByField, $name, t::m('FORM_FIELD_IS_REQUIRED'));
          return 0;
        } else {
          return 1;
        }
      }

      if (($minLength !== NULL) && (mb_strlen($value, Settings::$S['ENCODING']) < $minLength) &&
          (($isRequired) || ($value != ''))) {
        $valid = 0;
        self::addFormErrorMessage($errorMessagesByField, $name, t::m('FORM_FIELD_TOO_SHORT'));
      }
      if (($maxLength !== NULL) && (mb_strlen($value, Settings::$S['ENCODING']) > $maxLength)) {
        $valid = 0;
        self::addFormErrorMessage($errorMessagesByField, $name, t::m('FORM_FIELD_TOO_LONG'));
      }
      if (($regExpToCheck !== NULL) && (!preg_match($regExpToCheck, $value))) {
        $valid = 0;
        self::addFormErrorMessage($errorMessagesByField, $name, t::m('FORM_FIELD_BAD_FORMAT'));
      }
    }

    return $valid;
  }


  public static function addFormErrorMessage(&$errorMessagesByField, $name, $errorMessage) {
    if (!array_key_exists($name, $errorMessagesByField)) {
      $errorMessagesByField[$name] = array($errorMessage);
    } else {
      $errorMessagesByField[$name][] = $errorMessage;
    }
  }


  public static function checkLoginFields(&$errorMessagesByField) {
    $formValid = 1;
    $formValid &= FormMethods::checkInput_string(
      'fEMail',                                             // $name
      1,                                                    // $isRequired
      $errorMessagesByField,                                // &$errorMessagesByField
      1,                                                    // $minLength
      20,                                                   // $maxLength
      NULL                                                  // $regExpToCheck
    );
    $formValid &= FormMethods::checkInput_string(
      'fPassword',                                          // $name
      1,                                                    // $isRequired
      $errorMessagesByField,                                // &$errorMessagesByField
      1,                                                    // $minLength
      20,                                                   // $maxLength
      NULL                                                  // $regExpToCheck
    );
    return $formValid;
  }


  public static function checkChangePasswordFields(&$errorMessagesByField) {
    $formValid = 1;
    // fOriginalPassword is not checked (can be historically any)
    $formValid &= FormMethods::checkInput_string(
      'fPassword1',                                                                       // $name
      FormDefinitionConstants::$CHANGE_PASSWORD_FORM['fPassword1']['isRequired'],         // $isRequired
      $errorMessagesByField,                                                              // &$errorMessagesByField
      5,                                                                                  // $minLength
      20,                                                                                 // $maxLength
      NULL                                                                                // $regExpToCheck
    );
    $formValid &= FormMethods::checkInput_string(
      'fPassword2',                                                                       // $name
      FormDefinitionConstants::$CHANGE_PASSWORD_FORM['fPassword2']['isRequired'],         // $isRequired
      $errorMessagesByField,                                                              // &$errorMessagesByField
      5,                                                                                  // $minLength
      20,                                                                                 // $maxLength
      NULL                                                                                // $regExpToCheck
    );
    return $formValid;
  }

  // }}}


  // data building methods {{{

  public static function getActivityTypeItems() {
    $dict = array();
    foreach (ActivitiesConstants::$ACTIVITIES_ORDER as $type) {
      $internalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$type];
      $dict[$type] = t::m('ACTIVITY_NAME__' . $internalName);
    }
    return $dict;
  }


  public static function getWeatherCodesItems() {
    $dict = array();
    foreach (WeatherConstants::$WEATHER_INTERNAL_NAMES as $weatherCode => $internalName) {
      $weatherTitle = t::m('WEATHER__' . $internalName);
      if ($weatherCode == WeatherConstants::WC_UNKNOWN) {
        $dict[''] = $weatherTitle;
      } else {
        $dict[$weatherCode] = $weatherTitle;
      }
    }
    asort($dict);
    return $dict;
  }


  public static function getGearTypesItems() {
    $dict = array();
    foreach (GearConstants::$GEAR_INTERNAL_NAMES as $gearType => $gearInternalName) {
      $dict[$gearType] = t::m('GEAR_NAME__' . $gearInternalName);
    }
    return $dict;
  }


  public static function getESTypeItems() {
    $dict = array();
    foreach (ExternalSourcesConstants::$EXTERNAL_SOURCE_NAMES as $eaType => $eaName) {
      $dict[$eaType] = $eaName;
    }
    return $dict;
  }


  public static function getPhotoRatingItems($addEmptyValue = False) {
    $dict = array();
    if ($addEmptyValue) {
      $dict[Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX']] = '';
    }
    for ($rating = Settings::$S['PHOTO_RATING_MIN']; $rating <= Settings::$S['PHOTO_RATING_MAX']; $rating ++) {
      $dict[$rating] = $rating;
    }
    return $dict;
  }


  public static function getYesNoItems($addEmptyValue = False) {
    $dict = array();
    if ($addEmptyValue) {
      $dict[Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX']] = '';
    }
    $dict[1] = t::m('LABEL_YES');
    $dict[0] = t::m('LABEL_NO');
    return $dict;
  }

  // }}}

}

?>