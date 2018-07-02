Alter table kizzang.Players add ipAddress varchar(45);
Alter table kizzang.PlayerLogins add ipAddress varchar(45) after loginSource;

Drop table marketing.impressions;
Create table marketing.impressions (
id int unsigned primary key auto_increment,
email_campaign_id int unsigned NOT NULL,
destination enum('iOS', 'Droid', 'Web', 'Facebook') default 'Web',
user_agent varchar(200),
ip_address varchar(45),
fingerprint varchar(200),
created timestamp default current_timestamp);

Create table Sponsor_Advertising_Campaigns (
id int unsigned primary key,
utm_source int unsigned NOT NULL,
utm_medium enum('email', 'ad', 'weblink') default 'email',
utm_content varchar(50),
utm_campaign int unsigned,
created timestamp default current_timestamp);