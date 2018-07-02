Create table Chedda (
playerId int unsigned NOT NULL,
gameKey varchar(50) NOT NULL,
isUsed int unsigned default 0,
count int unsigned NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp,
primary key (playerId, isUsed, gameKey));

ALTER TABLE `kizzang`.`Chedda` ADD INDEX `idx_gameKey` (`gameKey` ASC);
ALTER TABLE `kizzang`.`Winners` CHANGE COLUMN `game_type` `game_type` ENUM('Slots','Scratchers','Sweepstakes','Parlay','BG','FT','Store') NOT NULL ;
ALTER TABLE `kizzang`.`SportParlayConfig` CHANGE COLUMN `type` `type` ENUM('profootball','collegefootball','ptbdailyshowdown','sicollegebasketball','sidailyshowdown','guestdailyshowdown') NULL DEFAULT 'ptbdailyshowdown' ;
ALTER TABLE `kizzang`.`Payouts` CHANGE COLUMN `gameType` `gameType` ENUM('Daily Slot','Weekly Slot','Monthly Slot','Parlay','Guest Parlay') NULL DEFAULT NULL ;
Alter table kizzang.GameLeaderBoards add foreign_key int unsigned default 0 after game_sub_type;
Alter table EventNotifications change updated updated timestamp default current_timestamp on update current_timestamp;
Alter table EventNotifications change added added timestamp default current_timestamp;

ALTER TABLE `kizzang`.`EventNotifications` CHANGE COLUMN `pending` `pending` TINYINT(1) NOT NULL DEFAULT 1 ;

ALTER TABLE `kizzang`.`Configs` CHANGE COLUMN `data_type` `data_type` ENUM('Numeric','Text','JSON','XML','Serialized','File') NULL DEFAULT 'Text' ;

Alter table kizzang.SportParlayConfig add maxCardCount int unsigned default 1 after serialNumber;

/* FOR THE SCRATCH CARD DB */
Alter table Scratch_GPGames add Theme varchar(50) NOT NULL after Name;