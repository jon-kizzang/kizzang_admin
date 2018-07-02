Select loginType, loginSource, mobileType, count(*) as cnt, group_concat(id) from (Select p.id, loginType, loginSource, mobileType from Players p
Inner join PlayerLogins pl on p.id = pl.playerId where date(convert_tz(accountCreated, 'GMT', 'US/Pacific')) = '2016-03-02'
group by p.id) a group by loginType, loginSource, mobileType;

Insert into  BracketConfigs (id,name,theme,numStartingTeams,startDate,endDate,sportCategoryId,left_answers,right_answers,champion_id,created,updated) values 
('1','SI College REAL Challenge','sicollegebasketball','64','2016-03-13 00:00:00','2016-03-17 15:00:00','7','','','','2016-03-13 14:44:26','2016-03-13 16:35:43');

Insert into  BracketMatchups (id,bracketConfigId,division,teamId1,teamRank1,teamId2,teamRank2,sequence,created,updated) values 
('35','1','MidWest','132','1','19','16','1','2016-03-13 14:56:51','2016-03-13 17:11:36'),
('36','1','MidWest','57','8','300','9','2','2016-03-13 14:59:07','2016-03-13 17:11:36'),
('37','1','MidWest','156','5','259','12','3','2016-03-13 15:00:20','2016-03-13 17:11:36'),
('38','1','MidWest','41','4','109','13','4','2016-03-13 15:01:01','2016-03-13 17:11:37'),
('39','1','MidWest','11','6','355','11','5','2016-03-13 15:02:26','2016-03-13 17:11:37'),
('40','1','MidWest','160','3','34','14','6','2016-03-13 15:03:26','2016-03-13 17:11:37'),
('41','1','MidWest','124','7','277','10','7','2016-03-13 15:04:27','2016-03-13 17:11:37'),
('42','1','MidWest','328','2','309','15','8','2016-03-13 15:05:19','2016-03-13 17:11:37'),
('43','1','West','215','1','352','16','1','2016-03-13 15:11:02','2016-03-13 16:25:09'),
('44','1','West','268','8','51','9','2','2016-03-13 15:16:13','2016-03-13 16:25:09'),
('45','1','West','21','5','350','12','3','2016-03-13 15:16:49','2016-03-13 16:25:09'),
('46','1','West','73','4','311','13','4','2016-03-13 15:17:39','2016-03-13 16:25:09'),
('47','1','West','281','6','201','11','5','2016-03-13 15:18:22','2016-03-13 16:25:09'),
('48','1','West','282','3','105','14','6','2016-03-13 15:19:00','2016-03-13 16:25:09'),
('49','1','West','216','7','326','10','7','2016-03-13 15:20:25','2016-03-13 16:25:09'),
('50','1','West','210','2','38','15','8','2016-03-13 15:21:25','2016-03-13 16:25:09'),
('51','1','South','191','1','353','16','1','2016-03-13 15:33:15','2016-03-13 17:11:37'),
('52','1','South','313','8','227','9','2','2016-03-13 15:34:39','2016-03-13 17:11:38'),
('53','1','South','121','5','49','12','3','2016-03-13 15:35:11','2016-03-13 17:11:38'),
('54','1','South','136','4','274','13','4','2016-03-13 15:43:03','2016-03-13 17:11:38'),
('55','1','South','205','6','354','11','5','2016-03-13 15:44:40','2016-03-13 17:11:38'),
('56','1','South','337','3','272','15','6','2016-03-13 15:45:29','2016-03-13 17:11:38'),
('57','1','South','345','7','221','10','7','2016-03-13 15:46:05','2016-03-13 17:11:38'),
('58','1','South','349','2','336','15','8','2016-03-13 15:47:32','2016-03-13 17:11:38'),
('59','1','East','329','1','106','16','1','2016-03-13 15:56:56','2016-03-13 17:11:39'),
('60','1','East','286','8','35','9','2','2016-03-13 15:57:55','2016-03-13 17:11:39'),
('61','1','East','228','5','15','12','3','2016-03-13 15:59:43','2016-03-13 17:11:39'),
('62','1','East','125','4','123','13','4','2016-03-13 16:01:02','2016-03-13 17:11:39'),
('63','1','East','252','6','102','11','5','2016-03-13 16:02:23','2016-03-13 17:11:39'),
('64','1','East','319','3','92','14','6','2016-03-13 16:03:14','2016-03-13 17:11:39'),
('65','1','East','65','7','275','10','7','2016-03-13 16:04:04','2016-03-13 17:11:40'),
('66','1','East','162','2','163','15','8','2016-03-13 16:04:39','2016-03-13 17:14:47');

Insert into  BracketTimes (id,bracketConfigId,round,startDate,endDate) values 
('8','1','64','2016-03-17 00:00:00','2016-03-18 00:00:00'),
('9','1','32','2016-03-19 00:00:00','2016-03-20 00:00:00'),
('10','1','16','2016-03-24 00:00:00','2016-03-25 00:00:00'),
('11','1','8','2016-03-26 00:00:00','2016-03-27 00:00:00'),
('12','1','4','2016-04-02 00:00:00','2016-04-02 23:00:00'),
('13','3','2','2016-04-04 00:00:00','2016-04-04 23:00:00');