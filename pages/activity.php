<div class="pageContent">
  <?
  $activityRow      = $tp['ACTIVITY_ROW'];
  $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityRow['type']];
  $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
  ?>

  <div class="activityActions">
    <a href="./activity-ae?id=<?=$tp['ACTIVITY_ROW']['id']?>"><img src="images/icons/edit.png" class="actionIcon" alt="<?=t::m('LABEL_EDIT')?>" title="<?=t::m('LABEL_EDIT')?>"></a>
    <a href="./uploadPhoto?activityId=<?=$tp['ACTIVITY_ROW']['id']?>"><img src="images/icons/uploadPhoto.png" class="actionIcon" alt="<?=t::m('LABEL_UPLOAD_PHOTO')?>" title="<?=t::m('LABEL_UPLOAD_PHOTO')?>"></a>
    &nbsp;&nbsp;
    <a href="./fetch-activity-photos-from-strava?id=<?=$tp['ACTIVITY_ROW']['id']?>"><?=t::m('PAGE_ACTIVITY_FETCH_STRAVA_PHOTOS')?></a>
    <?
    if (Settings::$S['USE_LOCATION_SERVICE_TO_FILL_DATA']) {
      ?>
      &nbsp;&nbsp;
      <a href="./fetch-activity-location?id=<?=$tp['ACTIVITY_ROW']['id']?>"><?=t::m('LABEL_UPDATE_LOCATION')?></a>
      <?
    }
    ?>
  </div>

  <h3 class="activityRow__<?=$typeInternalName?> activityHeader">
    <img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/>
    <?=$activityRow['title']?>,
    <?=t::m('DAY_SHORT_NAME')[date('N', $activityRow['startTime'])]?>
    <?=TextFormattingMethods::formatDateTime($activityRow['startTime'])?>
  </h3>

  <?
  HTMLBlockMethods::renderDiaryCalendar($tp['MONTH'], $tp['YEAR'], $tp['YEARS'], $tp['CURRENT_MONTH'], $tp['CURRENT_YEAR'], $tp['MONTH_ACTIVITIES'], $tp['MONTH_DIARY_DATA'],
                                        $activityRow['id']);
  ?>

  <div class="activityValuesBox">
    <?=_getActivityValueCell(
      t::m('LABEL_DISTANCE'),
      (($activityRow['distance'] !== NULL) ? TextFormattingMethods::formatDistance($activityRow['distance']) : '-'),
      '')?>

    <?=_getActivityValueCell(
      t::m('LABEL_TIME'),
      (($activityRow['movingTime'] !== NULL) ? TextFormattingMethods::secondsToFromattedTimeUnits($activityRow['movingTime']) : '-'),
      '')?>

    <?=_getActivityValueCell(
      t::m('LABEL_AVG'),
      TextFormattingMethods::formatAverageSpeedOrPace($activityRow['type'], $activityRow['distance'], $activityRow['movingTime']),
      '')?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_ELEVATION_GAIN'),
      (($activityRow['elevationGain'] !== NULL) ? TextFormattingMethods::formatElevationGain($activityRow['elevationGain']) : '-'),
      '')?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_MAX_SPEED'),
      (($activityRow['maxSpeed'] !== NULL) ? TextFormattingMethods::formatSpeed($activityRow['maxSpeed']) : '-'),
      '')?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_AVG_HEARTRATE'),
      (($activityRow['averageHeartrate'] !== NULL) ? TextFormattingMethods::formatNumber($activityRow['averageHeartrate'], $decimals = 0) : '-'),
      t::m('LABEL_UNIT_HEART_RATE_IN_MINUTE'))?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_MAX_HEARTRATE'),
      (($activityRow['maxHeartrate'] !== NULL) ? TextFormattingMethods::formatNumber($activityRow['maxHeartrate'], $decimals = 0) : '-'),
      t::m('LABEL_UNIT_HEART_RATE_IN_MINUTE'))?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_KILOCALORIES'),
      (($activityRow['kilocalories'] !== NULL) ? TextFormattingMethods::formatNumber($activityRow['kilocalories'], $decimals = 0) : '-'),
      t::m('LABEL_UNIT_KILOCAL'))?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_AVERAGE_TEMERATURE'),
      (($activityRow['averageTemperature'] !== NULL) ? TextFormattingMethods::formatNumber($activityRow['averageTemperature'], $decimals = 1) : '-'),
      t::m('LABEL_UNIT_CELSIUS_DEGREE'))?>

    <?=_getActivityValueCell(
      t::m('LABEL_TOTAL_TIME'),
      (($activityRow['elapsedTime'] !== NULL) ? TextFormattingMethods::secondsToFromattedTimeUnits($activityRow['elapsedTime']) : '-'),
      '')?>

    <?=_getActivityValueCell(
      t::m('LABEL_LOCATION'),
      (($activityRow['locationCity'] !== NULL) ? $activityRow['locationCity'] : '-'),
      '', 'big')?>

    <?
    if ($activityRow['weatherCode'] !== NULL) {
      $iconFile = WeatherConstants::$WEATHER_ICON_BY_CODE[$activityRow['weatherCode']];
      $hour = date('H', $activityRow['startTime']);
      if (($hour >= 20) || ($hour <= 6)) {
        $dayNightSuffix = 'n';
      } else {
        $dayNightSuffix = 'd';
      }
      $iconFile = 'images/icons/weather/' . $iconFile . $dayNightSuffix . '.png';
      $weatherIcon = '<img src="' . $iconFile . '" ' .
                     'alt="' . t::m('WEATHER__' . WeatherConstants::$WEATHER_INTERNAL_NAMES[$activityRow['weatherCode']]) . '" ' .
                     'title="' . t::m('WEATHER__' . WeatherConstants::$WEATHER_INTERNAL_NAMES[$activityRow['weatherCode']]) . '" />';
      ?>
      <?=_getActivityValueCell(
        t::m('LABEL_WEATHER'),
        $weatherIcon . TextFormattingMethods::formatNumber($activityRow['weatherTemperature']),
        t::m('LABEL_UNIT_CELSIUS_DEGREE'))?>
      <?
    } elseif ($activityRow['weatherTemperature'] !== NULL) {
      ?>
      <?=_getActivityValueCell(
        t::m('LABEL_WEATHER'),
        TextFormattingMethods::formatNumber($activityRow['weatherTemperature']),
        t::m('LABEL_UNIT_CELSIUS_DEGREE'))?>
      <?
    }
    ?>

    <?
    if ($tp['GEAR_ROW'] !== NULL) {
      ?>
      <?=_getActivityValueCell(
        t::m('PAGE_ACTIVITY_GEAR_NAME'),
        (($tp['GEAR_ROW']['name'] !== NULL) ? $tp['GEAR_ROW']['name'] : '-'),
        '', 'big')?>
      <?
    }
    ?>

    <?=_getActivityValueCell(
      t::m('PAGE_ACTIVITY_DEVICE_NAME'),
      (($activityRow['deviceName'] !== NULL) ? $activityRow['deviceName'] : '-'),
      '', 'big')?>

    <?=_getActivityValueCell(
      t::m('LABEL_DESCRIPTION'),
      AuxiliaryMethods::shortText($activityRow['description'], 120),
      '', 'description')?>
  </div>

  <div class="proviousNextActivity">
    <?
    if ($tp['PREVIOUS_ACTIVITY']) {
      ?>
      <a href="./activity?id=<?=$tp['PREVIOUS_ACTIVITY']['id']?>"><?=t::m('PAGE_ACTIVITY_PREVIOUS_ACTIVITY')?></a>
      <?
    }
    if ($tp['NEXT_ACTIVITY']) {
      ?>
      &nbsp;&nbsp;&nbsp;
      <a href="./activity?id=<?=$tp['NEXT_ACTIVITY']['id']?>"><?=t::m('PAGE_ACTIVITY_NEXT_ACTIVITY')  ?></a>
      <?
    }
    ?>
  </div>

  <div class="activityPhotos photos">
    <?
    foreach ($tp['PHOTOS_ROWS'] as $photoRow) {
      HTMLBlockMethods::renderPhoto($photoRow, './activity?id=' . $activityRow['id']);
    }
    ?>
  </div>

  <div class="clear">&nbsp;</div>

  <?
  if (($tp['COORDINTES'] !== NULL) || ($tp['START_POSITION'] !== NULL)) {
    ?>
    <link rel="stylesheet" href="js/leaflet.css" />
    <script src="js/leaflet.js"></script>

   <div class="activityMap" id="map"></div>

    <script>
      var map = L.map('map').setView([<?=$tp['START_POSITION'][0]?>,<?=$tp['START_POSITION'][1]?>], 13);
      L.tileLayer(
          '<?=Settings::$S['LEAFLET_MAP_TILES']?>', {
              maxZoom: 15,
          }).addTo(map);

      L.marker([<?=$tp['START_POSITION'][0]?>,<?=$tp['START_POSITION'][1]?>]).addTo(map)
        .bindPopup('<?=$activityRow['title']?>');//.openPopup();

      <?
      if ($tp['COORDINTES'] !== NULL) {
        ?>
        var coordinates = [
        <?
        foreach ($tp['COORDINTES'] as $coordinates) {
          ?>
          [<?=$coordinates[0]?>, <?=$coordinates[1]?>],
          <?
        }
        ?>
        ];
        var polyline = L.polyline(
            coordinates,
            {
                color: 'blue',
                weight: 2,
                opacity: .7,
                lineJoin: 'round'
            }
        ).addTo(map);
        map.fitBounds(polyline.getBounds());
        <?
      }
      ?>
    </script>
    </div>
  <?
  }
  ?>

  <div class="clear">&nbsp;</div>
</div>

<?

function _getActivityValueCell($title, $formattedValue, $unit, $additinalCellClass = '') {
  $content = '<div class="activityValueCell ' . $additinalCellClass . '">';
  $content .= '<div class="activityValueCellTitle">';
  $content .= $title;
  $content .= '</div>';
  $content .= '<div class="activityValueCellValue">';
  $content .= $formattedValue . ' ' . $unit;
  $content .= '</div>';
  $content .= '</div>';
  return $content;
}

?>
