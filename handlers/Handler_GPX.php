<?

class Handler_GPX {

  public static function gpxFilesHandler($pageRequest) {

    if ((isset($_GET['dir'])) && (file_exists($_GET['dir']))) {
      $gpxCurrentDir = $_GET['dir'];
    } else {
      $gpxCurrentDir = Settings::$S['GPX_DIR'];
    }
    if (substr($gpxCurrentDir, 0, strlen(Settings::$S['GPX_DIR'])) !== Settings::$S['GPX_DIR']) {
      $pageRequest->sessionData->addErrorMessage(t::m('ERROR_INVALID_INPUT'));
      $gpxCurrentDir = Settings::$S['GPX_DIR'];
    }

    $gpxPath  = explode(DIRECTORY_SEPARATOR, $gpxCurrentDir);
    $gpxFiles = array();
    $gpxDirs  = array();

    $lastGPXFilesNames = DBMethods::getLastGPXFilesNames($pageRequest->db, $pageRequest->sessionData->userId);

    $cdir = scandir($gpxCurrentDir);
    foreach ($cdir as $key => $value) {
      if (!in_array($value,array(".", ".."))) {
         $fileName = $gpxCurrentDir . DIRECTORY_SEPARATOR . $value;
         if (is_dir($fileName)) {
            $gpxDirs[$value] = $fileName;
         } else {
            $gpxFiles[$value] = array($fileName, in_array($fileName, $lastGPXFilesNames));
         }
      }
   }

    $tp = array();
    $tp['GPX_FILES'] = $gpxFiles;
    $tp['GPX_DIRS']  = $gpxDirs;
    $tp['GPX_PATH']  = $gpxPath;

    $homePage = new Page($pageRequest, array('pages/gpxFiles.php'), $tp, t::m('PAGE_GPX_FILES_TITLE'));
    $homePage->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function loadGPXHandler($pageRequest) {

    try {
      // check file name {{{
      if (!isset($_GET['fileName'])) throw new ErrorInvalidInput(t::m('ERROR_INVALID_INPUT'));
      $gpxFileName = $_GET['fileName'];
      if (!file_exists($gpxFileName)) throw new ErrorInvalidInput(sprintf(t::m('ERROR_GPX_FILE_NOT_FOUND'), $gpxFileName));
      // }}}

      $activitiesRows = DBMethods::getActivityIdsTitlesByGPXFileName($pageRequest->db, $pageRequest->sessionData->userId, $gpxFileName);
      if (count($activitiesRows) > 0) {
        throw new ErrorDuplicity(t::m('ERROR_GPX_FILE_ALREADY_IN_SYSTEM') . ' (' . implode(', ', TextFormattingMethods::getActivityLinksByIds($activitiesRows)) . ')');
      }

      // check processor {{{
      if (!isset($_GET['processor'])) throw new ErrorInvalidInput(t::m('ERROR_INVALID_INPUT'));
      $gpxFileProcessorName = $_GET['processor'];
      if (!in_array($gpxFileProcessorName, ActivitiesConstants::$GPX_FILE_PROCESSORS_NAMES)) throw new ErrorInvalidInput(t::m('ERROR_INVALID_INPUT'));
      // }}}

      // try to load the base data from the given GPX file {{{
      list($type, $startTime, $title) = GPXMethods::getBaseDataFromGPXFile($gpxFileName);
      if (($startTime === False) || ($title === False)) {
        throw new ErrorInvalidFormat(sprintf(t::m('ERROR_GPX_FILE_INVALID_FORMAT'), $gpxFileName));
      }
      // }}}

      $activitiesRows = DBMethods::getActivityIdsTitlesByStartTime($pageRequest->db, $pageRequest->sessionData->userId, $startTime);
      if (count($activitiesRows) > 0) {
        throw new ErrorDuplicity(t::m('ERROR_DUPLICATE_ACTIVITY') . ' (' . implode(', ', TextFormattingMethods::getActivityLinksByIds($activitiesRows)) . ')');
      }

      // PHP GPX processor {{{
      if ($gpxFileProcessorName == ActivitiesConstants::$GPX_FILE_PROCESSORS_NAMES[ActivitiesConstants::GFP_PHP]) {
        $activityStats  = GPXMethods::getActivityStatsFromGPX($gpxFileName);
        $polyline       = '';
        $gearId         = NULL;
        $esType         = ExternalSourcesConstants::EST_NONE;
        $esId           = '';
        $description    = '';
      // }}}
      // Strava GPX processor {{{
      } elseif ($gpxFileProcessorName == ActivitiesConstants::$GPX_FILE_PROCESSORS_NAMES[ActivitiesConstants::GFP_STRAVA]) {
        $stravaConnector = new StravaConnector();
        list($type, $gearId, $esType, $esId, $startTime, $title, $description, $activityStats, $polyline) =
          $stravaConnector->getActivityDataByStartTime($pageRequest->db, $pageRequest->sessionData->userId, $startTime);
      // }}}
      } else {
        throw new ErrorInvalidInput(t::m('ERROR_INVALID_INPUT'));
      }

      //$activityStats->print();
      //exit();

      $activityId = DBMethods::registerActivity(
        $pageRequest->db,                     // $db
        $pageRequest->sessionData->userId,    // $userId
        $gearId,                              // gearId
        $type,                                // type
        $title,                               // $title
        $description,                         // $description
        $startTime,                           // $startTime
        $gpxFileName,                         // $gpxFileName
        $esType,                              // $esType
        $esId,                                // $esId
        $activityStats,                       // $activityStats
        $polyline                             // $polyline
      );

      AuxiliaryMethods::temporaryRedirect($pageRequest, './activity?id=' . $activityId);

    } catch (ErrorInvalidInput $e) {
      $pageRequest->sessionData->addErrorMessage($e->getMessage());
    } catch (ErrorInvalidFormat $e) {
      $pageRequest->sessionData->addErrorMessage($e->getMessage());
    } catch (ErrorDuplicity $e) {
      $pageRequest->sessionData->addErrorMessage($e->getMessage());
    }

    $tp = array();

    $homePage = new Page($pageRequest, array('pages/loadGPX.php'), $tp, t::m('PAGE_LOAD_GPX_TITLE'));
    $homePage->printPage();

    return HandlersConstants::HS_OK;
  }

}

?>
