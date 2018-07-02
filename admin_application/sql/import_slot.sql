Drop database kizzangslot;
Create database kizzangslot;
use kizzangslot;
set FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `Players` (
  `PlayerID` bigint(20) unsigned NOT NULL,
  `TournamentID` int(10) unsigned NOT NULL,
  `Token` char(40) DEFAULT NULL,
  `SessionID` bigint(20) unsigned DEFAULT NULL,
  `TournamentList` text,
  `ScreenName` char(25) NOT NULL,
  `FacebookID` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`PlayerID`),
  UNIQUE KEY `Token` (`Token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `SlotGame` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Theme` varchar(50) NOT NULL,
  `Math` varchar(50) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `SpinsTotal` smallint(5) unsigned NOT NULL,
  `SecsTotal` mediumint(8) unsigned NOT NULL,
  `Disclaimer` varchar(1000) DEFAULT NULL,
  `CreateDate` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (1,'Angry Chefs','angrychefs','angrychefs','00:00:00','23:59:59',26,360,'  ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (2,'Bankroll Bandits','bankrollbandits','bankrollbandits','00:00:00','23:59:59',26,240,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (3,'Butterfly Treasures','butterflytreasures','butterflytreasures','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (5,'Romancing Riches','romancingriches','romancingriches','00:00:00','23:59:59',26,300,' ','2014-12-07 22:06:47');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (6,'Undersea World 2','underseaworld2','underseaworld2','00:00:00','23:59:59',26,300,' ','2015-02-26 02:47:34');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (7,'Oak in the Kitchen','oakinthekitchen','oakinthekitchen','00:00:00','23:59:59',26,300,' ','2015-05-21 22:04:34');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (8,'Crusader''s Quest','crusadersquest','crusadersquest','00:00:00','23:59:59',26,300,' ','2015-06-01 17:56:15');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (9,'Mummy''s Revenge','mummysrevenge','mummysrevenge','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (10,'Ghost Treasures','ghosttreasures','ghosttreasures','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (11,'Penguin Riches','penguinriches','penguinriches','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (12,'Bounty','bounty','bounty','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (13,'Fat Cat 7s','fatcat7','fatcat7','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (14,'Holiday Joy','holidayjoy','holidayjoy','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (15,'Happy New Year','happynewyear','happynewyear','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (16,'Gummibar','gummibar','gummibar','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (17,'April Madness','aprilmadness','aprilmadness','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (18,'Money Booth','moneybooth','moneybooth','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (19,'Xtreme Cash Explosion','xtremecash','xtremecash','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (20,'Astrology Answers','astrologyanswers','astrologyanswers','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (21,'Firehouse Frenzy','firehousefrenzy','firehousefrenzy','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (23,'Monkey Mayhem','monkeymadness','monkeymadness','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (24,'Diamond Streak','diamondstreak','diamondstreak','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
INSERT INTO `SlotGame` (`ID`,`Name`,`Theme`,`Math`,`StartTime`,`EndTime`,`SpinsTotal`,`SecsTotal`,`Disclaimer`,`CreateDate`) VALUES (25,'Payment Panic','paymentpanic','paymentpanic','00:00:00','23:59:59',26,300,' ','0000-00-00 00:00:00');
CREATE TABLE `SlotServer` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Host` varchar(200) NOT NULL,
  `Port` int(10) unsigned NOT NULL,
  `CryptoOn` tinyint(3) unsigned NOT NULL,
  `CryptoKey` varchar(100) NOT NULL,
  `Debug` tinyint(3) unsigned NOT NULL,
  `MathList` text NOT NULL,
  `MaxConnections` int(10) unsigned NOT NULL,
  `StartDate` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `SlotTournament` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `PrizeList` text,
  `type` enum('Daily','Weekly','Monthly') NOT NULL DEFAULT 'Daily',
  `GameIDs` set('paymentpanic','diamondstreak','monkeymadness','firehousefrenzy','xtremecash','astrologyanswers','gummibar','moneybooth','aprilmadness','bounty','fatcat7','angrychefs','bankrollbandits','butterflytreasures','romancingriches','underseaworld2','oakinthekitchen','crusadersquest','mummysrevenge','ghosttreasures','penguinriches','holidayjoy','happynewyear') DEFAULT NULL,
  `Title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `SPBK` (
  `PlayerID` bigint(20) unsigned NOT NULL,
  `TournamentID` int(10) unsigned NOT NULL,
  `PlayerToken` char(40) NOT NULL,
  `LastSessionTime` bigint(20) unsigned DEFAULT NULL,
  `WinTotal` bigint(20) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `UsedTokens` (
  `Token` char(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`Token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
Drop database kizzangslot_archive;
Create database kizzangslot_archive;
CREATE TABLE kizzangslot_archive.SlotAggregate (
  SlotTournamentId int(10) unsigned NOT NULL,
  SessionId int(10) unsigned NOT NULL,
  PlayerId int(10) unsigned NOT NULL,
  GameId int(10) unsigned NOT NULL,
  SpinsLeft int(10) unsigned NOT NULL,
  Rank int(10) unsigned NOT NULL,
  WinTotal int(10) unsigned NOT NULL,
  completed datetime DEFAULT NULL,
  time_elapsed int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (SlotTournamentId,SessionId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE SlotTournament AUTO_INCREMENT = 587;

Truncate SportGameResults;
Truncate SportParlayCards;
Truncate SportParlayConfig;
Truncate SportParlayPlaces;
Truncate SportPlayerCards;
Truncate SportPlayerPicks;
Truncate PlayerLogins;
Truncate Sessions;
Truncate Tickets;
Truncate TicketsAggregate;
Truncate Winners;
Truncate Payments;
Truncate PlayPeriod;
Truncate GameCount;
Truncate Chedda;