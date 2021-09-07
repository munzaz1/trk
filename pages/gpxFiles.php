<div class="pageContent">

  <h3><?=t::m('PAGE_GPX_FILES_TITLE')?></h3>

  <p>
  <?
  $fullPath = array();
  foreach ($tp['GPX_PATH'] as $part) {
    $fullPath[] = $part;
    ?>
    &raquo;&nbsp;<a href="gpx-files?dir=<?=implode(DIRECTORY_SEPARATOR, $fullPath)?>"><?=$part?></a>
    <?
  }
  ?>
  </p>

  <p>
  <?
  if (count($tp['GPX_PATH']) > 1) {
    ?>
    <a href="gpx-files?dir=<?=implode(DIRECTORY_SEPARATOR, array_slice($tp['GPX_PATH'], 0, -1))?>">..</a><br />
    <?
  }
  foreach ($tp['GPX_DIRS'] as $name => $path) {
    ?>
    &#128448; <a href="gpx-files?dir=<?=$path?>"><?=$name?></a><br />
    <?
  }
  ?>
  </p>

  <p>
  <?
  foreach ($tp['GPX_FILES'] as $fileShortName => $fileData) {
    list($fileName, $isLoaded) = $fileData;
    if ($isLoaded) {
      ?>
      &#10003;
      <?
    } else {
      foreach (ActivitiesConstants::$SUPPORTED_GPX_FILE_PROCESSORS as $processor) {
        $processorName = ActivitiesConstants::$GPX_FILE_PROCESSORS_NAMES[$processor];
        ?>
        <a href="load-gpx?fileName=<?=$fileName?>&amp;processor=<?=$processorName?>"><?=t::m('LABEL_LOAD')?> (<?=$processorName?>)</a>
        &nbsp;
        <?
      }
    }
    ?>
    &nbsp;
    <?=$fileShortName?>
    <br />
    <?
  }
  ?>
  </p>

</div>
