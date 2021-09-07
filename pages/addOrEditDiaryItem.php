<div class="pageContent">
  <h2><?=t::m('LABEL_DIARY')?>: <?=$tp['PAGE_HEADER']?></h2>

  <?
  HTMLBlockMethods::renderStandardForm($tp['FORM'])
  ?>

  <?
  if ($tp['DIARY_ID'] != -1) {
    ?>
    <p>
      <a href="./diary?remove=<?=$tp['DIARY_ID']?>"><?=t::m('LABEL_REMOVE')?></a>
    </p>
    <?
  }
  ?>

  <p>
    <a href="./diary?month=<?=$tp['DIARY_MONTH']?>&year=<?=$tp['DIARY_YEAR']?>"><?=t::m('LABEL_DIARY')?></a>
  </p>

</div>
