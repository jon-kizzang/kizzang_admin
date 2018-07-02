// ----------------- MULTIPLIER STUFF ------------------ //
Alter table Positions add multiplier int unsigned NOT NULL DEFAULT 1 after endPosition;

Select concat('Update Positions set endPosition = endPosition + ', a.diff, ' where id = ', max(id), ' and playerId = ', d.playerId, '||') as query, d.playerId from Positions d
Inner join (Select p.playerId, sum(fromPosition - startPosition) as diff from Positions p
Inner join PlayPeriod pp on p.calendarDate = date(pp.endDate) and p.playerId = pp.playerId and pp.gamesPlayed > 14
where concat(p.playerId, '-', calendarDate) in 
(Select iddate from (Select concat(playerId, '-', calendarDate) as iddate, count(*)  as cnt from Positions group by concat(playerId, '-', calendarDate) having cnt > 1) a )
and p.ruleCode > 2 and p.ack = 1
group by p.playerId) a on a.playerId = d.playerId
group by d.playerId;

//------------------  FIXES GO BACK ISSUE --------------------- //
Select concat('Update Positions set startPosition = ', e.endPosition, ', endPosition = ', e.endPosition + 1, ', ruleApplied = ''Manual Fix'', ruleCode = 1 where id = ', p.id, ' and playerId = ', p.playerId) as query 
from Positions p
Left Join (Select * from Positions where calendarDate = '2015-09-20') e on e.playerId = p.playerId 
where p.calendarDate = '2015-09-21' and p.playerId in (7753,806,6997,8971,6657,3853,385,5261,6112,4436,4500,648,8650,3083,462,1760,5957,1782,4720,1426,388,7748,4640,9001,7874,9177,6953,4716,4439,433,4747,511,6116,7033,7444,6796,1787,541,7956,628,7548,9845);


Select concat('Update Positions set startPosition = ', e.endPosition, ', endPosition = ', e.endPosition + 1, ', ruleApplied = ''Manual Fix'', ruleCode = 1 where id = ', p.id, ' and playerId = ', p.playerId) as query 
from Positions p
Left Join (Select * from Positions where calendarDate = '2015-09-21') e on e.playerId = p.playerId 
where p.calendarDate = '2015-09-22' and p.ruleCode > 2



Select * from Positions where concat(playerId, '-', calendarDate) in 
(Select iddate from (Select concat(playerId, '-', calendarDate) as iddate, count(*)  as cnt from Positions group by concat(playerId, '-', calendarDate) having cnt > 1)
and ack = 0;

Select concat('Delete from Positions where id = ', p.playerId, ' and calendarDate = ''', p.calendarDate, ''' order by ack ASC limit ', (cnt - 1), '||') as query from Positions p
Inner join (Select concat(playerId, '-', calendarDate) as iddate, count(*)  as cnt from Positions group by concat(playerId, '-', calendarDate) having cnt > 1) a on iddate = concat(playerId, '-', calendarDate)
Group by playerId, calendarDate

Select concat('Delete from PlayPeriod where id in (', ids, ') where gamesPlayed = 0') as query 
from (Select playerId, date(endDate) as date, group_concat(id) as ids, count(*) as cnt from PlayPeriod group by playerId, date(endDate) having cnt > 1) a;

ALTER TABLE `kizzang`.`Positions` 
ADD UNIQUE INDEX `playerid_calendarDate_idx` (`playerId` ASC, `calendarDate` ASC);

ALTER TABLE `kizzang`.`PlayPeriod` 
ADD UNIQUE INDEX `playerId_endDate_idx` (`playerId` ASC, `endDate` ASC);