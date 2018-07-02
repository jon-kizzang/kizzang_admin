Create table Users (
id int unsigned primary key auto_increment,
firstName varchar(50) NOT NULL,
lastName varchar(50) NOT NULL,
dob date NOT NULL,
userType enum('User','Administrator','Guest') default 'User',
screenName varchar(50) NOT NULL,
accountName varchar(300) NOT NULL,
payPalEmail varchar(200) NOT NULL,
accountCode varchar(300) NOT NULL,
passwordHash varchar(300),
fbId varchar(100) default NULL,
email varchar(100) default NULL,
phone varchar(20) default NULL,
address varchar(100),
city varchar(100),
state char(2),
zip int unsigned,
lastApprovedTOS datetime,
lastApprovedPrivacyPolicy datetime,
referralCode varchar(20) DEFAULT NULL,
gender enum('Male','Female','None','Other') default 'None',
accountStatus enum('Suspended','Deleted','Active', 'Email Suspended','W2 Blocked') default 'Active',
newUserFlow tinyint unsigned default 1,
profileComplete tinyint unsigned default 0,
status tinyint unsigned default 3,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

ALTER TABLE `kizzang`.`Users` ADD UNIQUE INDEX `idx_accountName_unq` (`accountName` ASC);
Update Users set fbId = NULL where fbId = '';
Update Users set email = NULL where email = '';
Update Users set phone = NULL where phone = '';
ALTER TABLE `kizzang`.`Users` ADD UNIQUE INDEX `idx_fbId_unq` (`fbId` ASC);
Alter table Users add unique index idx_email_ung (email asc);
Alter table Users add unique index idx_phone_ung (phone asc);
ALTER TABLE `kizzang`.`PlayPeriod` DROP FOREIGN KEY `playPeriodId_fk`;
ALTER TABLE `kizzang`.`Positions` DROP FOREIGN KEY `playerId_fk`;
ALTER TABLE `kizzang`.`GameCount` DROP FOREIGN KEY `gameCount_playerId_fk`;
ALTER TABLE `kizzang`.`Player_Groups` DROP FOREIGN KEY `Player_Groups_ibfk_1`;
ALTER TABLE `reports`.`PlayerEvents` CHANGE COLUMN `game_type` `game_type` ENUM('Slots','Parlay','Scratchers','Tickets','Login','Sweepstakes','Bracket','Lottery','ROAL') NOT NULL DEFAULT 'Slots' ;

ALTER TABLE `kizzang`.`Wheels` CHANGE COLUMN `wheelType` `wheelType` ENUM('Basic','Sponsored','Multiplier') NULL DEFAULT 'Basic' ;

Create table GuestConversions (
conversionTime datetime NOT NULL,
playerId int unsigned NOT NULL,
accountCreated datetime NOT NULL,
secDiff bigint unsigned NOT NULL);

Alter table kizzang.Winners add status enum('New','Document','Claimed','Approved','Denied','Expired') default 'New' after processed;
ALTER TABLE `kizzang`.`GameExpireTimes` CHANGE COLUMN `game` `game` ENUM('slotTournament','dailyShowdown','finalThree','bigGame','sweepstakes','scratchCard','store','lottery') NOT NULL ;
Alter table kizzang.Winners add expirationDate datetime after order_num;
Alter table Winners add comments varchar(3000) after expirationDate;
ALTER TABLE `kizzang`.`GameLeaderBoards` CHANGE COLUMN `game_type` `game_type` ENUM('Slot','Parlay','ROAL') NULL DEFAULT 'Slot' ;
Alter table PlayPeriod add playDate date after playerId;
Alter table PlayPeriod add unique index (playerId, playDate);

Create table Payments (
id int unsigned auto_increment primary key,
winnerId int unsigned NOT NULL,
playerId int unsigned NOT NULL,
amount decimal(10,2) NOT NULL,
prizeName varchar(100) NOT NULL,
serialNumber char(7) NOT NULL,
status ENUM('Unpaid','Pending','Paid','Forfeited') NULL DEFAULT 'Unpaid',
firstName varchar(50),
lastName varchar(50),
email varchar(100) default NULL,
phone varchar(20) default NULL,
address varchar(100),
city varchar(100),
state char(2),
zip int unsigned,
payPalEmail varchar(200) NOT NULL,
payPalItemId varchar(50),
payPalBatchId varchar(50),
payPalTransactionId varchar(50),
payPalStatus varchar(50),
payPalError varchar(200),
qb tinyint unsigned default 0,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table UserQB (
playerId int unsigned NOT NULL,
qbVendorId int unsigned NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp,
primary key (playerId, qbVendorId));

Create table WinnerQuestions (
id int unsigned primary key auto_increment,
question varchar(200) NOT NULL,
required tinyint unsigned NOT NULL default 0,
updated timestamp default current_timestamp on update current_timestamp);

Create table WinnerQuestionAnswers (
winnerId int unsigned NOT NULL,
questionId int unsigned NOT NULL,
passed tinyint unsigned NOT NULL default 0,
updated timestamp default current_timestamp on update current_timestamp,
primary key (winnerId, questionId));

Create table WinnerCalls (
winnerId int unsigned NOT NULL,
sequence tinyint unsigned NOT NULL,
callDate datetime NOT NULL,
result enum('Answered','Voicemail'),
updated timestamp default current_timestamp on update current_timestamp,
primary key (winnerId, sequence));

Insert into kizzang.WinnerQuestions values 
('In which year were you born', 1),
('Please spell your first name and last name', 0),
('What is your home address, including city, state, and zip code?', 0),
('What is your email address?', 0),
('What is your cell phone number?', 0),
('What is your home phone number?', 0),
('How much did you win at Kizzang?', 0),
('Where did you hear about Kizzang?', 0),
('What is your favorite game on Kizzang?', 0),
('Why do you have more than one account on Kizzang?',0);

Alter table GameLeaderBoards add player_id int unsigned NOT NULL after place;

Alter table Users add emailStatus enum('Normal','Commercial Opt Out','Transaction Opt Out','Both Opt Out') default 'Normal' after accountStatus;