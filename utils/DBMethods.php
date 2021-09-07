<?

class DBMethods {

  // common methods {{{

  /*
  public static function getSQLTimeFromTimestamp($timestamp) {
    return "'" . date('Y-m-d H:i:s', $timestamp) . "'";
  }


  public static function getSQLTimeUTCFromTimestamp($timestamp) {
    return "'" . gmdate('Y-m-d H:i:s', $timestamp) . "'";
  }


  public static function getSQLTimeFromTimeString($timeString) {
    if ($timeString == '') {
      return 'NULL';
    } else {
      return "'" . date('Y-m-d H:i:s', strtotime($timeString)) . "'";
    }
  }


  public static function getTimestampFromSQLTime($sqlTime) {
    return strtotime($sqlTime);
  }
  */


  public static function stringOrNULL($value) {
    if ($value === NULL) {
      return "NULL";
    } else {
      return "'$value'";
    }
  }


  public static function valueOrNULL($value) {
    if ($value === NULL) {
      return "NULL";
    } else {
      return "$value";
    }
  }

  // }}}

  // activities related {{{

  public static function registerActivity($db, $userId, $gearId, $type, $title, $description, $startTime, $gpxFileName, $esType, $esId,
                                          $activityStats, $polyline) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` SET " .
             "`userId`           = $userId, " .
             "`gearId`           = " . DBMethods::valueOrNULL($gearId) . ", " .
             "`type`             = $type, " .
             "`title`            = '" . $db->escapeString($title) . "', " .
             "`description`      = '" . $db->escapeString($description) . "', " .
             "`startTime`        = " . $startTime . ", " .
             "`createdTime`      = " . time() . ", " .
             "`timeZone`         = '" . Settings::$S['TIME_ZONE'] . "', " .
             "`isEnabled`        = 1," .
             "`gpxFileName`      = '" . $db->escapeString($gpxFileName) . "', " .
             "`esType`           = $esType, " .
             "`esId`             = '" . $db->escapeString($esId) . "', " .

             "`distance`              = " . DBMethods::valueOrNULL( $activityStats->distance            ) . ", " .
             "`movingTime`            = " . DBMethods::valueOrNULL( $activityStats->movingTime          ) . ", " .
             "`elapsedTime`           = " . DBMethods::valueOrNULL( $activityStats->elapsedTime         ) . ", " .
             "`elevationGain`         = " . DBMethods::valueOrNULL( $activityStats->elevationGain       ) . ", " .
             "`startLatitude`         = " . DBMethods::valueOrNULL( $activityStats->startLatitude       ) . ", " .
             "`startLongitude`        = " . DBMethods::valueOrNULL( $activityStats->startLongitude      ) . ", " .
             "`locationCity`          = " . DBMethods::stringOrNULL($activityStats->locationCity        ) . ", " .
             "`locationDistrict`      = " . DBMethods::stringOrNULL($activityStats->locationDistrict    ) . ", " .
             "`locationCountry`       = " . DBMethods::stringOrNULL($activityStats->locationCountry     ) . ", " .
             "`maxSpeed`              = " . DBMethods::valueOrNULL( $activityStats->maxSpeed            ) . ", " .
             "`averageTemperature`    = " . DBMethods::valueOrNULL( $activityStats->averageTemperature  ) . ", " .
             "`averageHeartrate`      = " . DBMethods::valueOrNULL( $activityStats->averageHeartrate    ) . ", " .
             "`maxHeartrate`          = " . DBMethods::valueOrNULL( $activityStats->maxHeartrate        ) . ", " .
             "`averageCadence`        = " . DBMethods::valueOrNULL( $activityStats->averageCadence      ) . ", " .
             "`averageWatts`          = " . DBMethods::valueOrNULL( $activityStats->averageWatts        ) . ", " .
             "`maxWatts`              = " . DBMethods::valueOrNULL( $activityStats->maxWatts            ) . ", " .
             "`kilocalories`          = " . DBMethods::valueOrNULL( $activityStats->kilocalories        ) . ", " .
             "`deviceName`            = " . DBMethods::stringOrNULL($activityStats->deviceName          ) . ", " .
             "`weatherTemperature`    = " . DBMethods::valueOrNULL( $activityStats->weatherTemperature  ) . ", " .
             "`weatherCode`           = " . DBMethods::valueOrNULL( $activityStats->weatherCode         ) . ", " .
             "`weatherWindSpeed`      = " . DBMethods::valueOrNULL( $activityStats->weatherWindSpeed    ) . ", " .
             "`weatherWindDeg`        = " . DBMethods::valueOrNULL( $activityStats->weatherWindDeg      ) . " " .
             ";";
    $db->query($query);
    $activityId = $db->lastInsertedId();

    if ($polyline !== NULL) {
      $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "polylines` SET " .
               "`activityId`       = $activityId, " .
               "`polyline`         = '" . $db->escapeString($polyline) . "' " .
               ";";
      $db->query($query);
    }
    return $activityId;
  }


  public static function updateActivity($db, $userId, $activityId, $gearId, $type, $title, $description, $startTime, $gpxFileName, $esType, $esId, $activityStats) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` SET " .
             "`gearId`           = " . DBMethods::valueOrNULL($gearId) . ", " .
             "`type`             = $type, " .
             "`title`            = '" . $db->escapeString($title) . "', " .
             "`description`      = '" . $db->escapeString($description) . "', " .
             "`startTime`        = " . $startTime . ", " .
             "`gpxFileName`      = '" . $db->escapeString($gpxFileName) . "', " .
             "`esType`           = $esType, " .
             "`esId`             = '" . $db->escapeString($esId) . "' " .
             "WHERE (`id` = '$activityId') AND (`userId` = $userId);";
    $db->query($query);

    DBMethods::updateActivityStats($db, $userId, $activityId, $activityStats);
  }


  public static function updateActivityStats($db, $userId, $activityId, $activityStats) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` SET " .
             "`distance`              = " . DBMethods::valueOrNULL( $activityStats->distance            ) . ", " .
             "`movingTime`            = " . DBMethods::valueOrNULL( $activityStats->movingTime          ) . ", " .
             "`elapsedTime`           = " . DBMethods::valueOrNULL( $activityStats->elapsedTime         ) . ", " .
             "`elevationGain`         = " . DBMethods::valueOrNULL( $activityStats->elevationGain       ) . ", " .
             "`startLatitude`         = " . DBMethods::valueOrNULL( $activityStats->startLatitude       ) . ", " .
             "`startLongitude`        = " . DBMethods::valueOrNULL( $activityStats->startLongitude      ) . ", " .
             "`locationCity`          = " . DBMethods::stringOrNULL($activityStats->locationCity        ) . ", " .
             "`locationDistrict`      = " . DBMethods::stringOrNULL($activityStats->locationDistrict    ) . ", " .
             "`locationCountry`       = " . DBMethods::stringOrNULL($activityStats->locationCountry     ) . ", " .
             "`maxSpeed`              = " . DBMethods::valueOrNULL( $activityStats->maxSpeed            ) . ", " .
             "`averageTemperature`    = " . DBMethods::valueOrNULL( $activityStats->averageTemperature  ) . ", " .
             "`averageHeartrate`      = " . DBMethods::valueOrNULL( $activityStats->averageHeartrate    ) . ", " .
             "`maxHeartrate`          = " . DBMethods::valueOrNULL( $activityStats->maxHeartrate        ) . ", " .
             "`averageCadence`        = " . DBMethods::valueOrNULL( $activityStats->averageCadence      ) . ", " .
             "`averageWatts`          = " . DBMethods::valueOrNULL( $activityStats->averageWatts        ) . ", " .
             "`maxWatts`              = " . DBMethods::valueOrNULL( $activityStats->maxWatts            ) . ", " .
             "`kilocalories`          = " . DBMethods::valueOrNULL( $activityStats->kilocalories        ) . ", " .
             "`deviceName`            = " . DBMethods::stringOrNULL($activityStats->deviceName          ) . ", " .
             "`weatherTemperature`    = " . DBMethods::valueOrNULL( $activityStats->weatherTemperature  ) . ", " .
             "`weatherCode`           = " . DBMethods::valueOrNULL( $activityStats->weatherCode         ) . ", " .
             "`weatherWindSpeed`      = " . DBMethods::valueOrNULL( $activityStats->weatherWindSpeed    ) . ", " .
             "`weatherWindDeg`        = " . DBMethods::valueOrNULL( $activityStats->weatherWindDeg      ) . " " .
             "WHERE (`id` = '" . $db->escapeString($activityId) . "') AND (`userId` = $userId);";
             ";";
    $db->query($query);
  }


  public static function getActivitiesRows($db, $userId, $fromTime, $toTime) {
    $query = "SELECT a.*, g.name AS gearName, COUNT(ap.activityId) AS photosCount " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` AS a " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` AS g ON (g.id = a.gearId) " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` AS ap ON (a.id = ap.activityId) " .
             "WHERE (a.`userId` = $userId) AND (`startTime` >= $fromTime) AND (`startTime` <= $toTime) AND (`isEnabled` = 1) " .
             "GROUP BY a.id " .
             "ORDER BY `startTime` ASC, `title`;";
    return $db->rows($query);
  }


  public static function getActivitiesRowsByIds($db, $userId, $activitiesIds) {
    $query = "SELECT a.*, g.name AS gearName FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` AS a " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` AS g ON (g.`id` = a.`gearId`) " .
             "WHERE (a.`userId` = $userId) AND (a.id IN (" . implode(", ", $activitiesIds) . ")) " .
             "ORDER BY `startTime` ASC, `title`;";
    return $db->rows($query);
  }


  public static function getActivityRow($db, $userId, $activityId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`id` = '" . $db->escapeString($activityId) . "') AND (`userId` = $userId) AND (`isEnabled` = 1);";
    return $db->row($query);
  }


  public static function getLastActivityRow($db, $userId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) AND (`isEnabled` = 1) " .
             "ORDER BY `startTime` DESC " .
             "LIMIT 1;";
    return $db->row($query);
  }


  public static function getESIdsFromTime($db, $userId, $esType, $timeFrom, $timeTo) {
    $query = "SELECT esId FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) AND (`esType` = $esType) AND (`isEnabled` = 1) AND (`startTime` >= $timeFrom) AND (`startTime` <= $timeTo);";
    return $db->column($query, 'esId');
  }


  public static function getActivityPolylineRow($db, $activityId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "polylines` " .
             "WHERE `activityId` = '" . $db->escapeString($activityId) . "';";
    return $db->row($query);
  }


  public static function getLastGPXFilesNames($db, $userId) {
    $query = "SELECT gpxFileName FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) " .
             "ORDER BY `startTime` DESC " .
             "LIMIT " . Settings::$S['LIMIT_FOR_DETMINING_LOADED_GPX_FILES'] . ";";
    return $db->column($query, 'gpxFileName');
  }


  public static function getActivityIdsTitlesByStartTime($db, $userId, $startTime) {
    $query = "SELECT id, title FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) and (isEnabled = 1) AND (`startTime` = $startTime);";
    return $db->rows($query);
  }


  public static function getActivityIdsTitlesByGPXFileName($db, $userId, $gpxFileName) {
    $query = "SELECT id, title FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) and (`isEnabled` = 1) AND (`gpxFileName` LIKE '" . $db->escapeString($gpxFileName) . "');";
    return $db->rows($query);
  }


  public static function getMinMaxActivityYears($db, $userId) {
    $query = "SELECT MIN(startTime) AS minStartTime, MAX(startTime) AS maxStartTime " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) and (`isEnabled` = 1);";
    $minMaxStartTimes = $db->row($query);

    if (!$minMaxStartTimes['minStartTime']) {
      return array(NULL, NULL);
    } else {
      $minYear = date('Y', $minMaxStartTimes['minStartTime']);
      $maxYear = date('Y', $minMaxStartTimes['maxStartTime']);
      return array($minYear, $maxYear);
    }
  }


  public static function getPreviousAndNextActivities($db, $userId, $startTime) {
    $query = "SELECT * " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) and (`isEnabled` = 1) AND (`startTime` < $startTime) " .
             "ORDER BY `startTime` DESC LIMIT 1;";
    $previousActivityRow = $db->row($query);

    $query = "SELECT * " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) and (`isEnabled` = 1) AND (`startTime` > $startTime) " .
             "ORDER BY `startTime` ASC LIMIT 1;";
    $nextActivityRow = $db->row($query);

    return array($previousActivityRow, $nextActivityRow);
  }


  public static function getMonthActivitiesByMonthYear($db, $userId, $month, $year, $activityType = ActivitiesConstants::AT_ANY) {
    $timeFrom     = mktime(0, 0, 0, $month, 1, $year);
    $daysInMonth  = date('t', $timeFrom);
    $timeTo       = $timeFrom + ($daysInMonth * TimeConstants::SECONDS_IN_DAY);

    $query = "SELECT a.*, re.title AS re_title " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` a " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "raceEvents` re ON a.id = re.activityId " .
             "WHERE (a.`userId` = $userId) AND (a.`isEnabled` = 1) AND " .
             "(a.`startTime` >= $timeFrom) AND (a.`startTime` <= $timeTo) AND " .
             ($activityType === ActivitiesConstants::AT_ANY ? "(1) " : "(a.type = $activityType) ") .
             "ORDER BY startTime ASC;";
    $rows = $db->rows($query);
    $monthActivities = array();
    foreach ($rows as $row) {
      $activityDay  = date('j', $row['startTime']);
      if (key_exists($activityDay, $monthActivities)) {
        $monthActivities[$activityDay][] = $row;
      } else {
        $monthActivities[$activityDay] = array($row);
      }
    }
    return $monthActivities;
  }


  public static function canBeActivityRemoved($db, $userId, $activityId) {
    $canBeRemoved = True;

    // check associated photos {{{
    $query = "SELECT COUNT(*) FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` " .
             "WHERE (`activityId` = $activityId);";
    if ($db->value($query) > 0) {
      $canBeRemoved = False;
    }
    // }}}

    return $canBeRemoved;
  }


  public static function getAllUsedActivitiesTypes($db, $userId) {
    $query = "SELECT DISTINCT(type) FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` ".
             "WHERE (`userId` = $userId);";
    return $db->column($query, 'type');
  }


  public static function removeActivity($db, $userId, $activityId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) AND (`id` = $activityId);";
    $db->query($query);

    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "polylines` " .
             "WHERE (`activityId` = $activityId);";
    $db->query($query);

    // XXX TODO: remove photos?
  }

  // }}}


  // gear related {{{

  public static function registerGear($db, $userId, $primary, $name, $brand, $model, $description, $type, $weight, $esType, $esId) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
             "`userId`          = $userId, " .
             "`primary`         = $primary, " .
             "`name`            = '" . $db->escapeString($name) . "', " .
             "`brand`           = '" . $db->escapeString($brand) . "', " .
             "`model`           = '" . $db->escapeString($model) . "', " .
             "`description`     = '" . $db->escapeString($description) . "', " .
             "`type`            = '" . $db->escapeString($type) . "', " .
             "`weight`          = " . DBMethods::valueOrNULL($weight) . ", " .
             "`esType`          = $esType, " .
             "`esId`            = '" . $db->escapeString($esId) . "', " .
             "`createdTime`     = " . time() . " " .
             ";";
    $db->query($query);
    $gearId = $db->lastInsertedId();
    return $gearId;
  }


  public static function getGearRowByESTypeESId($db, $userId, $esType, $esId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` " .
             "WHERE (`userId` = $userId) AND (`esType` = $esType) AND (`esId` = '" . $db->escapeString($esId) . "');";
    $rows = $db->rows($query);
    if (count($rows) == 0) {
      return NULL;
    } elseif (count($rows) == 1) {
      return $rows[0];
    } else {
      throw new ErrorDataInconsistency(t::m('ERROR_TOO_MANY_ASSOCIATED_GEAR_RECORDS') .
        ' (' . ExternalSourcesConstants::$EXTERNAL_SOURCE_NAMES[$esType] . '#' . $esId . ')');
    }
  }


  public static function getGearRow($db, $userId, $gearId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` " .
             "WHERE (`userId` = $userId) AND (`id` = $gearId);";
    return $db->row($query);
  }


  public static function getGearRows($db, $userId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` " .
             "WHERE (`userId` = $userId) ORDER BY `type`, `name`;";
    return $db->rows($query);
  }


  public static function getGearRowsWithDistance($db, $userId) {
    $query = "SELECT g.*, SUM(a.distance) AS distance FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` AS g " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` AS a ON a.gearId = g.id " .
             "WHERE (g.`userId` = $userId) " .
             "GROUP BY g.id " .
             "ORDER BY g.`type`, g.`name`;";
    return $db->rows($query);
  }


  public static function getGearDictForSelect($db, $userId, $addEmptyValue = True) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` AS g " .
             "WHERE (`userId` = $userId) " .
             "ORDER BY g.`type`, g.`name`;";
    $rows = $db->rows($query);

    $dict = array();
    if ($addEmptyValue) {
      $dict[Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX']] = '';
    }
    foreach ($rows as $row) {
      $dict[$row['id']] = $row['name'] . ' | ' . t::m('GEAR_NAME__' . GearConstants::$GEAR_INTERNAL_NAMES[$row['type']]);
    }
    return $dict;
  }


  public static function updateGear($db, $userId, $gearId, $primary, $name, $brand, $model, $description, $type, $weight, $esType, $esId) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
             "`primary`         = $primary, " .
             "`name`            = '" . $db->escapeString($name) . "', " .
             "`brand`           = '" . $db->escapeString($brand) . "', " .
             "`model`           = '" . $db->escapeString($model) . "', " .
             "`description`     = '" . $db->escapeString($description) . "', " .
             "`type`            = '" . $db->escapeString($type) . "', " .
             "`weight`          = " . DBMethods::valueOrNULL($weight) . ", " .
             "`esType`          = $esType, " .
             "`esId`            = '" . $db->escapeString($esId) . "' " .
             "WHERE (`id` = '" . $db->escapeString($gearId) . "') AND (`userId` = $userId);";
    $db->query($query);
  }


  public static function canBeGearRemoved($db, $userId, $gearId) {
    $canBeRemoved = True;

    // check associated activities {{{
    $query = "SELECT COUNT(*) FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` " .
             "WHERE (`userId` = $userId) AND (`gearId` = $gearId);";
    if ($db->value($query) > 0) {
      $canBeRemoved = False;
    }
    // }}}

    // XXX TODO: check diary, check photos

    return $canBeRemoved;
  }


  public static function removeGear($db, $userId, $gearId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` " .
             "WHERE (`userId` = $userId) AND (`id` = $gearId);";
    $db->query($query);

    // XXX TODO: remove photos?
  }


  public static function setPrimaryGear($db, $userId, $gearId) {
    $gearRow = DBMethods::getGearRow($db, $userId, $gearId);
    if ($gearRow !== NULL) {
      $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
               "`primary` = 0 " .
               "WHERE (`userId` = $userId) AND (`type` = " . $gearRow['type'] . ");";
      $db->query($query);

      $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
               "`primary` = 1 " .
               "WHERE (`userId` = $userId) AND (`id` = $gearId);";
      $db->query($query);
    }
  }


  public static function setGearAsRetired($db, $userId, $gearId) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
             "`retiredTime` = " . time() . " " .
             "WHERE (`userId` = $userId) AND (`id` = $gearId);";
    $db->query($query);
  }


  public static function setGearAsNotRetired($db, $userId, $gearId) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "gear` SET " .
             "`retiredTime` = NULL " .
             "WHERE (`userId` = $userId) AND (`id` = $gearId);";
    $db->query($query);
  }

  // }}}


  // weight related {{{

  public static function registerWeight($db, $userId, $measurementTime, $weight) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "weights` SET " .
             "`userId`          = $userId, " .
             "`measurementTime` = $measurementTime, " .
             "`weight`          = $weight, " .
             "`createdTime`     = " . time() . " " .
             ";";
    $db->query($query);
    $id = $db->lastInsertedId();
    return $id;
  }


  public static function getWeightsRowsByMeasurementTime($db, $userId, $timeFrom, $timeTo) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "weights` " .
             "WHERE (`userId` = $userId) AND (`measurementTime` >= $timeFrom) AND (`measurementTime` <= $timeTo) " .
             "ORDER BY `measurementTime` DESC;";
    return $db->rows($query);
  }


  public static function removeWeight($db, $userId, $weightId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "weights` " .
             "WHERE (`userId` = $userId) AND (`id` = $weightId);";
    $db->query($query);
  }

  // }}}


  // photos related {{{

  public static function getPhotoRowById($db, $photoId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` WHERE id = $photoId ";
    return $db->row($query);
  }


  public static function registerPhoto($db, $fileName, $description, $rating, $isDefault, $latitude, $longitude, $takenTime) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` SET " .
             "`fileName`        = '" . $db->escapeString($fileName) . "', " .
             "`description`     = '" . $db->escapeString($description) . "', " .
             "`rating`          = " . DBMethods::stringOrNULL($rating) . ", " .
             "`isDefault`       = " . $isDefault . ", " .
             "`latitude`        = " . DBMethods::stringOrNULL($latitude) . ", " .
             "`longitude`       = " . DBMethods::stringOrNULL($longitude) . ", " .
             "`takenTime`       = " . DBMethods::stringOrNULL($takenTime) . ", " .
             "`createdTime`     = " . time() . " " .
             ";";
    $db->query($query);
    $id = $db->lastInsertedId();
    return $id;
  }


  public static function registerPhoto_Activity($db, $activityId, $fileName, $description, $rating, $isDefault, $latitude, $longitude, $takenTime) {
    $photoId = DBMethods::registerPhoto($db, $fileName, $description, $rating, $isDefault, $latitude, $longitude, $takenTime);
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` SET " .
             "`activityId`    = $activityId, " .
             "`photoId`       = $photoId " .
             ";";
    $db->query($query);
  }


  public static function getPhotosForActivity($db, $activityId) {
    $query = "SELECT p.* FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` AS p " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` AS ap ON (p.id = ap.photoId) " .
             "WHERE (ap.`activityId` = $activityId) " .
             "ORDER BY p.`isDefault` DESC, p.`rating` DESC, p.`takenTime` DESC, p.id;";
    return $db->rows($query);
  }


  public static function getPhotosForActivities($db, $activitiesIds) {
    if (count($activitiesIds) == 0) {
      return array();
    }
    $query = "SELECT p.* FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` AS p " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` AS ap ON (p.id = ap.photoId) " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "activities` AS a ON (a.id = ap.activityId) " .
             "WHERE (ap.`activityId` IN (" . implode(', ', $activitiesIds) . ")) " .
             "ORDER BY a.`startTime`, p.`isDefault` DESC, p.`rating` DESC, p.`takenTime` DESC, p.id;";
    return $db->rows($query);
  }


  public static function getPhotosForGear($db, $gearId) {
    $query = "SELECT p.* FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` AS p " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "gearPhoto` AS gp ON (p.id = gp.photoId) " .
             "WHERE (gp.`gearId` = $gearId) " .
             "ORDER BY p.`isDefault` DESC, p.`rating` DESC, p.`takenTime` DESC, p.id;";
    return $db->rows($query);
  }


  public static function registerPhoto_Gear($db, $gearId, $fileName, $description, $rating, $isDefault, $latitude, $longitude, $takenTime) {
    $photoId = DBMethods::registerPhoto($db, $fileName, $description, $rating, $isDefault, $latitude, $longitude, $takenTime);
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "gearPhoto` SET " .
             "`gearId`  = $gearId, " .
             "`photoId` = $photoId " .
             ";";
    $db->query($query);
  }


  public static function removePhoto($db, $photoId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` " .
             "WHERE (`id` = $photoId);";
    $db->query($query);

    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "activityPhoto` " .
             "WHERE (`photoId` = $photoId);";
    $db->query($query);

    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "gearPhoto` " .
             "WHERE (`photoId` = $photoId);";
    $db->query($query);
  }


  public static function ratePhoto($db, $photoId, $rating) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "photos` " .
             "SET rating = $rating " .
             "WHERE (`id` = $photoId);";
    $db->query($query);
  }

  // }}}


  // diary related {{{

  public static function getDiaryRow($db, $userId, $diaryId) {
    $query = "SELECT d.*, dc.title AS categoryTitle " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` d " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` dc ON dc.id = d.categoryId " .
             "WHERE (d.`userId` = $userId) AND (d.`id` = $diaryId);";
    return $db->row($query);
  }


  public static function getDiaryDataByMonthYear($db, $userId, $month, $year) {
    $timeFrom     = mktime(0, 0, 0, $month, 1, $year);
    $daysInMonth  = date('t', $timeFrom);
    $timeTo       = $timeFrom + ($daysInMonth * TimeConstants::SECONDS_IN_DAY);

    $query = "SELECT d.id, d.eventTime, d.createdTime, d.text, dc.title AS categoryTitle " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` d " .
             "LEFT JOIN `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` dc ON dc.id = d.categoryId " .
             "WHERE (d.`userId` = $userId) AND (d.`eventTime` >= $timeFrom) AND (d.`eventTime` <= $timeTo) " .
             "ORDER BY d.`eventTime` ASC;";
    $rows = $db->rows($query);
    $monthDiaryData = array();
    foreach ($rows as $row) {
      $day  = date('j', $row['eventTime']);
      if (key_exists($day, $monthDiaryData)) {
        $monthDiaryData[$day][] = $row;
      } else {
        $monthDiaryData[$day] = array($row);
      }
    }
    return $monthDiaryData;
  }


  public static function getMinMaxDiaryYears($db, $userId) {
    $query = "SELECT MIN(eventTime) AS minEventTime, MAX(eventTime) AS maxEventTime " .
             "FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` " .
             "WHERE (`userId` = $userId);";
    $minMaxEventTimes = $db->row($query);

    if (!$minMaxEventTimes['minEventTime']) {
      return array(NULL, NULL);
    } else {
      $minYear = date('Y', $minMaxEventTimes['minEventTime']);
      $maxYear = date('Y', $minMaxEventTimes['maxEventTime']);
      return array($minYear, $maxYear);
    }
  }


  public static function getDiaryCategoriesDictForSelect($db, $userId, $addEmptyValue = True) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` " .
             "WHERE (`userId` = $userId) " .
             "ORDER BY `title`;";
    $rows = $db->rows($query);

    $dict = array();
    if ($addEmptyValue) {
      $dict[Settings::$S['EMPTY_VALUE_FOR_SELECT_BOX']] = '';
    }
    foreach ($rows as $row) {
      $dict[$row['id']] = $row['title'];
    }
    return $dict;
  }


  public static function getDiaryCategoryRow($db, $userId, $diaryCategoryId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` " .
             "WHERE (`userId` = $userId) AND (`id` = $diaryCategoryId);";
    return $db->row($query);
  }


  public static function registerDiaryCategory($db, $userId, $title) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` SET " .
             "`userId`          = $userId, " .
             "`title`           = '" . $db->escapeString($title) . "' " .
             ";";
    $db->query($query);
    $id = $db->lastInsertedId();
    return $id;
  }


  public static function updateDiaryCategory($db, $userId, $fDiaryCategoryId, $title) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` SET " .
             "`title`           = '" . $db->escapeString($title) . "' " .
             "WHERE (`id` = '" . $db->escapeString($fDiaryCategoryId) . "') AND (`userId` = $userId);";
    $db->query($query);
  }


  public static function removeDiaryCategory($db, $userId, $fDiaryCategoryId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` " .
             "WHERE (`userId` = $userId) AND (`id` = $fDiaryCategoryId);";
    $db->query($query);
  }


  public static function getDiaryCategoriesRowsWithDiaryItems($db, $userId) {
    $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diaryCategories` " .
             "WHERE (`userId` = $userId) " .
             "ORDER BY `title`;";
    $diaryCategoriesRows = $db->rows($query);

    foreach ($diaryCategoriesRows as $index => $diaryCategoryRow) {
      $query = "SELECT * FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` " .
               "WHERE (categoryId = " . $diaryCategoryRow['id'] . ") " .
               "ORDER BY `eventTime` ASC;";
      $diaryRows = $db->rows($query);
      $diaryCategoriesRows[$index]['diaryItems'] = $diaryRows;
    }
    return $diaryCategoriesRows;
  }


  public static function registerDiaryItem($db, $userId, $categoryId, $eventTime, $text) {
    $query = "INSERT INTO `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` SET " .
             "`userId`          = $userId, " .
             "`categoryId`      = $categoryId, " .
             "`eventTime`       = $eventTime, " .
             "`createdTime`     = " . time() . ", " .
             "`text`            = '" . $db->escapeString($text) . "' " .
             ";";
    $db->query($query);
    $id = $db->lastInsertedId();
    return $id;
  }


  public static function updateDiaryItem($db, $userId, $diaryId, $categoryId, $eventTime, $text) {
    $query = "UPDATE `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` SET " .
             "`categoryId`      = $categoryId, " .
             "`eventTime`       = $eventTime, " .
             "`text`            = '" . $db->escapeString($text) . "' " .
             "WHERE (`id` = '" . $db->escapeString($diaryId) . "') AND (`userId` = $userId);";
    $db->query($query);
  }


  public static function removeDiaryItem($db, $userId, $diaryId) {
    $query = "DELETE FROM `" . Settings::$S['DB_TABLE_PREFIX'] . "diary` " .
             "WHERE (`userId` = $userId) AND (`id` = $diaryId);";
    $db->query($query);
  }

  // }}}

}

?>
