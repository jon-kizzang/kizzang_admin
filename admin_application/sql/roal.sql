Create table ROALConfigs (
id int unsigned primary key auto_increment,
cardDate date NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table ROALQuestions (
id int unsigned primary key auto_increment,
ROALConfigId int unsigned NOT NULL,
SportScheduleId int unsigned NOT NULL,
startTime datetime NOT NULL,
endTime datetime NOT NULL,
answer int unsigned,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table ROALAnswers (
playerId int unsigned NOT NULL,
ROALConfigId int unsigned NOT NULL,
ROALQuestionId int unsigned NOT NULL,
winningTeam int unsigned NOT NULL,
isCorrect tinyint unsigned NOT NULL DEFAULT 0,
currentStreak int unsigned NOT NULL DEFAULT 0,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp,
primary key(playerId, ROALConfigId));