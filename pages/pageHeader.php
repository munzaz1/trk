  <div id="main">
    <div id="header">
      <div class="headerContent">
        <div class="header_l headerLogo">
          <a href="./"><img src="images/logoWhite.png" title="<?=AboutConstants::APPLICATION_NAME?>" /></a>
        </div>
        <div class="header_l">
          <?
          if ($tp['IS_LOGGED_IN']) {
            function getMenuItem($tp, $urlId, $label) {
              $class = '';
              if ($tp['URL_ID'] == $urlId) {
                $class = 'active';
              }
              return '<a href="./' . $urlId . '" class="' . $class . '">' . $label . '</a>';
            }
            ?>
            <?=getMenuItem($tp, 'diary', t::m('LABEL_DIARY'))?>
            &nbsp;&nbsp;
            <?=getMenuItem($tp, 'summary', t::m('LABEL_SUMMARY'))?>
            &nbsp;&nbsp;
            <?=getMenuItem($tp, 'activity-ae', t::m('LABEL_ADD_ACTIVITY'))?>
            &nbsp;&nbsp;
            <?=getMenuItem($tp, 'stats', t::m('LABEL_STATS'))?>
            &nbsp;&nbsp;
            <?=getMenuItem($tp, 'profile', t::m('PAGE_PROFILE_TITLE'))?>
            &nbsp;&nbsp;
            <?=getMenuItem($tp, 'import-csv', t::m('PAGE_IMPORT_CSV_TITLE'))?>
            &nbsp;&nbsp;
            <img src="images/icons/Synchronize_White.png" alt="<?=t::m('PAGE_SYNCHRONIZE_STRAVA_TITLE')?>" title="<?=t::m('PAGE_SYNCHRONIZE_STRAVA_TITLE')?>"/>
            <a href="./synchronize-strava-auth"><?=t::m('LABEL_STRAVA')?></a>
            <? /*&nbsp;&nbsp;
            <a href="./gpx-files"><?=t::m('LABEL_GPX')?></a>*/ ?>
            <?
            if (Settings::$S['AUTO_LOGIN_USER_ID'] === NULL) {
              ?>
              &nbsp;&nbsp;
              <a href="./logout"><?=t::m('LABEL_LOGOUT')?></a>
              <?
            }
          }
          ?>
        </div>
        <div class="clear">&nbsp;</div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <?
        if (count($tp['NOTIFICATION_INFO_MESSAGES']) > 0) {
          ?>
          <p class="notificationMessageInfo">
            <?=implode('<br />', $tp['NOTIFICATION_INFO_MESSAGES'])?>
          </p>
          <?
        }
        /*if ((array_key_exists('ERROR_MESSAGES_BY_FIELD', $tp)) && (count($tp['ERROR_MESSAGES_BY_FIELD']) > 0)) {
          $tp['NOTIFICATION_ERROR_MESSAGES'][] = t::m('ERROR_FORM_CONTAINS_ERRORS');
        }*/
        if (count($tp['NOTIFICATION_ERROR_MESSAGES']) > 0) {
          ?>
          <p class="notificationMessageError">
            <?=implode('<br />', $tp['NOTIFICATION_ERROR_MESSAGES'])?>
          </p>
          <?
        }
        ?>
