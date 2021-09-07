<div class="pageContent">
  <h2><?=t::m('PAGE_GEAR_TITLE')?>: <?=$tp['PAGE_HEADER']?></h2>

  <?
  HTMLBlockMethods::renderStandardForm($tp['FORM'])
  ?>

  <?
  if ($tp['GEAR_ID'] != -1) {
    if ($tp['CAN_BE_REMOVED']) {
      ?>
      <p>
        <a href="./gear?remove=<?=$tp['GEAR_ID']?>"><?=t::m('LABEL_REMOVE')?></a>
      </p>
      <?
    }
    ?>
    <p>
      <a href="./gear?primary=<?=$tp['GEAR_ID']?>"><?=t::m('PAGE_GEAR_SET_AS_PRIMARY')?></a>
    </p>
    <?
    if ($tp['IS_RETIRED']) {
      ?>
      <p>
        <a href="./gear?notRetired=<?=$tp['GEAR_ID']?>"><?=t::m('PAGE_GEAR_SET_AS_NOT_RETIRED')?></a>
      </p>
      <?
    } else {
      ?>
      <p>
        <a href="./gear?retired=<?=$tp['GEAR_ID']?>"><?=t::m('PAGE_GEAR_SET_AS_RETIRED')?></a>
      </p>
      <?
    }
  }
  ?>

  <p>
    <a href="./gear"><?=t::m('PAGE_GEAR_BACK_TO_GEAR_SUMMARY')?></a>
  </p>

</div>
