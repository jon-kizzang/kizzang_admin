Create table LotteryConfigs (
id int unsigned primary key auto_increment,
numTotalBalls int unsigned NOT NULL,
numAnswerBalls int unsigned NOT NULL,
numCards int unsigned NOT NULL,
cardLimit enum('Per Day','Per Game') default 'Per Day',
startDate datetime NOT NULL,
endDate datetime NOT NULL,
answerHash varchar(200),
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table LotteryCards (
id int unsigned primary key auto_increment,
playerId int unsigned NOT NULL,
lotteryConfigId int unsigned NOT NULL,
answerHash varchar(200) NOT NULL,
correctAnswers int unsigned default 0,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Alter table LotteryCards add foreign key (playerId) references Users(id);
Alter table LotteryCards add foreign key (lotteryConfigId) references LotteryConfigs(id);

ALTER TABLE `kizzang`.`GameRules` CHANGE COLUMN `gameType` `gameType` ENUM('Slots','Scratchers','Sweepstakes','Parlay','BG','FT','365Vegas','Bracket','Lottery') NULL DEFAULT NULL;
