<div class="pageContent">

  <h2><?=t::m('PAGE_IMPORT_CSV_TITLE')?></h2>

  <h3><?=t::m('PAGE_IMPORT_CSV_ACTIVITIES')?></h3>

  <?
  if (count($tp['REGISTERED_ACTIVITIES_ROWS']) > 0) {
    ?>
    <div class="notificationMessageInfo">
    <ul>
      <?
      foreach ($tp['REGISTERED_ACTIVITIES_ROWS'] as $activityRow) {
        ?>
        <li><a href="activity?id=<?=$activityRow['id']?>"><?=$activityRow['title']?> (<?=TextFormattingMethods::formatDateTime($activityRow['startTime'])?>)</a></li>
        <?
      }
      ?>
    </ul>
    </div>
    <?
  }
  ?>


  <form method="post" action="./import-csv" enctype="multipart/form-data">
    <div class="shortFormFieldRow">
      <input type="file" name="fActivitiesCSVFile" />
    </div>

    <div class="shortFormFieldRow">
      <div class="sendButton">
        <input type="submit" value="<?=t::m('LABEL_UPLOAD')?>" name="fUpload" />
      </div>
    </div>
  </form>

  <div class="clear">&nbsp;</div>

  <p>
    <?=t::m('PAGE_IMPORT_CSV_ACTIVITIES_HELP')?>
  </p>

</div>
