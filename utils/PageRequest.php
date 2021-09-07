<?

class PageRequest {

  public $db;
  public $sessionData;
  public $urlId;
  public $globalSettings;


  /**
    @param db Instace of DBConnector()
    @param sessionData Instace of SessionData()
    @param urlId url ID string
    @param globalSettings Instance of GlobalSettings()
    */
  function __construct($db, $sessionData, $urlId, $globalSettings) {
    $this->db                       = $db;
    $this->sessionData              = $sessionData;
    $this->urlId                    = $urlId;
    $this->globalSettings           = $globalSettings;
  }

}

?>
