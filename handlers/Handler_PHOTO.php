<?

class Handler_PHOTO {

  public static function uploadPhotoHandler($pageRequest) {
    $gearId       = -1;
    $activityId   = -1;
    $diaryId      = -1;

    $gearRow      = NULL;
    $activityRow  = NULL;
    $diaryRow     = NULL;

    $getParameters = array();

    // photo for gear {{{
    if ((isset($_GET['gearId'])) && (is_numeric($_GET['gearId']))) {
      $gearRow = DBMethods::getGearRow($pageRequest->db, $pageRequest->sessionData->userId, $_GET['gearId']);
      if ($gearRow !== NULL) {
        $gearId = $gearRow['id'];
        $getParameters[] = 'gearId=' . $gearId;
      }
    }
    // }}}

    // photo for activity {{{
    if ((isset($_GET['activityId'])) && (is_numeric($_GET['activityId']))) {
      $activityRow = DBMethods::getActivityRow($pageRequest->db, $pageRequest->sessionData->userId, $_GET['activityId']);
      if ($activityRow !== NULL) {
        $activityId = $activityRow['id'];
        $getParameters[] = 'activityId=' . $activityId;
      }
    }
    // }}}

    // photo for diary {{{
    if ((isset($_GET['diaryId'])) && (is_numeric($_GET['diaryId']))) {
      $diaryRow = DBMethods::getDiaryRow($pageRequest->db, $pageRequest->sessionData->userId, $_GET['diaryId']);
      if ($diaryRow !== NULL) {
        $diaryId = $diaryRow['id'];
        $getParameters[] = 'diaryId=' . $diaryId;
      }
    }
    // }}}

    if (count($getParameters) == 0) {
      return HandlersConstants::HS_NOT_FOUND;
    }

    $form = new Form('fUploadPhoto', './uploadPhoto?' . implode('&', $getParameters), t::m('LABEL_UPLOAD_PHOTO'), array(
      new Field_File(
        'fPhoto',                                                                                       // $name
        t::m('PAGE_UPLOAD_PHOTO_PHOTO'),                                                                // $label
        True                                                                                            // $isRequired
      ),
      new Field_String(
        'fDescription',                                                                                 // $name
        t::m('LABEL_DESCRIPTION'),                                                                      // $label
        False,                                                                                          // $isRequired
        ''                                                                                              // $initialValue
      ),
      new Field_DateTime(
        'fTakenTime',                                                                                   // $name
        t::m('PAGE_UPLOAD_PHOTO_TAKEN_TIME'),                                                           // $label
        False,                                                                                          // $isRequired
        ''                                                                                              // $initialValue
      ),
      new Field_String(
        'fLatitudeLongitude',                                                                           // $name
        t::m('PAGE_EDIT_ACTIVITY_LATITUDE_LONGITUDE'),                                                  // $label
        False,                                                                                          // $isRequired
        ''                                                                                              // $initialValue
      ),
      new Field_Select(
        'fIsDedault',                                                                                   // $name
        t::m('LABEL_IS_DEFAULT'),                                                                       // $label
        True,                                                                                           // $isRequired
        0,                                                                                              // $initialValue
        FormMethods::getYesNoItems()                                                                    // $optionsDict
      ),
      new Field_Select(
        'fRating',                                                                                      // $name
        t::m('PAGE_UPLOAD_PHOTO_RATING'),                                                               // $label
        False,                                                                                          // $isRequired
        Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX'],                                                           // $initialValue
        FormMethods::getPhotoRatingItems(True)                                                          // $optionsDict
      ),
      new Field_HiddenId(
        'fGearId',                                                                                      // $name
        $gearId                                                                                         // $initialValue
      ),
      new Field_HiddenId(
        'fActivityId',                                                                                  // $name
        $activityId                                                                                     // $initialValue
      ),
      new Field_HiddenId(
        'fDiaryId',                                                                                     // $name
        $diaryId                                                                                        // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();
      // prepare and validate data {{{
      if (count($form->errorMessagesByField) == 0) {
        $description                = $form->getFieldValue('fDescription');
        $rating                     = $form->getFieldValueOrNULLIfEmpty('fRating');
        $isDefault                  = $form->getFieldValue('fIsDedault');
        list($latitude, $longitude) = ConversionMethods::stringToLatitudeLongintude($form->getFieldValueOrNULLIfEmpty('fLatitudeLongitude'));
        if (($form->getFieldValue('fLatitudeLongitude') != '') && ($latitude === NULL)) {
          FormMethods::addFormErrorMessage($form->errorMessagesByField, 'fLatitudeLongitude', t::m('FORM_FIELD_BAD_FORMAT'));
        }
        $takenTime                  = $form->getFieldValueOrNULLIfEmpty('fTakenTime');
        if ($takenTime !== NULL) {
          $takenTime = strtotime($takenTime);
        }
        if ($_FILES["fPhoto"]["error"] > 0) {
          FormMethods::addFormErrorMessage($form->errorMessagesByField, 'fPhoto', t::m('PAGE_UPLOAD_PHOTO_UPLOAD_ERROR') . ' (' . t::m('FILE_UPLOAD_ERRORS')[$_FILES["fPhoto"]["error"]] . ')');
        } else {
          $check = getimagesize($_FILES["fPhoto"]["tmp_name"]);
          if ($check === FALSE) {
            FormMethods::addFormErrorMessage($form->errorMessagesByField, 'fPhoto', t::m('PAGE_UPLOAD_PHOTO_ERROR_FILE_IS_NO_IMAGE'));
          }
        }
      }
      // }}}
      // register photo {{{
      if (count($form->errorMessagesByField) == 0) {
        // XXX: todo shared photo

        // gear photo {{{
        if ($gearRow !== NULL) {
          $photosDictionary = PhotosMethods::determinePhotosDictionary('gear', $pageRequest->sessionData->userId);
          $fileName = PhotosMethods::determineGearPhotoFileName($photosDictionary, $gearRow);
          move_uploaded_file($_FILES["fPhoto"]['tmp_name'], $fileName);
          PhotosMethods::createThumbnail($fileName);
          // XXX: todo resize image?

          if (!$takenTime) {
            $exif = exif_read_data($fileName);
            if (($exif !== FALSE) && (array_key_exists('DateTimeOriginal', $exif))) {
              $takenTime = strtotime($exif['DateTimeOriginal']);
            }
          }

          DBMethods::registerPhoto_Gear(
            $pageRequest->db,                       // $db
            $gearRow['id'],                         // $gearId
            $fileName,                              // $fileName
            $description,                           // $description
            $rating,                                // $rating
            $isDefault,                             // $isDefault
            $latitude,                              // $latitude
            $longitude,                             // $longitude
            $takenTime                              // $takenTime
          );
          AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
        }
        // }}}

        // activity photo {{{
        if ($activityRow !== NULL) {
          $photosDictionary = PhotosMethods::determinePhotosDictionary(date('Y', $activityRow['startTime']), $pageRequest->sessionData->userId);
          $fileName = PhotosMethods::determineActivityPhotoFileName($photosDictionary, $activityRow);
          move_uploaded_file($_FILES["fPhoto"]['tmp_name'], $fileName);
          PhotosMethods::createThumbnail($fileName);
          // XXX: todo resize image?

          if (!$takenTime) {
            $exif = exif_read_data($fileName);
            if (($exif !== FALSE) && (array_key_exists('DateTimeOriginal', $exif))) {
              $takenTime = strtotime($exif['DateTimeOriginal']);
            }
          }

          DBMethods::registerPhoto_Activity(
            $pageRequest->db,                       // $db
            $activityRow['id'],                     // $activityId
            $fileName,                              // $fileName
            $description,                           // $description
            $rating,                                // $rating
            $isDefault,                             // $isDefault
            $latitude,                              // $latitude
            $longitude,                             // $longitude
            $takenTime                              // $takenTime
          );
          AuxiliaryMethods::temporaryRedirect($pageRequest, './activity?id=' . $activityRow['id']);
        }
        // }}}
      }
      // }}}
      // }}}
    }

    $tp = array();
    $tp['FORM']         = $form;
    $tp['GEAR_ROW']     = $gearRow;
    $tp['ACTIVITY_ROW'] = $activityRow;
    $tp['DIARY_ROW']    = $diaryRow;

    $page = new Page($pageRequest, array('pages/uploadPhoto.php'), $tp, t::m('PAGE_UPLOAD_PHOTO_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function ratePhotoHandler($pageRequest) {
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) &&
        (isset($_GET['backURI'])) &&
        (isset($_GET['rating'])) && (is_numeric($_GET['rating'])) && (in_array($_GET['rating'], array(1, 2, 3, 4, 5)))) {
      $photoId = $_GET['id'];
      $rating  = $_GET['rating'];
      $backURI = urldecode($_GET['backURI']);

      $photoRow = DBMethods::getPhotoRowById($pageRequest->db, $photoId);
      if ($photoRow) {
        DBMethods::ratePhoto($pageRequest->db, $photoId, $rating);
      }

      AuxiliaryMethods::temporaryRedirect($pageRequest, $backURI);
    }

    $tp = array();
    $tp['RESULT_TEXT'] = t::m('ERROR_INVALID_INPUT');

    $page = new Page($pageRequest, array('pages/resultError.php'), $tp, t::m('LABEL_ERROR'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function removePhotoHandler($pageRequest) {
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) &&
        (isset($_GET['backURI']))) {
      $photoId = $_GET['id'];
      $backURI = urldecode($_GET['backURI']);

      $photoRow = DBMethods::getPhotoRowById($pageRequest->db, $photoId);
      if ($photoRow) {
        PhotosMethods::removePhotoWithThumbnail($photoRow['fileName']);
        DBMethods::removePhoto($pageRequest->db, $photoId);
      }

      AuxiliaryMethods::temporaryRedirect($pageRequest, $backURI);
    }

    $tp = array();
    $tp['RESULT_TEXT'] = t::m('ERROR_INVALID_INPUT');

    $page = new Page($pageRequest, array('pages/resultError.php'), $tp, t::m('LABEL_ERROR'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }

}
?>
