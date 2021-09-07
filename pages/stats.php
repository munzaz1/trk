<div class="pageContent">
  <?
  $years = array_keys($tp['STATS_DATA_BY_YEAR_BY_MONTH_BY_TYPE']);
  rsort($years);
  ?>
  <table class="statsTable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <?
    for ($month = 1; $month <= 12; $month ++) {
      ?>
      <th><a href="./cumulativeStats?month=<?=$month?>"><?=t::m('MONTH_NAME')[$month]?></a></th>
      <?
    }
    ?>
    <th>∑</th>
  </tr>
  <?
  foreach ($years as $year) {
    ?>
    <tr>
      <td colspan="13">
        <span class="year"><?=$year?></span>
      </td>
    </tr>
    <tr>
    <?
    for ($month = 1; $month <= 12; $month ++) {
      $totalMonthTime = 0;
      $statsDataByType = $tp['STATS_DATA_BY_YEAR_BY_MONTH_BY_TYPE'][$year][$month];
      if (($month % 2) == 0) {
        $oddClass = 'odd';
      } else {
        $oddClass = '';
      }
      ?>
      <td class="values <?=$oddClass?>">
        <?
        foreach (ActivitiesConstants::$ACTIVITIES_ORDER as $activityType) {
          if (key_exists($activityType, $statsDataByType)) {
            $totalMonthTime   += $statsDataByType[$activityType]['time'];
            $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityType];
            $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
            ?>
            <a href="./cumulativeStats?month=<?=$month?>&type=<?=$activityType?>"
            class="cumulative_stats"><img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/></a>
            <?=TextFormattingMethods::formatDistance($statsDataByType[$activityType]['distance'], False)?>
            <br />
            <span class="time"><?=TextFormattingMethods::secondsToFromattedTimeUnits($statsDataByType[$activityType]['time'])?>
              (<?=$statsDataByType[$activityType]['count']?>)</span>
            <br />
            <?
          }
        }
        ?>
        <span class="totalTime">∑ <?=TextFormattingMethods::secondsToFromattedTimeUnits($totalMonthTime)?></span>
        <a class="diaryIcon" href="./diary?month=<?=$month?>&year=<?=$year?>"><img src="images/icons/calendar.png" alt="<?=t::m('LABEL_DIARY')?>" /></a>
      </td>
      <?
    }
    ?>
    <td class="values">
      <?
      $totalTime = 0;
      $statsDataByType = $tp['STATS_DATA_BY_YEAR_BY_TYPE'][$year];
      foreach (ActivitiesConstants::$ACTIVITIES_ORDER as $activityType) {
        if (key_exists($activityType, $statsDataByType)) {
          $totalTime        += $statsDataByType[$activityType]['time'];
          $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityType];
          $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
          ?>
          <img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/>
          <?=TextFormattingMethods::formatDistance($statsDataByType[$activityType]['distance'], False)?>
          <br />
          <span class="time"><?=TextFormattingMethods::secondsToFromattedTimeUnits($statsDataByType[$activityType]['time'])?>
            (<?=$statsDataByType[$activityType]['count']?>)</span>
          <br />
          <?
        }
      }
      ?>
      <span class="totalTime">∑ <?=TextFormattingMethods::secondsToFromattedTimeUnits($totalTime)?></span>
    </td>
    </tr>
    <?
  }
  ?>
  </table>
</div>
