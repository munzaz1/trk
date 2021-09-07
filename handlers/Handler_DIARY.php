<?

class Handler_DIARY {

  public static function diaryHandler($pageRequest) {
    $month  = date('n');
    $year   = date('Y');

    if (isset($_GET['month'])) {
      if ((is_numeric($_GET['month'])) && ($_GET['month'] >= 1) && ($_GET['month'] <= 12)) {
        $month = $_GET['month'];
      } else {
        AuxiliaryMethods::temporaryRedirect($pageRequest, './diary');
      }
    }

    if (isset($_GET['year'])) {
      if ((is_numeric($_GET['year'])) && ($_GET['year'] >= Settings::$S['MIN_YEAR']) && ($_GET['year'] <= $year)) {
        $year = $_GET['year'];
      } else {
        AuxiliaryMethods::temporaryRedirect($pageRequest, './diary');
      }
    }

    // remove diary item {{{
    if ((isset($_GET['remove'])) && (is_numeric($_GET['remove']))) {
      DBMethods::removeDiaryItem($pageRequest->db, $pageRequest->sessionData->userId, $_GET['remove']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './diary');
    }
    // }}}


    list($minYearA, $maxYearA) = DBMethods::getMinMaxActivityYears($pageRequest->db, $pageRequest->sessionData->userId);
    list($minYearD, $maxYearD) = DBMethods::getMinMaxDiaryYears($pageRequest->db, $pageRequest->sessionData->userId);
    $years = AuxiliaryMethods::getYearsListFromMinMax(min($minYearA, $minYearD), max($maxYearA, $maxYearD));

    // data for calendar {{{
    $monthActivities = DBMethods::getMonthActivitiesByMonthYear($pageRequest->db, $pageRequest->sessionData->userId, $month, $year);
    $monthDiaryData  = DBMethods::getDiaryDataByMonthYear($pageRequest->db, $pageRequest->sessionData->userId, $month, $year);
    // }}}

    $activityIds = array();
    foreach ($monthActivities as $day => $activitiesRows) {
      foreach ($activitiesRows as $activityRow) {
        $activityIds[] = $activityRow['id'];
      }
    }
    $monthPhotos = DBMethods::getPhotosForActivities($pageRequest->db, $activityIds);

    $tp = array();
    $tp['MONTH']            = $month;
    $tp['YEAR']             = $year;
    $tp['YEARS']            = $years;
    $tp['MONTH_ACTIVITIES'] = $monthActivities;
    $tp['MONTH_DIARY_DATA'] = $monthDiaryData;
    $tp['MONTH_PHOTOS']     = $monthPhotos;
    $tp['CURRENT_MONTH']    = date('n');
    $tp['CURRENT_YEAR']     = date('Y');

    $page = new Page($pageRequest, array('pages/diary.php'), $tp, t::m('LABEL_DIARY'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function addOrEditDiaryItemHandler($pageRequest) {
    $diaryRow = NULL;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
      $diaryId = $_GET['id'];
      $diaryRow = DBMethods::getDiaryRow($pageRequest->db, $pageRequest->sessionData->userId, $diaryId);
      if ($diaryRow === NULL) return HandlersConstants::HS_NOT_FOUND;
    }

    // add diary item {{{
    if ($diaryRow === NULL) {
      $actionAdd         = True;
      $submitButtonTitle = t::m('LABEL_CREATE');
      $pageHeader        = t::m('PAGE_DIARY_ADD_ITEM');

      $diaryMonth        = date('n');
      $diaryYear         = date('Y');

      $fDiaryId          = -1;
      $fCategoryId       = Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX'];
      $fEventTime        = TextFormattingMethods::formatDateTime(time());
      $fText             = '';
    // }}}
    // edit diary item {{{
    } else {
      $actionAdd         = False;
      $submitButtonTitle = t::m('LABEL_SAVE');
      $pageHeader        = t::m('PAGE_DIARY_EDIT_ITEM');

      $diaryMonth        = date('n', $diaryRow['eventTime']);
      $diaryYear         = date('Y', $diaryRow['eventTime']);

      $fDiaryId          = $diaryRow['id'];
      $fCategoryId       = $diaryRow['categoryId'];
      $fEventTime        = TextFormattingMethods::formatDateTime($diaryRow['eventTime']);
      $fText             = $diaryRow['text'];
    }
    // }}}

    $form = new Form('fAddOrEditDiaryItem', './diary-ae', $submitButtonTitle, array(
      new Field_Select(
        'fCategory',                                                                                    // $name
        t::m('LABEL_CATEGORY'),                                                                         // $label
        True,                                                                                           // $isRequired
        $fCategoryId,                                                                                   // $initialValue
        DBMethods::getDiaryCategoriesDictForSelect($pageRequest->db, $pageRequest->sessionData->userId) // $optionsDict
      ),
      new Field_DateTime(
        'fEventTime',                                                                                   // $name
        t::m('LABEL_DATE'),                                                                             // $label
        True,                                                                                           // $isRequired
        $fEventTime                                                                                     // $initialValue
      ),
      new Field_String(
        'fText',                                                                                        // $name
        t::m('PAGE_DIARY_TEXT'),                                                                        // $label
        False,                                                                                          // $isRequired
        $fText                                                                                          // $initialValue
      ),
      new Field_HiddenId(
        'fDiaryId',                                                                                     // $name
        $fDiaryId                                                                                       // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();

      if (count($form->errorMessagesByField) == 0) {
        $fCategoryId = $form->getFieldValue('fCategory');
        $fEventTime  = strtotime($form->getFieldValue('fEventTime'));
        $fText       = $form->getFieldValue('fText');
        $fDiaryId    = $form->getFieldValue('fDiaryId');

        if ($fDiaryId == -1) {
          DBMethods::registerDiaryItem($pageRequest->db, $pageRequest->sessionData->userId, $fCategoryId, $fEventTime, $fText);
        } else {
          DBMethods::updateDiaryItem($pageRequest->db, $pageRequest->sessionData->userId, $fDiaryId, $fCategoryId, $fEventTime, $fText);
        }

        $month  = date('n', $fEventTime);
        $year   = date('Y', $fEventTime);
        AuxiliaryMethods::temporaryRedirect($pageRequest, './diary?month=' . $month . '&year=' . $year);
      }
    }
    // }}}


    $tp = array();
    $tp['FORM']         = $form;
    $tp['PAGE_HEADER']  = $pageHeader;
    $tp['DIARY_ID']     = $fDiaryId;
    $tp['DIARY_MONTH']  = $diaryMonth;
    $tp['DIARY_YEAR']   = $diaryYear;

    $page = new Page($pageRequest, array('pages/addOrEditDiaryItem.php'), $tp, t::m('LABEL_DIARY'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function diaryCategoriesHandler($pageRequest) {
    $diaryCategoriesRowsWithDiaryItems = DBMethods::getDiaryCategoriesRowsWithDiaryItems($pageRequest->db, $pageRequest->sessionData->userId);

    // remove diary category {{{
    if ((isset($_GET['remove'])) && (is_numeric($_GET['remove']))) {
      DBMethods::removeDiaryCategory($pageRequest->db, $pageRequest->sessionData->userId, $_GET['remove']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './diary-categories');
    }
    // }}}

    $tp = array();
    $tp['DIARY_CATEGORIES_ROWS'] = $diaryCategoriesRowsWithDiaryItems;

    $page = new Page($pageRequest, array('pages/diaryCategories.php'), $tp, t::m('PAGE_DIARY_CATEGORIES_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function addOrEditDiaryCategory($pageRequest) {
    $diaryCategoryRow = NULL;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
      $diaryCategoryId = $_GET['id'];
      $diaryCategoryRow = DBMethods::getDiaryCategoryRow($pageRequest->db, $pageRequest->sessionData->userId, $diaryCategoryId);
      if ($diaryCategoryRow === NULL) return HandlersConstants::HS_NOT_FOUND;
    }

    // add diary item {{{
    if ($diaryCategoryRow === NULL) {
      $actionAdd         = True;
      $submitButtonTitle = t::m('LABEL_CREATE');
      $pageHeader        = t::m('PAGE_DIARY_CATEGORIES_ADD_CATEGORY');

      $fDiaryCategoryId  = -1;
      $fTitle            = '';
    // }}}
    // edit diary item {{{
    } else {
      $actionAdd         = False;
      $submitButtonTitle = t::m('LABEL_SAVE');
      $pageHeader        = t::m('PAGE_DIARY_CATEGORIES_EDIT_CATEGORY');

      $fDiaryCategoryId  = $diaryCategoryRow['id'];
      $fTitle            = $diaryCategoryRow['title'];
    }
    // }}}

    $form = new Form('fAddOrEditDiaryItem', './diary-category-ae', $submitButtonTitle, array(
      new Field_String(
        'fTitle',                                                                                       // $name
        t::m('LABEL_TITLE'),                                                                            // $label
        True,                                                                                           // $isRequired
        $fTitle                                                                                         // $initialValue
      ),
      new Field_HiddenId(
        'fDiaryCategoryId',                                                                             // $name
        $fDiaryCategoryId                                                                               // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();

      if (count($form->errorMessagesByField) == 0) {
        $fTitle           = $form->getFieldValue('fTitle');
        $fDiaryCategoryId = $form->getFieldValue('fDiaryCategoryId');

        if ($fDiaryCategoryId == -1) {
          DBMethods::registerDiaryCategory($pageRequest->db, $pageRequest->sessionData->userId, $fTitle);
        } else {
          DBMethods::updateDiaryCategory($pageRequest->db, $pageRequest->sessionData->userId, $fDiaryCategoryId, $fTitle);
        }
        AuxiliaryMethods::temporaryRedirect($pageRequest, './diary-categories');
      }
    }
    // }}}


    $tp = array();
    $tp['FORM']               = $form;
    $tp['PAGE_HEADER']        = $pageHeader;
    $tp['DIARY_CATEGORY_ID']  = $fDiaryCategoryId;

    $page = new Page($pageRequest, array('pages/addOrEditDiaryCategory.php'), $tp, $pageHeader);
    $page->printPage();

    return HandlersConstants::HS_OK;
  }

}
?>
