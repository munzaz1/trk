<div class="pageContent">
  <h2><?=t::m('LABEL_DIARY')?>: <?=$tp['PAGE_HEADER']?></h2>

  <?
  HTMLBlockMethods::renderStandardForm($tp['FORM'])
  ?>

  <?
  if ($tp['DIARY_CATEGORY_ID'] != -1) {
    ?>
    <p>
      <a href="./diary-categories?remove=<?=$tp['DIARY_CATEGORY_ID']?>"><?=t::m('LABEL_REMOVE')?></a>
    </p>
    <?
  }
  ?>

  <p>
    <a href="./diary-categories"><?=t::m('PAGE_DIARY_CATEGORIES_TITLE')?></a>
  </p>

</div>
