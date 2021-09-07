<?

class HTMLBlockMethods {

  public static function renderOneRowForm($form) {
    ?>
    <form action="<?=$form->action?>" method="post" class="<?=$form->name?> <?=$form->extraClass?>" <?=$form->enctype?>>

      <div class="longFormRow">
        <?
        $hiddenFiledsCodeItems = array();
        foreach ($form->fields as $field) {
          if (get_class($field) == 'Field_HiddenId') {
            $hiddenFiledsCodeItems[] = $field->getInputCode($form->valueByField, $form->errorMessagesByField);
            continue;
          }
          ?>
          <div class="longFormRowElement label <?=($field->isRequired ? 'inputLabel_required' : '')?>"><?=$field->label?></div>
          <div class="longFormRowElement">
            <?=$field->getInputCode($form->valueByField, $form->errorMessagesByField)?>
          </div>
          <div class="longFormRowSeparator">&nbsp;</div>
        <?
        }
        ?>
        <div class="sendButton">
          <input type="submit" value="<?=$form->sendButtonTitle?>" />
        </div>
      </div>

      <div class="clear">&nbsp;</div>

      <input type="hidden" name="<?=$form->name?>" value="1" />

      <?=implode("\n", $hiddenFiledsCodeItems)?>

    </form>
    <?
  }


  public static function renderStandardForm($form) {
    ?>
    <form action="<?=$form->action?>" method="post" class="<?=$form->name?> <?=$form->extraClass?>" <?=$form->enctype?>>

      <?
      $hiddenFiledsCodeItems = array();
      $lineNumber = 0;
      foreach ($form->fields as $field) {
        if (get_class($field) == 'Field_HiddenId') {
          $hiddenFiledsCodeItems[] = $field->getInputCode($form->valueByField, $form->errorMessagesByField);
          continue;
        }
        ?>
        <div class="standardFormFieldRow <?=(($lineNumber % 2) == 0 ? 'oddRow' : '')?>">
          <div class="standardFormFieldRowLabel <?=($field->isRequired ? 'inputLabel_required' : '')?>">
            <?=$field->label?>
          </div>
          <div class="standardFormFieldRowInput">
            <?=$field->getInputCode($form->valueByField, $form->errorMessagesByField)?>
          </div>
          <div class="clear"></div>
        </div>
        <?
        $lineNumber ++;
      }
      ?>

      <div class="shortFormFieldRow">
        <div class="sendButton">
          <input type="submit" value="<?=$form->sendButtonTitle?>" />
        </div>
        <div class="clear">&nbsp;</div>
      </div>

      <input type="hidden" name="<?=$form->name?>" value="1" />
      <?=implode("\n", $hiddenFiledsCodeItems)?>

    </form>
    <?
  }


  public static function renderShortForm($form) {
    ?>
    <form action="<?=$form->action?>" method="post" class="<?=$form->name?> <?=$form->extraClass?>" <?=$form->enctype?>>

      <?
      $hiddenFiledsCodeItems = array();
      foreach ($form->fields as $field) {
        if (get_class($field) == 'Field_HiddenId') {
          $hiddenFiledsCodeItems[] = $field->getInputCode($form->valueByField, $form->errorMessagesByField);
          continue;
        }
        ?>
        <div class="shortFormFieldRow <?=($field->isRequired ? 'inputLabel_required' : '')?>">
          <?=$field->label?>
        </div>
        <div class="shortFormFieldRow">
          <?=$field->getInputCode($form->valueByField, $form->errorMessagesByField)?>
        </div>
        <?
      }
      ?>

      <div class="shortFormFieldRow">
        <div class="sendButton">
          <input type="submit" value="<?=$form->sendButtonTitle?>" />
        </div>
        <div class="clear">&nbsp;</div>
      </div>

      <input type="hidden" name="<?=$form->name?>" value="1" />
      <?=implode("\n", $hiddenFiledsCodeItems)?>

    </form>
    <?
  }


  public static function getActivityInfoBlock($activityRow, $shortForm = FALSE) {
    $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$activityRow['type']];
    $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);

    $outItems = array();
    $outItems[] = '<a href="./activity?id=' . $activityRow['id'] . '"><img src="images/icons/activity/Activity__' . $typeInternalName . '.png" ' .
                  'alt="' . $typeName . ' title="' . $typeName . '"/></a> ';
    if (!$shortForm) {
      $outItems[] = TextFormattingMethods::formatDateTime($activityRow['startTime'], FALSE, FALSE);
    } else {
      $outItems[] = TextFormattingMethods::formatDate($activityRow['startTime'], TRUE);
    }
    $outItems[] = '<a href="./activity?id=' . $activityRow['id'] . '">' . $activityRow['title']. '</a>';
    if ($activityRow['distance'] !== NULL) {
      $outItems[] = TextFormattingMethods::formatDistance($activityRow['distance']);
    }
    if ($activityRow['movingTime'] !== NULL) {
      $outItems[] = TextFormattingMethods::secondsToFromattedTimeUnits($activityRow['movingTime']);
    }
    if ((!$shortForm) && ($activityRow['locationCity'] !== NULL)) {
      $outItems[] = $activityRow['locationCity'];
    }

    $outString = implode(', ', $outItems);
    return $outString;
  }


  public static function getDiaryInfoBlock($diaryRow) {
    $outItems = array();
    $outItems[] = TextFormattingMethods::formatDateTime($diaryRow['eventTime'], FALSE, TRUE);
    $outItems[] = '<a href="./diary-ae?id=' . $diaryRow['id'] . '" title="' . $diaryRow['text'] . '">' . $diaryRow['categoryTitle'] . '</a>';
    if ($diaryRow['text']) {
      $outItems[] = $diaryRow['text'];
    }

    return implode(', ', $outItems);
  }


  public static function renderDiaryCalendar($month, $year, $years, $currentMonth, $currentYear, $monthActivities, $monthDiaryData,
                                             $currentActivityId = NULL) {
    $previousMonth_Month    = $month - 1;
    $previousMonth_Year     = $year;
    if ($previousMonth_Month == 0) {
      $previousMonth_Month  = 12;
      $previousMonth_Year   -= 1;
    }

    $nextMonth_Month        = $month + 1;
    $nextMonth_Year         = $year;
    if ($nextMonth_Month == 13) {
      $nextMonth_Month      = 1;
      $nextMonth_Year       += 1;
    }

    $diaryItems = array();
    ?>
    <div class="diaryCalendar">
      <div class="dateSelector">
        <form method="get" action="./diary">
          <select name="month" onchange="this.form.submit()">
            <?
            for ($tmpMonth = 1; $tmpMonth <= 12; $tmpMonth ++) {
              ?>
              <option value="<?=$tmpMonth?>" <?=($tmpMonth == $month ? 'selected' : '')?>><?=t::m('MONTH_NAME')[$tmpMonth]?></option>
              <?
            }
            ?>
          </select><br />
          <select name="year" onchange="this.form.submit()">
            <?
            foreach ($years as $tmpYear) {
              ?>
              <option value="<?=$tmpYear?>" <?=($tmpYear == $year ? 'selected' : '')?>><?=$tmpYear?></option>
              <?
            }
            ?>
          </select><br />
          <a href="./diary?month=<?=$currentMonth?>&year=<?=$currentYear?>"><?=t::m('LABEL_CURRENT_MONTH')?></a><br />
          <a href="./diary?month=<?=$previousMonth_Month?>&year=<?=$previousMonth_Year?>"><?=t::m('LABEL_PREVIOUS')?> <?=t::m('LABEL_MONTH')?></a><br />
          <a href="./diary?month=<?=$nextMonth_Month?>&year=<?=$nextMonth_Year?>"><?=t::m('LABEL_NEXT')?> <?=t::m('LABEL_MONTH')?></a><br />
          <br />
          <a href="./diary?month=<?=$month?>&year=<?=$year?>"><?=t::m('LABEL_WHOLE_MONTH')?></a><br />
        </form>
      </div>

      <?
      $currentDay = -100;

      if (($month == date('n')) && ($year == date('Y'))) {
        $currentDay = date('d');
      }

      $firstDayOfMonth    = mktime(0, 0, 0, $month, 1, $year);
      $daysInMonth        = date('t', $firstDayOfMonth);
      $firstDayDayOfWeek  = date('N', $firstDayOfMonth) - 1; // 0 - Monday, 1 - Tuesday, ...

      $weeks = array();
      $day = -1;
      while ($day < $daysInMonth) {
        $week = array();
        for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek ++) {
          if ($day == -1) {
            if ($dayOfWeek == $firstDayDayOfWeek) $day = 1;
            $week[] = $day;
          } else {
            if ($day < $daysInMonth) {
              $day ++;
              $week[] = $day;
            } else {
              $week[] = -1;
            }
          }
        }
        $weeks[] = $week;
      }
      ?>
      <table class="calendar" cellpadding="0" cellspacing="0">
        <tr>
        <?
        foreach (t::m('DAY_SHORT_NAME') as $dayTitle) {
          ?>
          <td class="dayHeader"><?=$dayTitle?></td>
          <?
        }
        ?>
        </tr>
        <?
        foreach ($weeks as $week) {
          ?>
          <tr>
            <?
            foreach ($week as $day) {
              $classes = array();
              if ($currentDay == $day) {
                $classes[] = 'currentDay';
              }
              if ($day == -1) {
                $classes[] = 'noMonthDay';
              }
              ?>
              <td class="<?=implode(' ', $classes)?>">
                <?
                if ($day == -1) {
                  ?>
                  &nbsp;
                  <?
                } else {
                  ?>
                  <div class="cellDayNumber"><?=$day?></div>
                  <?
                  if (key_exists($day, $monthActivities)) {
                    foreach ($monthActivities[$day] as $monthActivityRow) {
                      $typeInternalName = ActivitiesConstants::$ACTIVITIES_INTERNAL_NAMES[$monthActivityRow['type']];
                      $typeName         = t::m('ACTIVITY_NAME__' . $typeInternalName);
                      $currentActivityClass = '';
                      if (($currentActivityId !== NULL) && ($currentActivityId == $monthActivityRow['id'])) {
                        $currentActivityClass = ' cellCurrentActivity';
                      }
                      ?>
                      <div class="cellActivity <?=$currentActivityClass?> activityRow__<?=$typeInternalName?>"><a href="./activity?id=<?=$monthActivityRow['id']?>"><img
                      src="images/icons/activity/Activity__<?=$typeInternalName?>.png"
                      alt="<?=$typeName?>" title="<?=$monthActivityRow['title']?>"/></a></div>
                      <?
                    }
                  }
                  if (key_exists($day, $monthDiaryData)) {
                    foreach ($monthDiaryData[$day] as $diaryRow) {
                      ?>
                      <div class="cellDiary"><a href="./diary-ae?id=<?=$diaryRow['id']?>" title="<?=$diaryRow['text']?>"><?=$diaryRow['categoryTitle']?></a></div>
                      <?
                    }
                  }
                }
                ?>
              </td>
              <?
            }
            ?>
          </tr>
          <?
        }
        ?>
      </table>
    </div>
    <?
  }


  public static function renderPhoto($photoRow, $backURI, $smallPhoto = False) {
    $thumbnailFileName = PhotosMethods::getThumbnailFileName($photoRow['fileName']);
    $classes = array();
    if ($photoRow['isDefault']) $classes[] = 'defaultPhoto';
    if ($smallPhoto) $classes[] = 'smallPhoto';
    ?>
    <div class="photo <?=implode(' ', $classes)?>">
      <a href="<?=$photoRow['fileName']?>" target="_blank"><img src="<?=$thumbnailFileName?>" alt="" /></a>
      <a href="./removePhoto?id=<?=$photoRow['id']?>&backURI=<?=urlencode($backURI)?>" class="photoRemove"
         onclick="return confirm('<?=t::m('PAGE_ACTIVITY_REALY_REMOVE_PHOTO')?>')">X</a>

      <div class="photoRating">
        <?
        for ($rating = Settings::$S['PHOTO_RATING_MIN']; $rating <= Settings::$S['PHOTO_RATING_MAX']; $rating ++) {
          $starClass = '';
          if ($photoRow['rating'] == '') {
            $starSymbol = '&#9734;';
            $starClass = 'inactive';
          } elseif ($photoRow['rating'] < $rating) {
            $starSymbol = '&#9734;';
          } else {
            $starSymbol = '&#9733;';
          }
          ?>
          <a href="./ratePhoto?id=<?=$photoRow['id']?>&rating=<?=$rating?>&backURI=<?=urlencode($backURI)?>" class="<?=$starClass?>"><?=$starSymbol?></a>
          <?
        }
        ?>
      </div>
    </div>
    <?
  }

}
