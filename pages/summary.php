<div class="pageContent">

  <? _printYearsBlock($tp); ?>

  <table class="itemsTable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th></th>
    <th></th>
    <th></th>
    <th class="unit">[<?=t::m('LABEL_UNIT_DISTANECE')?>]</th>
    <th><?=t::m('LABEL_AVG')?></th>
    <th><?=t::m('LABEL_TIME')?></th>
    <th class="unit">↑[<?=t::m('LABEL_UNIT_ELEVATION_GAIN')?>]</th>
    <th><?=t::m('LABEL_HEARTRATER_AVG_MAX')?></th>
    <th></th>
  </tr>
  <?
  $yearlyAggregatedValuesByType   = array();
  $monthlyAggregatedValuesByType  = array();
  $previousMonth                  = -1;
  $sortedActivityTypes            = ActivitiesConstants::$ACTIVITIES_ORDER;
  foreach ($tp['ACTIVITIES_ROWS'] as $activityRow) {
    // aggregate {{{
    $currentMonth = date('n', $activityRow['startTime']);
    if (($previousMonth != -1) && ($currentMonth != $previousMonth)) {
      _printAggregatedRows($sortedActivityTypes, t::m('MONTH_NAME')[$previousMonth], $monthlyAggregatedValuesByType);
      $monthlyAggregatedValuesByType = array();
    }

    if (key_exists($activityRow['type'], $yearlyAggregatedValuesByType)) {
      $yearlyAggregatedValuesByType[$activityRow['type']]['count']           += 1;
      $yearlyAggregatedValuesByType[$activityRow['type']]['distance']        += $activityRow['distance'];
      $yearlyAggregatedValuesByType[$activityRow['type']]['time']            += $activityRow['movingTime'];
      $yearlyAggregatedValuesByType[$activityRow['type']]['elevationGain']   += $activityRow['elevationGain'];
    } else {
      $yearlyAggregatedValuesByType[$activityRow['type']] = array(
        'count'         => 1,
        'distance'      => $activityRow['distance'],
        'time'          => $activityRow['movingTime'],
        'elevationGain' => $activityRow['elevationGain'],
      );
    }

    if (key_exists($activityRow['type'], $monthlyAggregatedValuesByType)) {
      $monthlyAggregatedValuesByType[$activityRow['type']]['count']          += 1;
      $monthlyAggregatedValuesByType[$activityRow['type']]['distance']       += $activityRow['distance'];
      $monthlyAggregatedValuesByType[$activityRow['type']]['time']           += $activityRow['movingTime'];
      $monthlyAggregatedValuesByType[$activityRow['type']]['elevationGain']  += $activityRow['elevationGain'];
    } else {
      $monthlyAggregatedValuesByType[$activityRow['type']] = array(
        'count'         => 1,
        'distance'      => $activityRow['distance'],
        'time'          => $activityRow['movingTime'],
        'elevationGain' => $activityRow['elevationGain'],
      );
    }
    $previousMonth = $currentMonth;
    // }}}

    $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityRow['type']];
    $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);

    _prinRow(
        'activityRow__' . $typeInternalName,                                                            // rowClass
        $typeInternalName,                                                                              // $typeInternalName
        $typeName,                                                                                      // $typeName
        $activityRow['type'],                                                                           // $activityType
        t::m('DAY_SHORT_NAME')[date('N', $activityRow['startTime'])] .
        ' ' . TextFormattingMethods::formatDateTime($activityRow['startTime']),                         // $formattedDateTime
        '<a href="./activity?id=' . $activityRow['id'] . '">' . $activityRow['title']. '</a>',          // $title
        $activityRow['distance'],                                                                       // $distance
        $activityRow['movingTime'],                                                                     // $time
        $activityRow['elevationGain'],                                                                  // $elevationGain
        $activityRow['averageHeartrate'],                                                               // $averageHeartrate
        $activityRow['maxHeartrate'],                                                                   // $maxHeartrate
        $activityRow['gearName'],                                                                       // $gearName
        $activityRow['weatherCode'],                                                                    // $weatherCode
        $activityRow['locationCity'],                                                                   // $locationCity
        $activityRow['photosCount']                                                                     // $photosCount
      );
  }
  if (count($monthlyAggregatedValuesByType) > 0) {
    _printAggregatedRows($sortedActivityTypes, t::m('MONTH_NAME')[$previousMonth], $monthlyAggregatedValuesByType);
  }

  ?>
  <tr>
    <td colspan="8">&nbsp;</td>
  </tr>
  <?

  if (count($yearlyAggregatedValuesByType) > 0) {
    _printAggregatedRows($sortedActivityTypes, $tp['CURRENT_YEAR'], $yearlyAggregatedValuesByType);
  }
  ?>
  </table>

  <? _printYearsBlock($tp); ?>

</div>

<?
function _printAggregatedRows($sortedActivityTypes, $formattedDateTime, $aggregatedValuesByType) {
  foreach ($sortedActivityTypes as $activityType) {
    if (key_exists($activityType, $aggregatedValuesByType)) {
      $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityType];
      $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);

      _prinRow(
        '',                                                                                             // rowClass
        $typeInternalName,                                                                              // $typeInternalName
        $typeName,                                                                                      // $typeName
        $activityType,                                                                                  // $activityType
        $formattedDateTime,                                                                             // $formattedDateTime
        '∑ ' . $typeName . '  ' . $aggregatedValuesByType[$activityType]['count'] . 'x',                // $title
        $aggregatedValuesByType[$activityType]['distance'],                                             // $distance
        $aggregatedValuesByType[$activityType]['time'],                                                 // $time
        $aggregatedValuesByType[$activityType]['elevationGain'],                                        // $elevationGain
        NULL,                                                                                           // $averageHeartrate
        NULL,                                                                                           // $maxHeartrate
        '',                                                                                             // $gearName
        WeatherConstants::WC_UNKNOWN,                                                                   // $weatherCode
        '',                                                                                             // $locationCity
        NULL                                                                                            // $photosCount
      );
    }
  }
  if (count($aggregatedValuesByType) > 0) {
    _prinRow(
      '',                                                                                               // rowClass
      NULL,                                                                                             // $typeInternalName
      NULL,                                                                                             // $typeName
      NULL,                                                                                             // $activityType
      $formattedDateTime,                                                                               // $formattedDateTime
      '∑ ' . AuxiliaryMethods::sumarizeSecondDimensionInArray($aggregatedValuesByType, 'count') . 'x',  // $title
      NULL,                                                                                             // $distance
      AuxiliaryMethods::sumarizeSecondDimensionInArray($aggregatedValuesByType, 'time'),                // $time
      AuxiliaryMethods::sumarizeSecondDimensionInArray($aggregatedValuesByType, 'elevationGain'),       // $elevationGain
      NULL,                                                                                             // $averageHeartrate
      NULL,                                                                                             // $maxHeartrate
      '',                                                                                               // $gearName
      WeatherConstants::WC_UNKNOWN,                                                                     // $weatherCode
      '',                                                                                               // $locationCity
      NULL                                                                                              // $photosCount
    );
  }
}

function _prinRow($rowClass, $typeInternalName, $typeName, $activityType, $formattedDateTime, $title, $distance, $time,
                  $elevationGain, $averageHeartrate, $maxHeartrate, $gearName, $weatherCode, $locationCity, $photosCount) {
  ?>
  <tr class="<?=$rowClass?>">
  <td>
    <?
    if ($typeInternalName !== NULL) {
      ?>
      <img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/>
      <?
    }
    ?>
  </td>
  <td>
    <?=$formattedDateTime?>
  </td>
  <td class="titleWithGear">
    <?=$title?>
    <?
    if ($locationCity) {
      ?>
       <span class="location"><?=$locationCity?></span>
      <?
    }
    ?>
    <br />

    <?
    if ($photosCount !== NULL) {
      ?>
       <span class="photosCount" title="<?=t::m('LABEL_PHOTOS_COUNT')?>">(<?=$photosCount?>)</span>
      <?
    }
    ?>

    <?
    if ($gearName) {
      ?>
       <span class="gearName"><?=$gearName?></span>
      <?
    }
    ?>
  </td>
  <td>
    <?
    if ($distance !== NULL) {
      ?>
      <?=TextFormattingMethods::formatDistance($distance, False)?>
      <?
    }
    ?>
  </td>
  <td>
    <?
    if ($distance !== NULL) {
      ?>
      <?=TextFormattingMethods::formatAverageSpeedOrPace($activityType, $distance, $time, False)?>
      <?
    }
    ?>
  </td>
  <td>
    <?=TextFormattingMethods::secondsToFromattedTimeUnits($time)?>
  </td>
  <td>
    <?=TextFormattingMethods::formatElevationGain($elevationGain, False)?>
  </td>
  <td>
    <?
    if ($averageHeartrate !== NULL) {
      ?>
      <?=TextFormattingMethods::formatNumber($averageHeartrate, $decimals = 0)?>
      /
      <?=TextFormattingMethods::formatNumber($maxHeartrate, $decimals = 0)?>
      <?
    }
    ?>
  </td>
  <td class="activityWarnings">
    <?
    if (($weatherCode === NULL) || ($weatherCode == WeatherConstants::WC_UNKNOWN)) {
      ?>
      <span class="noWeatherData" title="<?=t::m('PAGE_ACTIVITIES_NO_WEATHER_DATA')?>">&nbsp;!&nbsp;</span>
      <?
    }
    ?>
    <?
    if ($locationCity === NULL) {
      ?>
      <span class="noLocationData" title="<?=t::m('PAGE_ACTIVITIES_NO_LOCATION_DATA')?>">&nbsp;!&nbsp;</span>
      <?
    }
    ?>
  </td>
</tr>
  <?
}


function _printYearsBlock($tp) {
  ?>
  <p>
    <?
    foreach ($tp['YEARS'] as $year) {
      if ($tp['CURRENT_YEAR'] == $year) {
        $class = 'currentValue';
      } else {
        $class = '';
      }
      ?>
      <a href="./summary?year=<?=$year?>" class="<?=$class?>"><?=$year?></a>
      <?
    }
    ?>
  </p>
  <?
}

?>
