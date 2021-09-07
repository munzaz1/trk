<?

class WebApplication {

  function __construct() {
    SettingsMethods::checkSettings();
    // TODO: check MAINTENANCE_PHASE

    date_default_timezone_set(Settings::$S['TIME_ZONE']);

    $db = new DBConnector(
      Settings::$S['DB_SERVER'],
      Settings::$S['DB_USER'],
      Settings::$S['DB_PASSWORD'],
      Settings::$S['DB_DATABASE']
    );
    $this->db                     = $db;
    $this->handleMethodByURLId    = array();
    $this->onlyForLoggedInByURLId = array();
  }


  public function doApp() {
    if (isset($_GET['url_id'])) {
      $urlId = $_GET['url_id'];
    } else {
      $urlId = '';
    }

    if (isset($_SESSION[AboutConstants::APPLICATION_NAME . 'sessionData'])) {
      $sessionData = unserialize($_SESSION[AboutConstants::APPLICATION_NAME . 'sessionData']);
    } else {
      $sessionData = NULL;
    }
    // XXX: DEBUG
    if (!($sessionData instanceof SessionData)) {
    //if (1) {
      $sessionData  = new SessionData();
    }

    if (Settings::$S['AUTO_LOGIN_USER_ID'] !== NULL) {
      $sessionData->userId = Settings::$S['AUTO_LOGIN_USER_ID'];
    }

    $globalSettings = new GlobalSettings($this->db);

    $pageRequest = new PageRequest($this->db, $sessionData, $urlId, $globalSettings);

    $handleResult = HandlersConstants::HS_NOT_FOUND;

    // registered URIs {{{
    if (array_key_exists($urlId, $this->handleMethodByURLId)) {
      if (($this->onlyForLoggedInByURLId[$urlId]) && ($sessionData->userId === NULL)) {
        AuxiliaryMethods::temporaryRedirect($pageRequest, 'login');
      } else {
        $handleMethod = $this->handleMethodByURLId[$urlId];
        $handleResult = call_user_func($handleMethod, $pageRequest);
      }
    // }}}
    // url ids from db {{{
    } else {
      // ?
    }
    // }}}

    // not found (404) {{{
    if ($handleResult == HandlersConstants::HS_NOT_FOUND) {
      header("HTTP/1.0 404 Not Found");
      $page = new Page($pageRequest, array('pages/404.php'), array(), t::m('PAGE_404_TITLE'));
      $page->printPage();
    }
    // }}}

    $this->db->close();
    $_SESSION[AboutConstants::APPLICATION_NAME . 'sessionData'] = serialize($sessionData);
  }


  /**
    @param urlId URL ID string (e.g. foobar will handle HOSTNAME/foobar)
    @param handleMethod array in format array('className', 'methodName') of a method to handle the URL
    */
  public function registerPageHandler($urlId, $handleMethod, $onlyForLoggedIn) {
    $this->handleMethodByURLId[$urlId]    = $handleMethod;
    $this->onlyForLoggedInByURLId[$urlId] = $onlyForLoggedIn;
  }

}

?>
