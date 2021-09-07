<?

class SimplePage {

  /**
    @param fileNames List of names of the template files
    @param tp Page data (array)
    @param pageTitle (optional) Title of the page
    @param pageDescription (optional) Description of the page
    @param pageKeywords (optional) Keywords of the page
    */
  function __construct($fileNames, $tp, $pageTitle = AboutConstants::APPLICATION_NAME, $pageDescription = '', $pageKeywords = '') {
    $this->fileNames  = $fileNames;
    $this->tp         = $tp;

    $this->tp['PAGE_TITLE']       = t::m('PAGE_TITLE_PREFIX') . $pageTitle;
    $this->tp['PAGE_KEYWORDS']    = $pageKeywords;
    $this->tp['PAGE_DESCRIPTION'] = $pageDescription;
    $this->tp['STYLE_PATH']       = './';
  }


  public function printPage() {
    $tp = $this->tp;
    require 'pages/header.php';
    foreach ($this->fileNames as $fileName) {
      require $fileName;
    }
    require 'pages/footer.php';
  }

}


class Page extends SimplePage {

  /**
    @param pageRequest Instance of PageRequest()
    @param fileNames List of names of the template files
    @param tp Page data (array)
    @param pageTitle (optional) Title of the page
    @param pageDescription (optional) Description of the page
    @param pageKeywords (optional) Keywords of the page
    */
  function __construct($pageRequest, $fileNames, $tp, $pageTitle = AboutConstants::APPLICATION_NAME, $pageDescription = '', $pageKeywords = '') {
    parent::__construct($fileNames, $tp, $pageTitle, $pageDescription, $pageTitle, $pageKeywords);

    $this->pageRequest = $pageRequest;
  }


  public function printPage() {
    $tp = $this->tp;

    list($errorMessages, $infoMessages) = $this->pageRequest->sessionData->popMessages();

    $tp['GLOBAL_SETTINGS']              = $this->pageRequest->globalSettings;
    $tp['IS_LOGGED_IN']                 = ($this->pageRequest->sessionData->userId !== NULL);
    $tp['URL_ID']                       = $this->pageRequest->urlId;
    $tp['NOTIFICATION_ERROR_MESSAGES']  = $errorMessages;
    $tp['NOTIFICATION_INFO_MESSAGES']   = $infoMessages;


    require 'pages/header.php';
    require 'pages/pageHeader.php';
    foreach ($this->fileNames as $fileName) {
      require $fileName;
    }
    require 'pages/pageFooter.php';
    require 'pages/footer.php';
  }

}

?>
