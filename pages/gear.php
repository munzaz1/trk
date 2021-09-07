<div class="pageContent">

  <h2><?=t::m('PAGE_GEAR_TITLE')?></h2>

  <p>
    <a href="./gear-ae"><img src="images/icons/add.png" class="actionIcon" alt="<?=t::m('PAGE_GEAR_ADD_GEAR')?>" title="<?=t::m('PAGE_GEAR_ADD_GEAR')?>">
      <?=t::m('PAGE_GEAR_ADD_GEAR')?></a>
  </p>

  <table class="gear photos">
    <?
    $previousType = NULL;
    foreach ($tp['GEAR_ROWS'] as $gearRow) {
      $classes = array();
      if ($gearRow['primary']) $classes[] = 'primary';
      if ($gearRow['retiredTime']) $classes[] = 'inactive';
      if ($previousType != $gearRow['type']) {
        $typeInternalName = GearConstants::$GEAR_INTERNAL_NAMES[$gearRow['type']];
        $typeName         = t::m('GEAR_NAME__' . $typeInternalName);
        ?>
        <tr><td colspan="5"><h3><?=$typeName?></h3></td></tr>
        <?
      }
      ?>
      <tr class="<?=implode(' ', $classes)?>">
      <td>
      <?=$gearRow['name']?>
      </td>
      <td>
      <a href="./gear-ae?id=<?=$gearRow['id']?>"><img src="images/icons/edit.png" class="actionIcon" alt="<?=t::m('LABEL_EDIT')?>" title="<?=t::m('LABEL_EDIT')?>"></a>
      <a href="./uploadPhoto?gearId=<?=$gearRow['id']?>"><img src="images/icons/uploadPhoto.png" class="actionIcon" alt="<?=t::m('LABEL_UPLOAD_PHOTO')?>" title="<?=t::m('LABEL_UPLOAD_PHOTO')?>"></a>
      </td>
      <td>
      <?
      if ($gearRow['distance']) {
        ?>
        <?=TextFormattingMethods::formatDistance($gearRow['distance'])?>
        <?
      }
      ?>
      </td>
      <td>#<?=$gearRow['id']?></td>
      <td>
      <?
      if (count($gearRow['photos']) > 0) {
        foreach ($gearRow['photos'] as $photoRow) {
          HTMLBlockMethods::renderPhoto($photoRow, './gear', True);
        }
      }
      ?>
      </td>
      </tr>
      <?
      $previousType = $gearRow['type'];
    }
    ?>
  </table>

</div>
