<div class="pageContent">

  <h2><?=t::m('PAGE_DIARY_CATEGORIES_TITLE')?></h2>

  <p>
    <a href="./diary-category-ae"><img src="images/icons/add.png" class="actionIcon" alt="<?=t::m('PAGE_DIARY_CATEGORIES_ADD_CATEGORY')?>" title="<?=t::m('PAGE_DIARY_CATEGORIES_ADD_CATEGORY')?>">
      <?=t::m('PAGE_DIARY_CATEGORIES_ADD_CATEGORY')?></a>
  </p>

  <?
    foreach ($tp['DIARY_CATEGORIES_ROWS'] as $diaryCategoryRow) {
    ?>
    <p>
      <h3>
        <?=$diaryCategoryRow['title']?>
        <a href="./diary-category-ae?id=<?=$diaryCategoryRow['id']?>"><img src="images/icons/edit.png" class="actionIcon" alt="<?=t::m('PAGE_DIARY_CATEGORIES_EDIT_CATEGORY')?>" title="<?=t::m('PAGE_DIARY_CATEGORIES_EDIT_CATEGORY')?>"></a>
      </h3>
      <?
      $items = array();
      foreach ($diaryCategoryRow['diaryItems'] as $diaryRow) {
        $items[] = '<a href="./diary-ae?id=' . $diaryRow['id'] . '" title="' . $diaryRow['text'] . '">' .
                   TextFormattingMethods::formatDateTime($diaryRow['eventTime'], FALSE, FALSE) .
                   '</a>';
      }
      ?>
      <?=implode(', ', $items)?>
    </p>
    <?
  }
  ?>

</div>
