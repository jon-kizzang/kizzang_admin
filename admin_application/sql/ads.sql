Create table Ads (
id int unsigned primary key auto_increment,
playerId int unsigned,
gameType enum('SlotTournament','SportsEvent','ScratchCard','Lottery','ROAL'),
theme varchar(20),
type varchar(20) NOT NULL,
status enum('Clicked','Viewed','Closed','Empty','Error') default 'Error',
created timestamp default current_timestamp);

Alter table kizzang.SportParlayConfig add adPlacement enum('Before','After') default 'After' after isActive;
Alter table SlotGame add adPlacement enum('Before','After') default 'After' after Math;
Alter table Scratch_GPGames add adPlacement enum('Before','After') default 'After' after Theme;