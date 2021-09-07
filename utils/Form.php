<?


class Form {

  public $name;
  public $action;
  public $sendButtonTitle;
  public $fields;
  public $extraClass;
  public $enctype;

  public $valueByField;
  public $errorMessagesByField;


  function __construct($name, $action, $sendButtonTitle, $fields, $extraClass = '') {
    $this->name             = $name;
    $this->action           = $action;
    $this->sendButtonTitle  = $sendButtonTitle;
    $this->fields           = $fields;
    $this->extraClass       = $extraClass;
    $this->enctype          = '';

    $this->valueByField           = array();
    $this->errorMessagesByField   = array();

    foreach ($fields as $field) {
      if (get_class($field) == 'Field_File') {
        $this->enctype = 'enctype="multipart/form-data"';
      }
      $this->valueByField[$field->name] = $field->initialValue;
    }
  }


  public function isSubmitted() {
    return isset($_POST[$this->name]);
  }

  public function validateData() {
    foreach ($this->fields as $field) {
      // file must process the handler {{{
      if (get_class($field) == 'Field_File') {
        continue;
      }
      // }}}

      // get data {{{
      if (isset($_POST[$field->name])) {
        $this->valueByField[$field->name] = $_POST[$field->name];
      } else {
        $this->valueByField[$field->name] = $field->initialValue;
      }
      // }}}

      if (($field->isRequired) && ($this->valueByField[$field->name] == '')) {
        FormMethods::addFormErrorMessage($this->errorMessagesByField, $field->name, t::m('FORM_FIELD_IS_REQUIRED'));
        continue;
      }

      $this->valueByField[$field->name] = $field->normalizeValue($this->valueByField[$field->name]);
      $field->isValid($this->valueByField[$field->name], $this->errorMessagesByField);
    }
  }


  public function getFieldValue($name) {
    return $this->valueByField[$name];
  }

  public function getFieldValueOrNULLIfEmpty($name) {
    $value = $this->valueByField[$name];
    if (($value == '') || ($value == Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX'])) {
      $value = NULL;
    }
    return $value;
  }

}


class Field {

  public $name;
  public $label;
  public $isRequired;
  public $initialValue;
  public $extraClass;


  function __construct($name, $label, $isRequired, $initialValue, $extraClass) {
    $this->name                   = $name;
    $this->label                  = $label;
    $this->isRequired             = $isRequired;
    $this->initialValue           = $initialValue;
    $this->extraClass             = $extraClass;
  }


  public function normalizeValue($value) {
    return $value;
  }


  public function isValid($value, &$errorMessagesByField) {
    return True;
  }


  public function getInputCode($valueByField, $errorMessagesByField) {
    return FormMethods::getInputCode_text(
      $this->name,              // $name
      $valueByField,            // $valueByField
      $errorMessagesByField,    // $errorMessagesByField
      $this->isRequired         // $isRequired
    );
  }

}


class Field_String extends Field {

  public $minLength;
  public $maxLength;
  public $regExpToCheck;


  function __construct($name, $label, $isRequired, $initialValue, $minLength = NULL, $maxLength = NULL, $regExpToCheck = NULL, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);

    $this->minLength              = $minLength;
    $this->maxLength              = $maxLength;
    $this->regExpToCheck          = $regExpToCheck;
  }

  public function isValid($value, &$errorMessagesByField) {
    $valid = True;
    if (($this->minLength !== NULL) && (mb_strlen($value, Settings::$S['ENCODING']) < $this->minLength)) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_SHORT'));
    }
    if (($this->maxLength !== NULL) && (mb_strlen($value, Settings::$S['ENCODING']) > $this->maxLength)) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_LONG'));
    }
    if (($this->regExpToCheck !== NULL) && (!preg_match($this->regExpToCheck, $value))) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_FORMAT'));
    }

    return $valid;
  }
}


class Field_TextArea extends Field {

  function __construct($name, $label, $isRequired, $initialValue, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);
  }


  public function getInputCode($valueByField, $errorMessagesByField) {
    return FormMethods::getInputCode_textArea(
      $this->name,              // $name
      $valueByField,            // $valueByField
      $errorMessagesByField,    // $errorMessagesByField
      $this->isRequired         // $isRequired
    );
  }

}


class Field_Integer extends Field {

  public $minValue;
  public $maxValue;


  function __construct($name, $label, $isRequired, $initialValue, $minValue = NULL, $maxValue = NULL, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);

    $this->minValue               = $minValue;
    $this->maxValue               = $maxValue;
  }


  public function isValid($value, &$errorMessagesByField) {
    $valid = True;
    if ((!$this->isRequired) && ($value == '')) {
      $valid = True;
    } elseif ((!is_numeric($value)) || (!preg_match(RegExpConstants::INTEGERS_CHECK_RE, $value))) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_FORMAT'));
    } else {
      $value = (int)$value;
      if (($this->minValue !== NULL) && ($value < $this->minValue)) {
        $valid = False;
        FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_LOW'));
      }
      if (($this->maxValue !== NULL) && ($value > $this->maxValue)) {
        $valid = False;
        FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_HIGH'));
      }
    }

    return $valid;
  }

}


class Field_Float extends Field {

  public $minValue;
  public $maxValue;


  function __construct($name, $label, $isRequired, $initialValue, $minValue = NULL, $maxValue = NULL, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);

    $this->minValue = $minValue;
    $this->maxValue = $maxValue;
  }


  public function normalizeValue($value) {
    $value = str_replace(' ', '', $value);
    $value = str_replace(',', '.', $value);
    return $value;
  }


  public function isValid($value, &$errorMessagesByField) {
    //$value = $this->normalizeValue($value);
    $valid = True;
    if ((!$this->isRequired) && ($value == '')) {
      $valid = True;
    } elseif (!is_numeric($value)) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_FORMAT'));
    } else {
      $value = (float)$value;
      if (($this->minValue !== NULL) && ($value < $this->minValue)) {
        $valid = False;
        FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_LOW'));
      }
      if (($this->maxValue !== NULL) && ($value > $this->maxValue)) {
        $valid = False;
        FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_TOO_HIGH'));
      }
    }

    return $valid;
  }

}


class Field_DateTime extends Field {

  function __construct($name, $label, $isRequired, $initialValue, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);
  }

  public function isValid($value, &$errorMessagesByField) {
    $valid = True;

    if ((!$this->isRequired) && ($value == '')) {
      $valid = True;
    } elseif (strtotime($value) === FALSE) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_FORMAT'));
    }

    return $valid;
  }
}


class Field_Select extends Field {

  public $optionsDict;


  function __construct($name, $label, $isRequired, $initialValue, $optionsDict = NULL, $extraClass = '') {
    parent::__construct($name, $label, $isRequired, $initialValue, $extraClass);

    $this->optionsDict = $optionsDict;
  }

  public function isValid($value, &$errorMessagesByField) {
    $valid = True;

    if (($this->isRequired) && ($value == Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX'])) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_IS_REQUIRED'));
    }
    elseif (!array_key_exists($value, $this->optionsDict)) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_VALUE'));
    }

    return $valid;
  }

  public function getInputCode($valueByField, $errorMessagesByField) {
    return FormMethods::getInputCode_select(
      $this->name,                                  // $name
      $valueByField,                                // $valueByField,
      $this->optionsDict,                           // $optionsDict,
      $errorMessagesByField,                        // $errorMessagesByField
      $this->isRequired                             // $isRequired
    );
  }

}


class Field_File extends Field {

  function __construct($name, $label, $isRequired) {
    parent::__construct($name, $label, $isRequired, '', '');
  }


  public function isValid($value, &$errorMessagesByField) {
    // must be checked by handler
    $valid = True;
    return $valid;
  }


  public function getInputCode($valueByField, $errorMessagesByField) {
    return FormMethods::getInputCode_file(
      $this->name,                                  // $name
      $errorMessagesByField,                        // $errorMessagesByField,
      $this->isRequired                             // $isRequired
    );
  }

}


class Field_HiddenId extends Field {

  function __construct($name, $initialValue) {
    parent::__construct($name, '', False, $initialValue, '');
  }


  public function isValid($value, &$errorMessagesByField) {
    $valid = True;
    if ((!is_numeric($value)) || (!preg_match(RegExpConstants::INTEGERS_CHECK_RE, $value))) {
      $valid = False;
      FormMethods::addFormErrorMessage($errorMessagesByField, $this->name, t::m('FORM_FIELD_BAD_FORMAT'));
    }
    return $valid;
  }


  public function getInputCode($valueByField, $errorMessagesByField) {
    return FormMethods::getInputCode_hidden(
      $this->name,                                  // $name
      $valueByField                                 // $valueByField,
    );
  }

}

?>
