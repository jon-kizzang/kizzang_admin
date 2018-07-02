Create database analytics;
use analytics;
Create table rawAnalytics (
playerId int unsigned NOT NULL,
type enum('Login','Logout','Lobby','RegistrationPrompt','UIButtonClick','Action','PlayAgain','LeaveGame','RegistrationPage') NOT NULL,
subType varchar(50) NOT NULL,
name varchar(50),
foreignId int unsigned default 0,
created timestamp default CURRENT_TIMESTAMP,
primary key (created, type, subType, playerId));