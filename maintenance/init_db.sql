CREATE TABLE `%%TABLE_PREFIX%%maintenance` (
  `phase` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `%%TABLE_PREFIX%%maintenance` (`phase`) VALUES
(1);


CREATE TABLE `%%TABLE_PREFIX%%activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `gearId` int DEFAULT NULL,
  `type` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `startTime` int NOT NULL,
  `createdTime` int NOT NULL,
  `timeZone` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `isEnabled` tinyint NOT NULL,
  `gpxFileName` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `esType` int NOT NULL,
  `esId` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `distance` int DEFAULT NULL,
  `movingTime` int DEFAULT NULL,
  `elapsedTime` int DEFAULT NULL,
  `elevationGain` float DEFAULT NULL,
  `startLatitude` float DEFAULT NULL,
  `startLongitude` float DEFAULT NULL,
  `locationCity` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `locationDistrict` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `locationCountry` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `maxSpeed` float DEFAULT NULL,
  `averageTemperature` float DEFAULT NULL,
  `averageHeartrate` float DEFAULT NULL,
  `maxHeartrate` int DEFAULT NULL,
  `averageCadence` float DEFAULT NULL,
  `averageWatts` float DEFAULT NULL,
  `maxWatts` float DEFAULT NULL,
  `kilocalories` float DEFAULT NULL,
  `deviceName` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `weatherTemperature` float DEFAULT NULL,
  `weatherCode` int DEFAULT NULL,
  `weatherWindSpeed` float DEFAULT NULL,
  `weatherWindDeg` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `startTime` (`startTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%activityPhoto` (
  `activityId` int NOT NULL,
  `photoId` int NOT NULL,
  PRIMARY KEY (`activityId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%diary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `categoryId` int NOT NULL,
  `eventTime` int NOT NULL,
  `createdTime` int NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `gearId` int DEFAULT NULL,
  `gearSnapshot` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%diaryCategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%diaryPhoto` (
  `diaryId` int NOT NULL,
  `photoId` int NOT NULL,
  PRIMARY KEY (`diaryId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%gear` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `primary` tinyint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `model` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `year` int DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `weight` int DEFAULT NULL,
  `esType` int NOT NULL,
  `esId` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `retiredTime` int DEFAULT NULL,
  `createdTime` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%gearPhoto` (
  `gearId` int NOT NULL,
  `photoId` int NOT NULL,
  PRIMARY KEY (`gearId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `rating` int DEFAULT NULL,
  `isDefault` smallint NOT NULL DEFAULT '0',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `takenTime` int DEFAULT NULL,
  `createdTime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%polylines` (
  `activityId` int NOT NULL,
  `polyline` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`activityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%raceEvents` (
  `activityId` int NOT NULL,
  `raceId` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `time` int DEFAULT NULL,
  `timeLoss` int DEFAULT NULL,
  `rank` int DEFAULT NULL,
  `rankFrom` int DEFAULT NULL,
  `createdTime` int NOT NULL,
  PRIMARY KEY (`activityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%races` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `activityType` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `createdTime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;


CREATE TABLE `%%TABLE_PREFIX%%weights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `measurementTime` int NOT NULL,
  `createdTime` int NOT NULL,
  `weight` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_czech_ci;
