Create database marketing;
Use marketing;

Create table marketing.impression_aggregate (
agg_date date,
campaign varchar(20) NOT NULL,
device_type varchar(20) NOT NULL,
sponsor varchar(50) NOT NULL,
impression_count int unsigned,
conversion_count int unsigned,
primary key (agg_date, campaign, device_type));

Create table campaigns (
id int unsigned primary key,
campaign_name varchar(100),
from_name varchar(100),
from_email varchar(100),
reply_to varchar(100),
subject varchar(100),
html_body text,
message_type enum ('text','html') default 'html',
cat_id int unsigned NOT NULL,
is_wz tinyint unsigned NOT NULL DEFAULT 1,
delivery_reminder tinyint unsigned default 0,
permission_reminder tinyint unsigned default 0,
track_customer_activity tinyint unsigned default 0,
ff_link tinyint unsigned default 0,
ff_link_text varchar(100),
active_campaign tinyint unsigned default 0,
deleted tinyint unsigned default 0,
send_now tinyint unsigned default 0,
schedule_date date,
schedule_time time,
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP );

Create table audiences (
id int unsigned NOT NULL primary key,
name varchar(200),
size int unsigned NOT NULL,
subscribers_count int unsigned NOT NULL DEFAULT 0,
status varchar(30) NOT NULL default 'Unknown',
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP );

Create table campaign_audiences (
campaign_id INT UNSIGNED NOT NULL,
audience_id INT UNSIGNED NOT NULL,
primary key(campaign_id, audience_id));

Alter table campaign_audiences add foreign key (campaign_id) references campaigns(id) ON DELETE CASCADE ON UPDATE CASCADE; 
Alter table campaign_audiences add foreign key (audience_id) references audiences(id) ON DELETE CASCADE ON UPDATE CASCADE;

Create table categories (
id int unsigned NOT NULL primary key,
name varchar(200),
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

Create table impressions (
id int unsigned primary key auto_increment,
xml varchar(5000),
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

Alter table campaigns add foreign key (cat_id) references categories(id) ON UPDATE CASCADE;

Create table custom_fields (
id int unsigned NOT NULL primary key,
name varchar(200),
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

Create table subscribers (
audience_id int unsigned NOT NULL,
email varchar(200) NOT NULL,
first_name varchar(50),
last_name varchar(50),
city varchar(100),
state char(2),
email_hash varchar(200) NOT NULL,
optin_date date,
optin_ip varchar(20),
optin_website varchar(50),
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
primary key (audience_id, email));

Create table test_subscribers (
id int unsigned NOT NULL primary key,
email varchar(200),
created timestamp DEFAULT CURRENT_TIMESTAMP,
updated timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

Alter table subscribers add foreign key (audience_id) references audiences(id) ON UPDATE CASCADE;

Select loginType, loginSource, count(distinct playerId) from Players p
Left join PlayerLogins l on p.id = l.playerId 
where date(created) = '2015-06-15' and date(accountCreated) = '2015-06-15' group by loginType, loginSource;

Select p.* from Positions p
inner join (Select playerId, endDate, count(*) as cnt , max(gamesPlayed) as max_games, min(gamesPlayed) from PlayPeriod group by playerId, endDate having cnt > 1 and max_games >14) a on date(a.endDate) = p.calendarDate and p.playerId = a.playerId and ruleCode > 2;

Select playerId, sum(fromPosition - startPosition) as diff 
From Positions p
inner join (Select playerId, endDate, count(*) as cnt , max(gamesPlayed) as max_games, min(gamesPlayed) from PlayPeriod group by playerId, endDate having cnt > 1 and max_games >14) a on date(a.endDate) = p.calendarDate and p.playerId = a.playerId and ruleCode > 2;
where p.ack = 1
group by playerId

Alter table marketing.impressions add url varchar(300) after adwords;
Alter table kizzang.Sponsor_Advertising_Campaigns add cdn varchar(20) after d;
Alter table kizzang.Sponsor_Advertising_Campaigns add image varchar(100) after cdn;