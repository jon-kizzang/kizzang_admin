Create table FinalConfigs (
id int unsigned primary key auto_increment,
startDate datetime NOT NULL,
endDate datetime NOT NULL,
serialNumber char(7),
prizes varchar(50),
theme varchar(20),
pickHash varchar(50),
sportCategoryId int NOT NULL,
disclaimer varchar(200),
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Alter table FinalConfigs add foreign key (sportCategoryId) references SportCategories(id);

Create table FinalGames (
id int unsigned primary key auto_increment,
finalConfigId int unsigned NOT NULL,
gameType enum('Final','Semi1','Semi2'),
dateTime datetime,
teamId1 int unsigned NOT NULL,
teamId2 int unsigned NOT NULL,
description varchar(45),
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

ALTER TABLE `kizzang`.`SportSchedule` CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;
Alter table FinalGames add foreign key (finalConfigId) references FinalConfigs(id);
Alter table FinalGames add foreign key (teamId1) references SportSchedule(id);
Alter table FinalGames add foreign key (teamId2) references SportSchedule(id);

Create table FinalAnswers (
id int unsigned primary key auto_increment,
playerId int unsigned NOT NULL,
finalConfigId int unsigned NOT NULL,
answerHash varchar(500),
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Alter table FinalAnswers add foreign key (finalConfigId) references FinalConfigs(id);
Alter table FinalAnswers add foreign key (playerId) references Players(id);
ALTER TABLE `kizzang`.`FinalAnswers` ADD UNIQUE INDEX `playerId_UNIQUE` (`playerId` ASC);

Alter table kizzang.BGQuestionsConfig add disclaimer varchar(500);
Alter table FinalAnswers add losses tinyint unsigned default 0 after answerHash;
Alter table FinalAnswers add wins tinyint unsigned default 0 after answerHash;
Alter table FinalAnswers add delta smallint unsigned default 0 after losses;
Alter table FinalAnswers add is_emailed tinyint unsigned default 0 after delta;