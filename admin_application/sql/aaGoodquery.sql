Select concat('Insert into GameCount (playerId, playPeriodId, foreignId, gameType, theme, count, maxGames, expirationDate) values (', playerId, ',', playPeriodId, ', 626, ''SportsEvent'', ''sidailyshowdown'', 0, 20, ''2016-09-28 23:59:59'')') as query from (Select distinct playerId, playPeriodId from GameCount where date(expirationDate) = '2016-09-28') a;
Update GameCount set gameType = 'sidailyshowdown' where gameType = 'ptbdailyshowdown';

Select concat('Insert into GameCount (playerId, playPeriodId, foreignId, gameType, theme, count, maxGames, expirationDate) values (', 
    a.playerId, ',', playPeriodId, ',656,''SportsEvent'', ''sidailyshowdown'', 0, 20, ''2016-10-13 16:00:00'')') as query 
from (Select distinct playerId from GameCount where playerId not in (Select distinct playerId from GameCount where theme = 'sidailyshowdown' and foreignId = 656)) a 
inner join (Select playerId, max(playPeriodId) as playPeriodId from GameCount group by playerId) b on a.playerId = b.playerId

Insert into EventNotifications (playerId, type, data, expireDate) values
(858, 'dailyShowdown', '{"serialNumber":"KP00645","entry":3361,"gameName":"Pick the Board® - Pro Football","prizeName":"2nd place - $500 prize","prizeAmount":"500.00"}', '2016-10-14 12:00:00'),
(5, 'dailyShowdown', '{"serialNumber":"KP00645","entry":11,"gameName":"Pick the Board® - Pro Football","prizeName":"$1.00","prizeAmount":"1.00"}', '2016-10-14 12:00:00'),
(219, 'dailyShowdown', '{"serialNumber":"KP00645","entry":3364,"gameName":"Pick the Board® - Pro Football","prizeName":"Tied for 3rd place - $166.67 prize","prizeAmount":"166.670"}', '2016-10-14 12:00:00');

Insert into EventNotifications (playerId, type, data, expireDate) values
(858, 'dailyShowdown', '{"serialNumber":"KS00607","entry":3617,"gameName":"Gummibar","tournamentType":"Daily","prizeName":"$100.00","prizeAmount":"100.00"}', '2016-10-14 12:00:00'),
