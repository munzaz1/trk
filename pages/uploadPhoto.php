<div class="pageContent">

  <h2><?=t::m('PAGE_UPLOAD_PHOTO_TITLE')?></h2>

  <?
  if ($tp['GEAR_ROW'] !== NULL) {
    ?>
    <p>
    <?=t::m('LABEL_FOR')?> <strong><?=$tp['GEAR_ROW']['name']?></strong>
    </p>
    <?
  }
  if ($tp['ACTIVITY_ROW'] !== NULL) {
    ?>
    <p>
    <?=t::m('LABEL_FOR')?> <?=HTMLBlockMethods::getActivityInfoBlock($tp['ACTIVITY_ROW'])?>
    </p>
    <?
  }
  if ($tp['DIARY_ROW'] !== NULL) {
    ?>
    <p>
    <?=t::m('LABEL_FOR')?> <?=HTMLBlockMethods::getDiaryInfoBlock($tp['DIARY_ROW'])?>
    </p>
    <?
  }
  ?>

  <?
  HTMLBlockMethods::renderStandardForm($tp['FORM'])
  ?>

</div>
