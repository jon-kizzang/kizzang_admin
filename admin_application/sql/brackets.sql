Create table BracketConfigs (
id int unsigned primary key auto_increment,
name varchar(50) NOT NULL,
theme varchar(50) NOT NULL,
numStartingTeams smallint unsigned,
startDate datetime,
endDate datetime,
sportCategoryId int unsigned,
created timestamp default CURRENT_TIMESTAMP,
updated timestamp default current_timestamp on update current_timestamp);

Create table BracketMatchups (
id int unsigned primary key auto_increment,
bracketConfigId int unsigned NOT NULL,
division enum('MidWest','West','East','South'),
teamId1 int unsigned,
teamRank1 tinyint unsigned,
teamId2 int unsigned,
teamRank2 tinyint unsigned,
sequence tinyint unsigned,
created timestamp default CURRENT_TIMESTAMP,
updated timestamp default current_timestamp on update current_timestamp);

Create table BracketPlayerMatchups (
id int unsigned primary key auto_increment,
bracketConfigId int unsigned NOT NULL,
data text,
tieBreakerTeam1 int unsigned,
tieBreakerTeam2 int unsigned,
status enum('Saved','Completed') default 'Saved',
created timestamp default CURRENT_TIMESTAMP,
updated timestamp default current_timestamp on update current_timestamp);

Create table BracketTimes (
id int unsigned primary key auto_increment,
bracketConfigId int unsigned NOT NULL,
round smallint unsigned NOT NULL,
startDate datetime,
endDate datetime);

Alter table BracketPlayerMatchups add wins int unsigned default 0 after status;
Alter table BracketPlayerMatchups add losses int unsigned default 0 after wins;

Alter table SportTeams add wins int unsigned default 0 after abbr;
Alter table SportTeams add losses int unsigned default 0 after wins;

Alter table BracketConfigs add left_answers varchar(2000) after sportCategoryId;
Alter table BracketConfigs add right_answers varchar(2000) after left_answers;
Alter table BracketConfigs add champion_id int unsigned after right_answers;

Alter table kizzang.Players add isCelebrity tinyint unsigned default 0 after isSuspended;

Alter table GameRules change column gameType gameType enum('Slots','Scratchers','Sweepstakes','Parlay','BG','FT','365Vegas','Bracket');
Insert into GameRules (serialNumber, ruleURL, gameType) values ('TEMPLATE', 'https://d23kds0bwk71uo.cloudfront.net/rules/bracket_challenge.txt', 'Bracket');