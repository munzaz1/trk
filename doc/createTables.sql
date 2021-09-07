-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `gearId` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `startTime` int(11) NOT NULL,
  `createdTime` int(11) NOT NULL,
  `timeZone` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `isEnabled` tinyint(4) NOT NULL,
  `gpxFileName` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `esType` int(11) NOT NULL,
  `esId` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `distance` int(11) DEFAULT NULL,
  `movingTime` int(11) DEFAULT NULL,
  `elapsedTime` int(11) DEFAULT NULL,
  `elevationGain` float DEFAULT NULL,
  `startLatitude` float DEFAULT NULL,
  `startLongitude` float DEFAULT NULL,
  `locationCity` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `locationDistrict` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `locationCountry` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `maxSpeed` float DEFAULT NULL,
  `averageTemperature` float DEFAULT NULL,
  `averageHeartrate` float DEFAULT NULL,
  `maxHeartrate` int(11) DEFAULT NULL,
  `averageCadence` float DEFAULT NULL,
  `averageWatts` float DEFAULT NULL,
  `maxWatts` float DEFAULT NULL,
  `kilocalories` float DEFAULT NULL,
  `deviceName` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `weatherTemperature` float DEFAULT NULL,
  `weatherCode` int(11) DEFAULT NULL,
  `weatherWindSpeed` float DEFAULT NULL,
  `weatherWindDeg` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `startTime` (`startTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `activityPhoto`;
CREATE TABLE `activityPhoto` (
  `activityId` int(11) NOT NULL,
  `photoId` int(11) NOT NULL,
  PRIMARY KEY (`activityId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `diary`;
CREATE TABLE `diary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `eventTime` int(11) NOT NULL,
  `createdTime` int(11) NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `diaryCategories`;
CREATE TABLE `diaryCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `diaryPhoto`;
CREATE TABLE `diaryPhoto` (
  `diaryId` int(11) NOT NULL,
  `photoId` int(11) NOT NULL,
  PRIMARY KEY (`diaryId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `gear`;
CREATE TABLE `gear` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `primary` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `year` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `weight` int(11) DEFAULT NULL,
  `esType` int(11) NOT NULL,
  `esId` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `retiredTime` int(11) DEFAULT NULL,
  `createdTime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `gearPhoto`;
CREATE TABLE `gearPhoto` (
  `gearId` int(11) NOT NULL,
  `photoId` int(11) NOT NULL,
  PRIMARY KEY (`gearId`,`photoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `isDefault` smallint(6) NOT NULL DEFAULT '0',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `takenTime` int(11) DEFAULT NULL,
  `createdTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `polylines`;
CREATE TABLE `polylines` (
  `activityId` int(11) NOT NULL,
  `polyline` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`activityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `weights`;
CREATE TABLE `weights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `measurementTime` int(11) NOT NULL,
  `createdTime` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2020-05-22 14:00:30
