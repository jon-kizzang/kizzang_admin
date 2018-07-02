Create table CronJobs (
id int unsigned not null auto_increment primary key,
name varchar(200) NOT NULL,
routine_id int unsigned,
minutes char(2) DEFAULT '*',
hours char(2) Default '*',
day_of_month char(2) Default '*',
months char(2) Default '*',
day_of_week char(2) Default '*',
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table CronSchedule (
id int unsigned primary key auto_increment,
cron_id int unsigned NOT NULL,
schedule_date datetime NOT NULL,
status enum('Pending', 'Running', 'Complete') default 'Pending',
is_active tinyint default 1,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

ALTER TABLE kizzang.CronSchedule 
ADD UNIQUE INDEX idx_cron_id_schedul_time USING BTREE (cron_id ASC, schedule_date ASC);

Alter table CronSchedule add constraint foreign key (cron_id) references CronJobs(id) ON DELETE CASCADE ON UPDATE CASCADE;

Create table CronLog (
id int unsigned NOT NULL auto_increment primary key,
cron_schedule_id int unsigned NOT NULL,
cron_id int unsigned NOT NULL,
status enum('Success', 'Fail') default 'Fail',
return_value text,
created timestamp default current_timestamp);

Create database notifications;

Create table notifications.history (
id char(36) NOT NULL PRIMARY KEY,
successful int unsigned,
failed int unsigned,
converted int unsigned,
remaining int unsigned,
queued_at timestamp,
contents varchar(1000),
headings varchar(1000),
segments varchar(2000),
player_ids varchar(2000),
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table notifications.players (
id char(36) NOT NULL PRIMARY KEY,
identifier varchar(1000),
session_count int unsigned,
language char(2),
timezone int,
game_version varchar(50),
device_os varchar(50),
device_type enum('iOS', 'Android', 'Amazon', 'Windows Phone'),
device_model varchar(50),
facebook_id int unsigned,
tags text,
last_active timestamp,
created timestamp,
badge_count int unsigned,
player_id int unsigned);

Create table notifications.queue (
id int unsigned primary key auto_increment,
info text NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table notifications.templates (
id char(36) NOT NULL primary key,
name varchar(200) NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table notifications.segments (
id int unsigned primary key auto_increment,
name varchar(200) NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Insert into notifications.templates (id, name) values
('97e0b454-094d-11e5-a47d-27500a5cf927', '1 more game to advance'),
('a9bf9906-094d-11e5-9754-c383a8195049', '2 more game to advance'),
('b792ba68-094d-11e5-b7f5-efd77af76761', '3 more game to advance'),
('c908cd50-094d-11e5-a55d-b7c22533006b', '4 more game to advance'),
('d22fc5c8-094d-11e5-8ec2-fbf4e1746873', '5 more game to advance'),
('dab78a3c-094d-11e5-97b9-f335dd1a511f', '6 more game to advance'),
('e57296f6-094d-11e5-b198-0fca3bcffb19', '7 more game to advance'),
('eed01af2-094d-11e5-a5fc-932f35a6a7ce', '8 more game to advance'),
('f76421fe-094d-11e5-96f0-c38872669619', '9 more game to advance'),
('ff2d6382-094d-11e5-a63a-e38e2953011f', '10 more game to advance'),
('0aec36bc-094e-11e5-af1c-e3f8c13c0290', '11 more game to advance'),
('16678212-094e-11e5-983f-6f0bda8b6f3e', '12 more game to advance'),
('2286cecc-094e-11e5-a6de-5ba14c7f8996', '13 more game to advance'),
('3b033a76-094e-11e5-b363-0b48c7af29f0', '14 more game to advance'),
('43b16cd8-094e-11e5-91f8-aff0ae8944f8', '15 more game to advance');


Insert into notifications.segments (name) values
('All'),
('Free User'),
('Paid User'),
('Games Remaining == 1'),
('Games Remaining == 2'),
('Games Remaining == 3'),
('Games Remaining == 4'),
('Games Remaining == 5'),
('Games Remaining == 6'),
('Games Remaining == 7'),
('Games Remaining == 8'),
('Games Remaining == 9'),
('Games Remaining == 10'),
('Games Remaining == 11'),
('Games Remaining == 12'),
('Games Remaining == 13'),
('Games Remaining == 14');

SELECT table_schema "Data Base Name", table_name,
(data_length + index_length ) / 1024 / 
1024 "Data Base Size in MB", 
( data_free )/ 1024 / 1024 "Free Space in MB" 
FROM information_schema.TABLES

Create table notifications.crons (
id int unsigned primary key auto_increment,
queue_id int unsigned NOT NULL,
schedule_date datetime NOT NULL,
status enum('Pending', 'Running', 'Complete') default 'Pending',
is_active tinyint default 1,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Alter table notifications.crons add foreign key (queue_id) references notifications.queue(id) on delete cascade on update cascade;

ALTER TABLE notifications.queue DROP COLUMN notification_type, DROP COLUMN type;

Update WinConfirmations set status = 'N' where prizeAmount < 5.0 and status = 'U' and playerActionChoice = 2 order by winConfirmed DESC limit 50;