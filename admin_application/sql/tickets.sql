Create table Tickets (
playerId int unsigned NOT NULL,
sweepstakesId int unsigned NOT NULL,
ticketDate date NOT NULL,
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
primary key(playerId, sweepstakesId, ticketDate));

Alter table Tickets add foreign key (playerId) references Users(id) on delete cascade on update cascade;
Alter table Tickets add foreign key (sweepstakesId) references Sweepstakes(id) on delete cascade on update cascade;

/------------------------------------------------------/

Create table TicketAggregate (
playerId int unsigned NOT NULL,
sweepstakesId int unsigned NOT NULL,
ticketCount int unsigned NOT NULL,
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
primary key(playerId, sweepstakesId));

Insert into kizzang.TicketAggregate (playerId, sweepstakesId, ticketCount)
(SELECT t.playerId, sweepstakeId, count(s.id) as ticketCount
From SweepstakeTickets s 
Inner join Tickets t on s.ticketId = t.id
Group by playerId, sweepstakeID)
On DUPLICATE KEY UPDATE set ticketCount = values(ticketCount);

Insert into kizzang.TicketAggregate (playerId, sweepstakesId, ticketCount)
Select playerId, 0, count(t.id) as ticketCount from Tickets t where isIssued = 0 group by playerId
On DUPLICATE KEY UPDATE set ticketCount = values(ticketCount);

Insert into kizzang.TicketAggregate (playerId, sweepstakesId, ticketCount)
Select playerId, sweepstakesId, count(t.id) as ticketCount from Tickets t where isIssued = 1 and sweepstakesId <> 0 group by playerId
On DUPLICATE KEY UPDATE set ticketCount = values(ticketCount);

Alter table Tickets drop column isDeleted;
Alter table Tickets add sweepstakesId int unsigned DEFAULT 0;
Update Tickets, SweepstakeTickets set Tickets.sweepstakesId = SweepstakeTickets.sweepstakeId where Tickets.id = SweepstakeTickets.ticketId;

ALTER TABLE `Scratch_GPGames` CHANGE COLUMN `WinAmount` `WinAmount` VARCHAR(30) NOT NULL ;

Alter table SportPlayerCards drop column winner;
Alter table SportPlayerCards add isQuickpick tinyint DEFAULT NULL;

Create table TicketArchive (
id int unsigned,
playerId int unsigned,
gameToken varchar(200),
dateCreated datetime,
isIssued tinyint unsigned,
sweepstakesId int unsigned,
updated timestamp,
created timestamp DEFAULT CURRENT_TIMESTAMP);

Insert into TicketArchive (id, playerId, gameToken, dateCreated, isIssued, sweepstakesId, updated)
Select id, playerId, gameToken, dateCreated, isIssued, sweepstakesId, updated
From Tickets
where sweepstakesId in (Select distinct id from Sweepstakes where endDate < now());

Delete From Tickets where sweepstakesId in (Select distinct id from Sweepstakes where endDate < now());

/*--------------------------------------------------------------------------*/

Create table TicketCompressed (
playerId int unsigned NOT NULL,
gameToken varchar(255) NOT NULL,
sweepstakesId int unsigned NOT NULL,
count mediumint unsigned NOT NULL,
dateCreated timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp,
primary key (playerId, sweepstakesId, gameToken));

Insert into TicketCompressed (playerId, gameToken, sweepstakesId, count, dateCreated, updated) 
SELECT playerId, gameToken, sweepstakesId, count(*) as cnt, max(dateCreated) as dateCreated, max(updated) as updated FROM kizzang.Tickets group by playerId, gameToken, sweepstakesId order by count(*) DESC;