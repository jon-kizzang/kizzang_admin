Alter table Wheels add wheelType enum('Basic','Sponsored') default 'Basic';
Alter table Wheels add radius smallint unsigned default 200 after wheelType;
Alter table Wheels add created timestamp default current_timestamp;
Alter table Wheels add updated timestamp default current_timestamp on update current_timestamp;

Alter table Wedges add sponsorCampaignId int unsigned default 0;
Alter table Wedges add magnitude int unsigned default 0 after sponsorCampaignId;
Alter table Wedges add height int unsigned default 0 after magnitude;
Alter table Wedges add width int unsigned default 0 after height;
Alter table Wedges add angle_radians double default 0.0 after height;
Alter table Wedges add created timestamp default current_timestamp;
Alter table Wedges add updated timestamp default current_timestamp on update current_timestamp;