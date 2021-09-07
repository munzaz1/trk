<?

class Handler_USER {

  public static function loginHandler($pageRequest) {
    if ($pageRequest->sessionData->userId !== NULL) {
      AuxiliaryMethods::temporaryRedirect($pageRequest, './');
    }

    $errorMessagesByField   = array();
    $login                  = '';

    // handle login form {{{
    if (isset($_POST['fLoginSend'])) {
      $formValid = 1;
      $formValid &= FormMethods::checkInput_string(
        'fPassword',                                                                                // $name
        1,                                                                                          // $isRequired
        $errorMessagesByField                                                                       // &$errorMessagesByField
      );

      if ($formValid) {
        $password = FormMethods::getStringValue('fPassword', 1);

        if ($password == Settings::$S['PASSWORD']) {
          $pageRequest->sessionData->userId = Settings::$S['DEFAULT_USER_ID'];
          AuxiliaryMethods::temporaryRedirect($pageRequest, './');
        } else {
          FormMethods::addFormErrorMessage($errorMessagesByField, 'fPassword', t::m('PAGE_LOGIN_INVALID_LOGIN_PASSWORD'));
        }
      }
    }
    // }}}

    $tp = array();
    $tp['ERROR_MESSAGES_BY_FIELD']  = $errorMessagesByField;

    $contentFileNames = array('pages/login.php');
    $page = new Page($pageRequest, $contentFileNames, $tp, t::m('PAGE_LOGIN_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function logoutHandler($pageRequest) {
    $pageRequest->sessionData->userId = NULL;

    AuxiliaryMethods::temporaryRedirect($pageRequest, './');

    return HandlersConstants::HS_OK;
  }

}
?>
