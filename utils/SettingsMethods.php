<?

class SettingsMethods {

  public static function checkSettings() {
    //  check settings keys {{{
    $missingFields = array_diff(SettingsConstants::$SETTINGS_KEYS, array_keys(Settings::$S));
    if (count($missingFields) > 0) {
      throw new ErrorInvalidSettings(sprintf(t::m('ERROR_INVALID_SETTINGS_MISSING_FIELDS'), implode(', ', $missingFields)));
    }

    $obsoleteFields = array_diff(array_keys(Settings::$S), SettingsConstants::$SETTINGS_KEYS);
    if (count($obsoleteFields) > 0) {
      throw new ErrorInvalidSettings(sprintf(t::m('ERROR_INVALID_SETTINGS_OBSOLETE_FIELDS'), implode(', ', $obsoleteFields)));
    }
    // }}}

    // check default values {{{
    $errorMessageParts = array();
    foreach (SettingsConstants::$SETTINGS_KEYS as $settingsKey) {
      if (strpos(Settings::$S[$settingsKey], '_xxx_') !== False) {
        $errorMessageParts[] = sprintf(t::m('ERROR_INVALID_SETTINGS_VALUE'), $settingsKey);
      }
    }
    if (count($errorMessageParts) > 0) {
      throw new ErrorInvalidSettings(implode('<br />', $errorMessageParts));
    }
    // }}}
  }

}

?>
