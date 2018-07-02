Alter table SportParlayConfig add disclaimer varchar(1000);
Alter table SportPlayerCards change column picksHash picksHash varchar(1000);

Update Configs set info = '{"theme":"sidailyshowdown"}' where id = 3;

Alter table SportParlayConfig add week tinyint unsigned after maxCardCount;

Alter table kizzang.SportParlayCards add spread decimal(3,1) after overUnderScore;
Alter table kizzang.SportParlayCards add question varchar(200);
INSERT INTO kizzang.SportCategories (id, name, rank, sort) VALUES ('9', 'Questions', '8', '8');