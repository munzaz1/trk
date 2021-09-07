<div class="pageContent">

  <div class="activityActions">
    <a href="diary-ae"><img src="images/icons/add.png" class="actionIcon" alt="<?=t::m('PAGE_DIARY_ADD_ITEM')?>" title="<?=t::m('PAGE_DIARY_ADD_ITEM')?>"></a>
    <a href="diary-categories"><img src="images/icons/categories.png" class="actionIcon" alt="<?=t::m('PAGE_DIARY_CATEGORIES_TITLE')?>" title="<?=t::m('PAGE_DIARY_CATEGORIES_TITLE')?>"></a>
  </div>

  <h3 class="diaryHeader activityHeader">
    <?=t::m('MONTH_NAME')[$tp['MONTH']]?> <?=$tp['YEAR']?>
  </h3>

  <?
  HTMLBlockMethods::renderDiaryCalendar($tp['MONTH'], $tp['YEAR'], $tp['YEARS'], $tp['CURRENT_MONTH'], $tp['CURRENT_YEAR'], $tp['MONTH_ACTIVITIES'], $tp['MONTH_DIARY_DATA'],
                                        NULL);
  ?>

  <?
  $mapMarkers       = array();
  $minLatitude      = 1000;
  $maxLatitude      = -1000;
  $minLongitude     = 1000;
  $maxLongitude     = -1000;
  $tolerance        = 0.0005;
  $diaryItems       = array();
  $statsDataByType  = array();
  for ($day = 1; $day <= 31; $day ++) {
    if (key_exists($day, $tp['MONTH_ACTIVITIES'])) {
      foreach ($tp['MONTH_ACTIVITIES'][$day] as $monthActivityRow) {
        $diaryItems[] = array('type' => 'activity', 'data' => $monthActivityRow);

        if (!key_exists($monthActivityRow['type'], $statsDataByType)) {
          $statsDataByType[$monthActivityRow['type']] = array(
            'count'         => 0,
            'distance'      => 0,
            'time'          => 0,
            'elevationGain' => 0,
          );
        }
        $statsDataByType[$monthActivityRow['type']]['count']         += 1;
        $statsDataByType[$monthActivityRow['type']]['distance']      += $monthActivityRow['distance'];
        $statsDataByType[$monthActivityRow['type']]['time']          += $monthActivityRow['movingTime'];
        $statsDataByType[$monthActivityRow['type']]['elevationGain'] += $monthActivityRow['elevationGain'];

        if (($monthActivityRow['startLatitude'] !== NULL) && ($monthActivityRow['startLongitude'] !== NULL)) {
          $key = $monthActivityRow['startLatitude'] . ',' . $monthActivityRow['startLongitude'];
          if (array_key_exists($key, $mapMarkers)) {
            $mapMarkers[$key]['titleDataList'][] = HTMLBlockMethods::getActivityInfoBlock($monthActivityRow, True);
          } else {
            // try to find near marker {{{
            $activityAdded = False;
            foreach ($mapMarkers as $tmpKey => $mapMarker) {
              if (($monthActivityRow['startLatitude'] >= ($mapMarker['latitude'] - $tolerance)) &&
                  ($monthActivityRow['startLatitude'] <= ($mapMarker['latitude'] + $tolerance)) &&
                  ($monthActivityRow['startLongitude'] >= ($mapMarker['longitude'] - $tolerance)) &&
                  ($monthActivityRow['startLongitude'] <= ($mapMarker['longitude'] + $tolerance))) {
                $mapMarkers[$tmpKey]['titleDataList'][] = HTMLBlockMethods::getActivityInfoBlock($monthActivityRow, True);
                $activityAdded = True;
                break;
              }
            }
            // }}}
            if (!$activityAdded) {
              $mapMarkers[$key] = array(
                'latitude'      => $monthActivityRow['startLatitude'],
                'longitude'     => $monthActivityRow['startLongitude'],
                'titleDataList' => array(HTMLBlockMethods::getActivityInfoBlock($monthActivityRow, True))
              );
              $minLatitude  = min($minLatitude, $monthActivityRow['startLatitude']);
              $maxLatitude  = max($maxLatitude, $monthActivityRow['startLatitude']);
              $minLongitude = min($minLongitude, $monthActivityRow['startLongitude']);
              $maxLongitude = max($maxLongitude, $monthActivityRow['startLongitude']);
            }
          }
        }
      }
    }
    if (key_exists($day, $tp['MONTH_DIARY_DATA'])) {
      foreach ($tp['MONTH_DIARY_DATA'][$day] as $diaryRow) {
        $diaryItems[] = array('type' => 'diary', 'data' => $diaryRow);
      }
    }
  }
  ?>

  <div class="diaryItems">
    <?
    foreach ($diaryItems as $diaryItem) {
      if ($diaryItem['type'] == 'activity') {
        $formattedRow = HTMLBlockMethods::getActivityInfoBlock($diaryItem['data']);
      } elseif ($diaryItem['type'] == 'diary') {
        $formattedRow = HTMLBlockMethods::getDiaryInfoBlock($diaryItem['data']);
      } else {
        $formattedRow = $diaryItem['type'];
      }
      ?>
      <?=$formattedRow?>
      <br />
      <?
    }
    ?>
    <hr/>
    <p>
    <?
    $totalMonthTime = 0;
    foreach (ActivitiesConstants::$ACTIVITIES_ORDER as $activityType) {
      if (key_exists($activityType, $statsDataByType)) {
        $totalMonthTime += $statsDataByType[$activityType]['time'];
        $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityType];
        $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
        ?>
        <a href="./cumulativeStats?month=<?=$tp['MONTH']?>&type=<?=$activityType?>" class="cumulative_stats">
        <img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/></a>
        <?=TextFormattingMethods::formatDistance($statsDataByType[$activityType]['distance'])?>
        <span class="time"><?=TextFormattingMethods::secondsToFromattedTimeUnits($statsDataByType[$activityType]['time'])?>
          (<?=$statsDataByType[$activityType]['count']?>)</span>
        <br />
        <?
      }
    }
    ?>
    <span class="totalTime">∑ <?=TextFormattingMethods::secondsToFromattedTimeUnits($totalMonthTime)?></span>
    <a href="./cumulativeStats?month=<?=$tp['MONTH']?>&type=<?=ActivitiesConstants::AT_ANY?>"><?=t::m('PAGE_CUMULATIVE_STATS_TITLE')?></a>
    </p>
  </div>

  <div class="clear">&nbsp;</div>

  <?
  if (count($tp['MONTH_PHOTOS']) > 0) {
    ?>
    <a name="photos"></a>
    <div class="photos">
      <?
      foreach ($tp['MONTH_PHOTOS'] as $photoRow) {
        HTMLBlockMethods::renderPhoto($photoRow, './diary?month=' . $tp['MONTH'] . '&year=' . $tp['YEAR'] . '#photos');
      }
      ?>
    </div>
    <?
  }
  ?>

  <div class="clear">&nbsp;</div>

  <?
  if (count($mapMarkers) > 0) {
    ?>
    <link rel="stylesheet" href="js/leaflet.css" />
    <script src="js/leaflet.js"></script>

    <div class="diaryMap" id="map"></div>

    <script>
      var map = L.map('map').setView([<?=(($maxLatitude + $minLatitude) / 2)?>,<?=(($maxLongitude + $minLongitude) / 2)?>], 13);
      L.tileLayer(
          '<?=Settings::$S['LEAFLET_MAP_TILES']?>', {
              maxZoom: 15,
          }).addTo(map);

      <?
      foreach ($mapMarkers as $key => $mapMarker) {
        $titleParts = array();
        foreach ($mapMarker['titleDataList'] as $titleData) {
          $titleParts[] = $titleData;
        }
        ?>
        L.marker([<?=$mapMarker['latitude']?>,<?=$mapMarker['longitude']?>]).addTo(map)
          .bindPopup('<?=implode('<br />', $titleParts)?>');
        <?
      }
      ?>

      map.fitBounds([
          [<?=$minLatitude?>, <?=$minLongitude?>],
          [<?=$maxLatitude?>, <?=$maxLongitude?>]
      ]);
    </script>
    </div>
  <?
  }
  ?>

  <div class="clear">&nbsp;</div>
</div>
