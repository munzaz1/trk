<?

class t {

  public static function m($key) {
    $translationDictionaryName          = 'translations_' . Settings::$S['LANGUAGE'];
    $fallbackTranslationDictionaryName  = 'translations_en';

    global $$translationDictionaryName;
    global $$fallbackTranslationDictionaryName;

    if (key_exists($key, $$translationDictionaryName)) {
      return $$translationDictionaryName[$key];
    } elseif (key_exists($key, $$fallbackTranslationDictionaryName)) {
      return $$fallbackTranslationDictionaryName[$key];
    } else {
      return 'n/a';
    }
  }

}

?>
