Drop database ebdb;
Create database ebdb;
use ebdb;
Set FOREIGN_KEY_CHECKS = 0;
CREATE TABLE Scratch_GPCards (
  SerialNumber varchar(64) NOT NULL,
  ScratchId int(4) unsigned NOT NULL,
  PrizeAmount decimal(10,2) NOT NULL,
  PrizeRank tinyint(1) NOT NULL,
  CardNumber int(4) unsigned NOT NULL,
  `Values` int(2) unsigned NOT NULL,
  KEY index_Scratch_GPCards_PrizeAmount (PrizeAmount),
  KEY ScratchId (ScratchId),
  KEY idx_serial_number (SerialNumber),
  KEY idx_prize_rank (PrizeRank)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE Scratch_GPGames (
  ID int(11) NOT NULL AUTO_INCREMENT,
  TotalCards int(8) unsigned NOT NULL,
  TotalWinningCards int(8) unsigned NOT NULL,
  PlayInterval int(4) unsigned NOT NULL,
  SpotsOnCard int(2) unsigned NOT NULL,
  StartDate datetime NOT NULL,
  EndDate datetime NOT NULL,
  WinningSpots int(1) unsigned NOT NULL,
  SerialNumber varchar(64) NOT NULL,
  CardIncrement int(8) unsigned NOT NULL,
  WinningCardIncrement int(8) unsigned NOT NULL,
  WinAmount varchar(30) NOT NULL,
  Card_Count int(8) unsigned NOT NULL,
  Win_Count int(8) unsigned NOT NULL,
  Name varchar(30) NOT NULL,
  Theme varchar(50) NOT NULL,
  Disclaimer varchar(500) DEFAULT NULL,
  DeployWeb tinyint(4) NOT NULL DEFAULT '1',
  DeployMobile tinyint(4) NOT NULL DEFAULT '1',
  PayoutID int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (ID),
  UNIQUE KEY index_ScratchGames_SerialNumber (SerialNumber)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE Scratch_GPLogs (
  SerialNumber varchar(64) NOT NULL,
  Id int(11) NOT NULL AUTO_INCREMENT,
  TimeStamp datetime NOT NULL,
  Message varchar(1024) NOT NULL,
  PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE Scratch_GPPayout (
  PayoutID int(11) NOT NULL,
  Rank tinyint(4) NOT NULL,
  PrizeAmount decimal(10,2) NOT NULL,
  PrizeName varchar(40) NOT NULL,
  TaxableAmount decimal(10,2) NOT NULL,
  Weight int(11) NOT NULL,
  Count int(11) NOT NULL DEFAULT '0',
  WinCount int(11) NOT NULL DEFAULT '0',
  KeyID int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (KeyID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE Scratch_GPPlays (
  SerialNumber varchar(64) NOT NULL,
  TimeStamp datetime NOT NULL,
  PlayerId int(11) NOT NULL,
  ScratchId int(10) unsigned NOT NULL,
  Location tinyint(4) NOT NULL,
  ABTestID int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY index_Scratch_GPPlays_SerialNumber_ScratchId (SerialNumber,ScratchId),
  KEY idx_Scratch_GPPlays_PlayerId (PlayerId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;