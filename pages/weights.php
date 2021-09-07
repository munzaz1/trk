<div class="pageContent">

  <h2><?=t::m('PAGE_WEIGHTS_TITLE')?></h2>

  <?
  HTMLBlockMethods::renderOneRowForm($tp['FORM'])
  ?>

  <?
  // prepare weights {{{
  $yearsData = array();
  foreach ($tp['WEIGHTS_ROWS'] as $weightsRow) {
    $year = date('Y', $weightsRow['measurementTime']);
    if (!key_exists($year, $yearsData)) {
       $yearsData[$year] = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
    }
    $month = (int) date('n', $weightsRow['measurementTime']);
    $yearsData[$year][$month - 1][] = $weightsRow['weight'];
  }
  // }}}
  // prepare weights {{{
  ksort($yearsData);
  $currentYear        = date('Y', time());
  $currentMonthIndex  = date('n', time()) - 1;
  $previousWeight = Null;
  foreach ($yearsData as $year => $monthsWeights) {
    foreach ($monthsWeights as $monthIndex => $weights) {
      if (count($weights) == 0) {
        $yearsData[$year][$monthIndex] = $previousWeight;
      } else {
        $yearsData[$year][$monthIndex] = round(TextFormattingMethods::formatWeightAsNumber(array_sum($weights) / count($weights)), 2);
        $previousWeight = $yearsData[$year][$monthIndex];
      }
      if (($year == $currentYear) && ($monthIndex > $currentMonthIndex)) {
        $yearsData[$year][$monthIndex] = Null;
      }
    }
  }
  // }}}
  ?>

  <link rel="stylesheet" type="text/css" href="js/Chart.min.css">
  <script src="js/Chart.min.js"></script>

  <canvas id="myChart" width="400" height="100"></canvas>

  <script>
      var ctx = document.getElementById('myChart');
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?='"' . implode('", "', t::m('MONTH_NAME_SHORTCUT')) . '"'?>],
          datasets: [
            <?
            $index = 0;
            foreach ($yearsData as $year => $monthsWeights) {
              ?>
              {
                label: '<?=$year?>',
                data: [<?=implode(', ', $monthsWeights)?>],
                borderWidth: 2,
                borderColor: '<?=GUIConstants::$COLORS[$index]?>'
              },
              <?
              $index ++;
            }
            ?>
          ]
        },
      });
  </script>


  <p>
    <?
    $previousYear = -1;
    foreach ($tp['WEIGHTS_ROWS'] as $weightsRow) {
      $year = date('Y', $weightsRow['measurementTime']);
      if ($year != $previousYear) {
        ?>
        <h3><?=$year?></h3>
        <?
        $previousYear = $year;
      }
      ?>
      <?=TextFormattingMethods::formatDate($weightsRow['measurementTime'])?>
      &nbsp;&nbsp;
      <?=TextFormattingMethods::formatWeight($weightsRow['weight'])?>
      &nbsp;&nbsp;
      <a href="./weights?remove=<?=$weightsRow['id']?>"><?=t::m('LABEL_REMOVE')?></a>
      <br />
      <?
    }
    ?>
  </p>

</div>
