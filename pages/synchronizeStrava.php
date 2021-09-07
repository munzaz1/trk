<div class="pageContent">

  <?
  if ($tp['SYNCHRONIZATION_DONE'] === TRUE) {
    ?>
    <div class="notificationMessageInfo">
    <p>
      <?=sprintf(t::m('PAGE_SYNCHRONIZE_STRAVA_REPORT'),
                 $tp['VALUE_BY_FIELD']['fTimeFrom'],
                 $tp['VALUE_BY_FIELD']['fTimeTo'],
                 $tp['REGISTERED_COUNT'],
                 $tp['SKIPPED_COUNT'])?>
    </p>
    <?
    if (count($tp['REGISTERED_ACTIVITIES_ROWS']) > 0) {
      ?>
      <ul>
        <?
        foreach ($tp['REGISTERED_ACTIVITIES_ROWS'] as $activityRow) {
          ?>
          <li><a href="activity?id=<?=$activityRow['id']?>"><?=$activityRow['title']?> (<?=TextFormattingMethods::formatDateTime($activityRow['startTime'])?>)</a></li>
          <?
        }
        ?>
      </ul>
      <?
    }
    ?>
    </div>
    <?
  }
  ?>

  <h2><?=t::m('PAGE_SYNCHRONIZE_STRAVA_TITLE')?></h2>

  <p>
    <?=t::m('PAGE_SYNCHRONIZE_STRAVA_NOTE')?>
  </p>

  <form action="./synchronize-strava" method="post">

    <div class="shortFormFieldRow inputLabel_required">
      <?=t::m('PAGE_SYNCHRONIZE_STRAVA_NEW_ACTIVITIES_FROM')?>
    </div>
    <div class="shortFormFieldRow">
      <?=FormMethods::getInputCode_text(
        'fTimeFrom',                    // $name
        $tp['VALUE_BY_FIELD'],          // $valueByField
        $tp['ERROR_MESSAGES_BY_FIELD'], // $errorMessagesByField
        1                               // $isRequired
      )?> (<?=t::m('LABEL_DATE_FORMAT')?>)
    </div>


    <div class="shortFormFieldRow inputLabel_required">
      <?=t::m('PAGE_SYNCHRONIZE_STRAVA_NEW_ACTIVITIES_TO')?>
    </div>
    <div class="shortFormFieldRow">
      <?=FormMethods::getInputCode_text(
        'fTimeTo',                      // $name
        $tp['VALUE_BY_FIELD'],          // $valueByField
        $tp['ERROR_MESSAGES_BY_FIELD'], // $errorMessagesByField
        1                               // $isRequired
      )?> (<?=t::m('LABEL_DATE_FORMAT')?>)
    </div>


    <div class="shortFormFieldRow">
      <div class="sendButton">
        <input type="submit" value="<?=t::m('LABEL_LOAD')?>" />
      </div>
      <div class="clear">&nbsp;</div>
    </div>

    <input type="hidden" name="fLoadSend" value="1" />

  </form>

  <p>
    <a href="./synchronize-strava-auth?action=refresh"><?=t::m('PAGE_SYNCHRONIZE_STRAVA_REFRESH_TOKEN')?></a><br />
    <a href="./synchronize-strava-auth?action=deauthorize"><?=t::m('PAGE_SYNCHRONIZE_STRAVA_DEAUTHORIZE')?></a>
  </p>

</div>
