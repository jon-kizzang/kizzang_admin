ALTER TABLE `kizzang`.`Payouts` 
CHANGE COLUMN `gameType` `gameType` ENUM('Daily Slot','Weekly Slot','Monthly Slot','profootball','bingo','ptbdailyshowdown','sicollegebasketball','sidailyshowdown','cheddadailyshowdown','profootball2016','Lottery','ROAL','collegefootball2016') NULL DEFAULT NULL ;

Create table BingoGames (
id int unsigned primary key auto_increment,
startTime datetime NOT NULL,
endTime datetime NOT NULL,
cardNumbersPicked varchar(2000) NOT NULL,
maxNumber tinyint unsigned default 45,
callTime mediumint unsigned default 15,
currentNum mediumint usnigned default 0,
status enum('Pending','Active','Paused','Complete') default 'Pending',
created timestamp default current_timestamp
);

Create table BingoCards (
bingoGameId int unsigned NOT NULL,
playerId int unsigned NOT NULL,
cardNumbers varchar(2000) NOT NULL,
chedda varchar(200) NOT NULL,
numberHits tinyint unsigned,
created timestamp default current_timestamp);

Insert into Payouts (gameType, payType, startRank, endRank, amount) values
('bingo','Money',30,30,10000.00),
('bingo','Money',31,31,5000.00),
('bingo','Money',32,32,1000.00),
('bingo','Money',33,33,100.00),
('bingo','Money',34,35,50.00),
('bingo','Money',35,45,25.00);