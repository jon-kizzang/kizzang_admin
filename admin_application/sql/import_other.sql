Drop database reports;
create database reports;
use reports;
CREATE TABLE `PlayerEvents` (
  `player_id` int(10) unsigned NOT NULL,
  `started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `game_type` enum('Slots','Parlay','Scratchers','Tickets','Login','Sweepstakes','Bracket','Lottery','ROAL') NOT NULL DEFAULT 'Slots',
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `extra` varchar(50) DEFAULT NULL,
  `game_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`player_id`,`started`,`game_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `retention` (
  `ret_date` date NOT NULL,
  `day_diff` int(10) unsigned NOT NULL,
  `ids_started` text,
  `ids_played` text,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ret_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
Drop database rightSignature;
create database rightSignature;
use rightSignature;
CREATE TABLE `attachments` (
  `id` char(36) NOT NULL,
  `documentId` char(36) NOT NULL,
  `action` varchar(200) DEFAULT NULL,
  `downloadUrl` varchar(500) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `audits` (
  `documentId` char(36) NOT NULL,
  `sequence` int(10) unsigned NOT NULL,
  `message` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`documentId`,`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `documents` (
  `id` char(36) NOT NULL,
  `playerId` int(10) unsigned DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `processingState` varchar(50) DEFAULT NULL,
  `tags` varchar(200) DEFAULT NULL,
  `thumbUrl` varchar(500) DEFAULT NULL,
  `signedUrl` varchar(500) DEFAULT NULL,
  `createdDate` datetime DEFAULT NULL,
  `completedDate` datetime DEFAULT NULL,
  `expirationDate` datetime DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `signins` (
  `id` char(36) NOT NULL,
  `playerId` int(10) unsigned NOT NULL,
  `templateId` int(10) unsigned NOT NULL,
  `status` enum('Pending','Complete','Expired') DEFAULT 'Pending',
  `expirationDate` datetime DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `guid` varchar(50) NOT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
INSERT INTO `templates` (`id`,`type`,`guid`) VALUES (1,'W9','a_8116970_3412ed545f794cc1b839e6ea6b6cdff5');
INSERT INTO `templates` (`id`,`type`,`guid`) VALUES (2,'DL','a_16925660_6020442fbc704858889d070f547ca32c');
INSERT INTO `templates` (`id`,`type`,`guid`) VALUES (3,'Notarize','a_17323739_1c157f6c4dec459d8ab46b79b0303626');

Set foreign_key_checks = 0;
Truncate SportGameResults;
Truncate SportParlayCards;
Truncate SportParlayConfig;
Truncate SportParlayPlaces;
Truncate SportPlayerCards;
Truncate SportPlayerPicks;
Truncate PlayerLogins;
Truncate Sessions;
Truncate Tickets;
Truncate TicketAggregate;
Truncate Winners;
Truncate Payments;
Truncate PlayPeriod;
Truncate GameCount;
Truncate Chedda;
ALTER TABLE SportParlayConfig AUTO_INCREMENT = 617;