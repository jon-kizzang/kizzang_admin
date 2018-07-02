Create table Payouts (
id int unsigned primary key auto_increment,
gameType enum('Daily Slot','Weekly Slot','Monthly Slot','profootball','collegefootball','ptbdailyshowdown','sicollegebasketball','sidailyshowdown','cheddadailyshowdown','profootball2016','Lottery','ROAL'),
payType enum('Money','Chedda') default 'Money',
startRank int unsigned,
endRank int unsigned,
amount decimal(12,2) NOT NULL default 0.00,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);