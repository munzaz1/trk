<div class="pageContent">
  <h2><?=$tp['PAGE_HEADER']?></h2>

  <?
  HTMLBlockMethods::renderStandardForm($tp['FORM'])
  ?>

  <?
  if ($tp['ACTIVITY_ID'] != -1) {
    if ($tp['CAN_BE_REMOVED']) {
      ?>
      <p>
        <a href="./activity?remove=<?=$tp['ACTIVITY_ID']?>" onclick="return confirm('<?=t::m('PAGE_EDIT_ACTIVITY_REALY_REMOVE_ACTIVITY')?>')"><?=t::m('LABEL_REMOVE')?></a>
      </p>
      <?
    }
    ?>
    <p>
      <a href="./activity?id=<?=$tp['ACTIVITY_ID']?>"><?=t::m('PAGE_EDIT_ACTIVITY_BACK')?></a>
    </p>
    <?
  }
  ?>

</div>
