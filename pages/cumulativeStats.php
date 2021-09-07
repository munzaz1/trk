<div class="pageContent">
  <?
  $currentYear      = date('Y', time());
  $currentMonth     = date('n', time());
  $currentDay       = date('d', time());

  $month            = $tp['MONTH'];

  $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$tp['ACTIVITY_TYPE']];
  $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
  ?>
  <h2><?=t::m('MONTH_NAME')[$month]?> <img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/></h2>
  <?
  if (count($tp['USED_ACTIVITIES_TYPES']) > 0) {
    ?>
    <p>
      <?
      foreach ($tp['USED_ACTIVITIES_TYPES'] as $usedActivityType) {
        $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$usedActivityType];
        $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
        ?>
        <a href="./cumulativeStats?month=<?=$month?>&type=<?=$usedActivityType?>&yAxis=<?=$tp['Y_AXIS']?>"
           class="cumulative_stats <?=(($tp['ACTIVITY_TYPE'] == $usedActivityType) ? ' cumulative_stats_active' : '')?>"
           ><img src="images/icons/activity/Activity__<?=$typeInternalName?>.png" alt="<?=$typeName?>" title="<?=$typeName?>"/></a>
        <?
      }
      $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[ActivitiesConstants::AT_ANY];
      $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
      ?>
      <a href="./cumulativeStats?month=<?=$month?>&type=<?=ActivitiesConstants::AT_ANY?>&yAxis=<?=$tp['Y_AXIS']?>"
        class="cumulative_stats <?=(($tp['ACTIVITY_TYPE'] == ActivitiesConstants::AT_ANY) ? ' cumulative_stats_active active' : '')?>"><?=$typeName?></a>
    </p>
    <?
  }
  ?>
  <link rel="stylesheet" type="text/css" href="js/Chart.min.css">
  <script src="js/Chart.min.js"></script>

  <canvas id="cumulativeStatsGraph"></canvas>

  <script>
      <?
      if ($month == $currentMonth) {
        $maxDay = $currentDay;
      } else {
        $maxDay = 0;
      }
      foreach ($tp['MONTH_ACTIVITIES_BY_YEAR'] as $year => $monthActivities) {
        if (count($monthActivities) > 0) {
          $maxDay = max($maxDay, max(array_keys($monthActivities)));
        }
      }
      ?>
      var ctx = document.getElementById('cumulativeStatsGraph');
      var cumulativeStatsGraph = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?=implode(', ', range(1, $maxDay))?>],
          datasets: [
            <?
            $index = 0;
            foreach ($tp['MONTH_ACTIVITIES_BY_YEAR'] as $year => $monthActivities) {
              if ($index < (count($tp['MONTH_ACTIVITIES_BY_YEAR']) - 2)) {
                $hidden = 1;
              } else {
                $hidden = 0;
              }
              $data = array();
              $cummulativeValue = 0;
              for ($day = 1; $day <= $maxDay; $day ++) {
                if (($year == $currentYear) && ($month == $currentMonth) && ($day > $currentDay)) continue;
                if (key_exists($day, $monthActivities)) {
                  foreach ($monthActivities[$day] as $activityRow) {
                    if ($tp['Y_AXIS'] == 1) {
                      $cummulativeValue += intval(TextFormattingMethods::formatDistance($activityRow['distance'], False));
                    } else {
                      $cummulativeValue += ($activityRow['movingTime'] / TimeConstants::SECONDS_IN_HOUR);
                    }
                  }
                }
                $data[] = $cummulativeValue;
              }
              ?>
              {
                steppedLine: 'before',
                label: '<?=$year?>',
                data: [<?=implode(', ', $data)?>],
                borderWidth: 2,
                hidden: <?=$hidden?>,
                borderColor: '<?=GUIConstants::$COLORS[$index]?>'
              },
              <?
              $index ++;
            }
            ?>
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: '<?=t::m('LABEL_DAY_IN_MONTH')?>'
              }
            }],
            yAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                <?
                if ($tp['Y_AXIS'] == 1) {
                  ?>
                  labelString: '<?=t::m('LABEL_DISTANCE')?> [<?=(Settings::$S['METRIC_UNITS'] ? t::m('LABEL_UNIT_KILOMETERS') : t::m('LABEL_UNIT_MILES'))?>]'
                  <?
                } else {
                  ?>
                  labelString: '<?=t::m('LABEL_TIME')?> [<?=t::m('LABEL_UNIT_HOUR')?>]'
                  <?
                }
                ?>
              }
            }]
          }
        }
      });
  </script>

  <p>
    <?=t::m('PAGE_CUMULATIVE_STATS_Y_AXIS')?>:
    <a href="./cumulativeStats?month=<?=$month?>&type=<?=$tp['ACTIVITY_TYPE']?>&yAxis=0"
      <?=(($tp['Y_AXIS'] == 0) ? 'class="active"' : '')?>><?=t::m('LABEL_TIME')?></a>
    <a href="./cumulativeStats?month=<?=$month?>&type=<?=$tp['ACTIVITY_TYPE']?>&yAxis=1"
      <?=(($tp['Y_AXIS'] == 1) ? 'class="active"' : '')?>><?=t::m('LABEL_DISTANCE')?></a>
  </p>
  <p>
    <?
    for ($tmpMonth = 1; $tmpMonth <= 12; $tmpMonth ++) {
      ?>
      <a href="./cumulativeStats?month=<?=$tmpMonth?>&type=<?=$tp['ACTIVITY_TYPE']?>&yAxis=<?=$tp['Y_AXIS']?>"
        <?=(($tmpMonth == $month) ? 'class="active"' : '')?>><?=t::m('MONTH_NAME_SHORTCUT')[$tmpMonth]?></a>
      <?
    }
    ?>
  </p>

</div>
