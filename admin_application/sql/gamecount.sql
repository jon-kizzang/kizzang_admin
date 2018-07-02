ALTER TABLE `kizzang`.`GameCount` DROP COLUMN `token`, DROP INDEX `token_idex` ;
Alter table GameCount add maxGames int unsigned default 0;
Alter table GameCount add updated timestamp default current_timestamp on update current_timestamp;
Alter table GameCount add foreignId int unsigned default 0 after playPeriodId;
Alter table GameCount add theme varchar(20) after gameType;
Alter table GameCount add expirationDate datetime after maxGames;

ALTER TABLE `kizzang`.`GameCount` DROP INDEX `index4` ,ADD UNIQUE INDEX `index4` (`playPeriodId` ASC, `gameType` ASC, `foreignId` ASC, theme ASC);
