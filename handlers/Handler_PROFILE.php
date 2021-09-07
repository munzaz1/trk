<?

class Handler_PROFILE {

  public static function profileHandler($pageRequest) {

    $tp = array();


    $page = new Page($pageRequest, array('pages/profile.php'), $tp, t::m('PAGE_PROFILE_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function weightsHandler($pageRequest) {
    $form = new Form('fAddWeight', './weights', t::m('PAGE_WEIGHTS_ADD_WEIGHT'), array(
      new Field_Float(
        'fWeight',                                                                                      // $name
        t::m('LABEL_WEIHGHT') . ' ' . t::m('LABEL_UNIT_KILOGRAM'),                                     // $label
        True,                                                                                           // $isRequired
        '',                                                                                             // $initialValue
        1,                                                                                              // $minValue
        1000                                                                                            // $maxValue
      ),
      new Field_DateTime(
        'fDate',                                                                                        // $name
        t::m('LABEL_DATE'),                                                                             // $label
        True,                                                                                           // $isRequired
        TextFormattingMethods::formatDate(time())                                                       // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();

      if (count($form->errorMessagesByField) == 0) {
        $measurementTime = strtotime($form->getFieldValue('fDate'));
        $weight          = round($form->getFieldValue('fWeight') * 1000);

        DBMethods::registerWeight($pageRequest->db, $pageRequest->sessionData->userId, $measurementTime, $weight);
        AuxiliaryMethods::temporaryRedirect($pageRequest, './weights');
      }
    }

    // remove weight {{{
    if ((isset($_GET['remove'])) && (is_numeric($_GET['remove']))) {
      DBMethods::removeWeight($pageRequest->db, $pageRequest->sessionData->userId, $_GET['remove']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './weights');
    }
    // }}}

    $tp = array();
    $tp['FORM']         = $form;
    $tp['WEIGHTS_ROWS'] = DBMethods::getWeightsRowsByMeasurementTime($pageRequest->db, $pageRequest->sessionData->userId, 0, time());

    $page = new Page($pageRequest, array('pages/weights.php'), $tp, t::m('PAGE_WEIGHTS_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function gearHandler($pageRequest) {
    // remove gear {{{
    if ((isset($_GET['remove'])) && (is_numeric($_GET['remove']))) {
      DBMethods::removeGear($pageRequest->db, $pageRequest->sessionData->userId, $_GET['remove']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
    }
    // }}}

    // primary gear {{{
    if ((isset($_GET['primary'])) && (is_numeric($_GET['primary']))) {
      DBMethods::setPrimaryGear($pageRequest->db, $pageRequest->sessionData->userId, $_GET['primary']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
    }
    // }}}

    // retired gear {{{
    if ((isset($_GET['retired'])) && (is_numeric($_GET['retired']))) {
      DBMethods::setGearAsRetired($pageRequest->db, $pageRequest->sessionData->userId, $_GET['retired']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
    }
    // }}}

    // retired gear {{{
    if ((isset($_GET['notRetired'])) && (is_numeric($_GET['notRetired']))) {
      DBMethods::setGearAsNotRetired($pageRequest->db, $pageRequest->sessionData->userId, $_GET['notRetired']);
      AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
    }
    // }}}

    $gearRows = DBMethods::getGearRowsWithDistance($pageRequest->db, $pageRequest->sessionData->userId);
    foreach ($gearRows as $index => $gearRow) {
      $gearRows[$index]['photos'] = DBMethods::getPhotosForGear($pageRequest->db, $gearRow['id']);
    }

    $tp = array();
    $tp['GEAR_ROWS']  = $gearRows;

    $page = new Page($pageRequest, array('pages/gear.php'), $tp, t::m('PAGE_GEAR_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }


  public static function addOrEditGearHandler($pageRequest) {
    $gearRow = NULL;
    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
      $gearId = $_GET['id'];
      $gearRow = DBMethods::getGearRow($pageRequest->db, $pageRequest->sessionData->userId, $gearId);
      if ($gearRow === NULL) return HandlersConstants::HS_NOT_FOUND;
    }

    // add gear {{{
    if ($gearRow === NULL) {
      $actionAdd         = True;
      $submitButtonTitle = t::m('LABEL_CREATE');
      $pageHeader        = t::m('PAGE_GEAR_ADD_GEAR');
      $canBeRemoved      = False;
      $isRetired         = False;

      $fGearId           = -1;
      $fPrimary          = 0;
      $fName             = '';
      $fBrand            = '';
      $fModel            = '';
      $fDescription      = '';
      $fType             = GearConstants::GT_OTHER;
      $fWeight           = '';
      $fEsType           = ExternalSourcesConstants::EST_NONE;
      $fEsId             = '';
    // }}}
    // edit gear {{{
    } else {
      $actionAdd         = False;
      $submitButtonTitle = t::m('LABEL_SAVE');
      $pageHeader        = t::m('PAGE_GEAR_EDIT_GEAR');
      $canBeRemoved      = DBMethods::canBeGearRemoved($pageRequest->db, $pageRequest->sessionData->userId, $gearRow['id']);
      $isRetired         = is_numeric($gearRow['retiredTime']);

      $fGearId           = $gearRow['id'];
      $fPrimary          = $gearRow['primary'];
      $fName             = $gearRow['name'];
      $fBrand            = $gearRow['brand'];
      $fModel            = $gearRow['model'];
      $fDescription      = $gearRow['description'];
      $fType             = $gearRow['type'];
      $fWeight           = $gearRow['weight'];
      if (is_numeric($fWeight)) {
        $fWeight = $fWeight / 1000;
        $fWeight = TextFormattingMethods::formatNumber($fWeight);
      }
      $fEsType           = $gearRow['esType'];
      $fEsId             = $gearRow['esId'];
    }
    // }}}

    $form = new Form('fAddOrEditGear', './gear-ae', $submitButtonTitle, array(
      new Field_String(
        'fName',                                                                                        // $name
        t::m('PAGE_GEAR_NAME'),                                                                         // $label
        True,                                                                                           // $isRequired
        $fName                                                                                          // $initialValue
      ),
      new Field_String(
        'fBrand',                                                                                       // $name
        t::m('PAGE_GEAR_BRAND'),                                                                        // $label
        False,                                                                                          // $isRequired
        $fBrand                                                                                         // $initialValue
      ),
      new Field_String(
        'fModel',                                                                                       // $name
        t::m('PAGE_GEAR_MODEL'),                                                                        // $label
        False,                                                                                          // $isRequired
        $fModel                                                                                         // $initialValue
      ),
      new Field_TextArea(
        'fDescription',                                                                                 // $name
        t::m('PAGE_GEAR_DESCRIPTION'),                                                                  // $label
        False,                                                                                          // $isRequired
        $fDescription                                                                                   // $initialValue
      ),
      new Field_Select(
        'fType',                                                                                        // $name
        t::m('PAGE_GEAR_TYPE'),                                                                         // $label
        True,                                                                                           // $isRequired
        $fType,                                                                                         // $initialValue
        FormMethods::getGearTypesItems()                                                                // $optionsDict
      ),
      new Field_Float(
        'fWeight',                                                                                      // $name
        t::m('PAGE_GEAR_WEIGHT'),                                                                       // $label
        False,                                                                                          // $isRequired
        $fWeight                                                                                        // $initialValue
      ),
      new Field_Select(
        'fEsType',                                                                                      // $name
        t::m('PAGE_GEAR_ES_TYPE'),                                                                      // $label
        False,                                                                                          // $isRequired
        $fEsType,                                                                                       // $initialValue
        FormMethods::getESTypeItems()                                                                   // $optionsDict
      ),
      new Field_String(
        'fEsId',                                                                                       // $name
        t::m('PAGE_GEAR_ES_ID'),                                                                       // $label
        False,                                                                                         // $isRequired
        $fEsId                                                                                         // $initialValue
      ),
      new Field_HiddenId(
        'fGearId',                                                                                      // $name
        $fGearId                                                                                        // $initialValue
      ),
    ));

    // handle form submit {{{
    if ($form->isSubmitted()) {
      $form->validateData();

      if (count($form->errorMessagesByField) == 0) {
        $fGearId        = $form->getFieldValue('fGearId');
        // $fPrimary -- will be used initial value
        $fName          = $form->getFieldValue('fName');
        $fBrand         = $form->getFieldValue('fBrand');
        $fModel         = $form->getFieldValue('fModel');
        $fDescription   = $form->getFieldValue('fDescription');
        $fType          = $form->getFieldValue('fType');
        $fWeight        = $form->getFieldValue('fWeight');
        if (!is_numeric($fWeight)) {
          $fWeight = NULL;
        } else {
          $fWeight = round($fWeight * 1000);
        }
        $fEsType        = $form->getFieldValue('fEsType');
        $fEsId          = $form->getFieldValue('fEsId');

        if ($fGearId == -1) {
          DBMethods::registerGear($pageRequest->db, $pageRequest->sessionData->userId,
            $fPrimary,
            $fName,
            $fBrand,
            $fModel,
            $fDescription,
            $fType,
            $fWeight,
            $fEsType,
            $fEsId
          );
        } else {
          DBMethods::updateGear($pageRequest->db, $pageRequest->sessionData->userId, $fGearId,
            $fPrimary,
            $fName,
            $fBrand,
            $fModel,
            $fDescription,
            $fType,
            $fWeight,
            $fEsType,
            $fEsId
          );
        }

        AuxiliaryMethods::temporaryRedirect($pageRequest, './gear');
      }
    }
    // }}}


    $tp = array();
    $tp['FORM']           = $form;
    $tp['PAGE_HEADER']    = $pageHeader;
    $tp['GEAR_ID']        = $fGearId;
    $tp['CAN_BE_REMOVED'] = $canBeRemoved;
    $tp['IS_RETIRED']     = $isRetired;

    $page = new Page($pageRequest, array('pages/addOrEditGear.php'), $tp, t::m('PAGE_GEAR_TITLE'));
    $page->printPage();

    return HandlersConstants::HS_OK;
  }

}
?>
