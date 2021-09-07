<?

class SessionData {

  public $userId;                   // NULL or ID of the user
  private $errorMessages;           // array of messages
  private $infoMessages;            // array of messages
  private $customDataByUrl;         // any type of data by URL

  function __construct() {
    $this->userId           = NULL;
    $this->errorMessages    = array();
    $this->infoMessages     = array();
    $this->customDataByUrl  = array();
  }


  public function addErrorMessage($message) {
    $this->errorMessages[] = $message;
  }


  public function addInfoMessage($message) {
    $this->infoMessages[] = $message;
  }


  public function popMessages() {
    $errorMessages  = $this->errorMessages;
    $infoMessages   = $this->infoMessages;

    $this->errorMessages  = array();
    $this->infoMessages   = array();

    return array($errorMessages, $infoMessages);
  }


  public function setCustomData($url, $customData) {
    $this->customDataByUrl[$url] = $customData;
  }


  public function popCustomData($url, $defaultValue = NULL) {
    if (array_key_exists($url, $this->customDataByUrl)) {
      $customData = $this->customDataByUrl[$url];
      unset($this->customDataByUrl[$url]);
      return $customData;
    } else {
      return $defaultValue;
    }
  }

}

?>
