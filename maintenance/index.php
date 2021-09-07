<?php
  session_start();

  const SCRIPT_NAME = 'index.php';

  const STATUS_ERROR = 0;
  const STATUS_OK    = 1;

  $status = STATUS_OK;

  if (file_exists('../Settings.php')) {
    include '../Settings.php';
  } else {
    include 'Settings.php';
    $status = STATUS_ERROR;
  }

  include '../language/' . Settings::$S['LANGUAGE'] . '.php';
  include '../language/en.php';

  include '../utils/Translation.php';

  include '../consts/AboutConstants.php';
  include '../consts/ClientBuildConstants.php';
  include '../consts/SettingsConstants.php';

  include '../utils/DBMethods.php';
  include '../utils/AuxiliaryMethods.php';
  include '../utils/Exceptions.php';
  include '../utils/DBConnector.php';
  include '../utils/SettingsMethods.php';

  $tp = array(
    'SCRIPT_NAME'           => SCRIPT_NAME,
    'PAGE_TITLE'            => t::m('PAGE_MAINTENANCE_TITLE'),
    'PAGE_KEYWORDS'         => '',
    'PAGE_DESCRIPTION'      => '',
    'STYLE_PATH'            => '../',
    'MAIN_PAGE_URI'         => '../',
    'ERROR_MESSAGE'         => '',
    'NOTIFICATION_MESSAGE'  => '',
    'MESSAGE'               => '',
  );

  // missing config file {{{
  if ($status == STATUS_ERROR) {
    $tp['ERROR_MESSAGE'] = t::m('PAGE_MAINTENANCE_SETTINGS_FILE_IS_MISSING');
  }
  // }}}

  //  check settings keys {{{
  if ($status == STATUS_OK) {
    try {
      SettingsMethods::checkSettings();
    } catch (Exception $e) {
      $tp['ERROR_MESSAGE'] = $e->getMessage();
      $status = STATUS_ERROR;
    }
  }
  // }}}


  // database {{{
  if ($status == STATUS_OK) {
    try {
      $db = new DBConnector(
        Settings::$S['DB_SERVER'],
        Settings::$S['DB_USER'],
        Settings::$S['DB_PASSWORD'],
        Settings::$S['DB_DATABASE']
      );

      // there is no tables yet {{{
      $query = "SHOW TABLES LIKE \"" . Settings::$S['DB_TABLE_PREFIX'] . "maintenance\";";
      $tablesRows = $db->rows($query);
      if (count($tablesRows) == 0) {
        $tp['ERROR_MESSAGE'] = '<a href="' . $tp['SCRIPT_NAME'] . '?initDatabase=1">' . t::m('PAGE_MAINTENANCE_INIT_DATABASE') . '</a>';
        $status = STATUS_ERROR;
      }
      // }}}

      // TODO: check MAINTENANCE_PHASE

      // actions {{{
      if ((isset($_GET['initDatabase'])) && ($_GET['initDatabase'] == 1)) {
        $sql = file_get_contents('init_db.sql');
        $sql = str_replace('%%TABLE_PREFIX%%', Settings::$S['DB_TABLE_PREFIX'], $sql);
        $db->multiQuery($sql);

        $_SESSION['NOTIFICATION_MESSAGE'] = t::m('PAGE_MAINTENANCE_DATABASE_INITIALIZED');
        AuxiliaryMethods::TemporaryRedirectSimple(SCRIPT_NAME);
      }
      // }}}

      $db->close();
    } catch (Exception $e) {
      $tp['ERROR_MESSAGE'] = t::m('PAGE_MAINTENANCE_INVALID_DB_SETTINGS') . '<br /><br />' . $e->getMessage();
      $status = STATUS_ERROR;
    }
  }
  // }}}

  if ($status == STATUS_OK) {
    $tp['MESSAGE'] = t::m('PAGE_MAINTENANCE_NO_MAINTENANCE_NEEDED');
  }


  if (isset($_SESSION['NOTIFICATION_MESSAGE'])) {
    $tp['NOTIFICATION_MESSAGE'] = $_SESSION['NOTIFICATION_MESSAGE'];
    unset($_SESSION['NOTIFICATION_MESSAGE']);
  }

  require '../pages/header.php';
  require '../pages/maintenance.php';
  require '../pages/footer.php';

?>
