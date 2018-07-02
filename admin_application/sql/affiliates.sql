--Main DB
Insert into Advertising_Mediums (id, description) values ('affiliate', 'Affiliate');
Alter table Sponsor_Advertising_Campaigns add message varchar(200) after image;
Alter table Users add referralUserId varchar(20) after referralCode;
Alter table kizzang.Sponsors change artRepo artRepo varchar(300);
Alter table Sponsor_Advertising_Campaigns add code varchar(50) after message;

Create table AffiliateGames (
Sponsor_Advertising_Campaign_Id char(20) NOT NULL,
GameType enum('Slot','Scratcher') default 'Slot',
Theme varchar(20) NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp,
primary key (Sponsor_Advertising_Campaign_Id, GameType, Theme));

Alter table AffiliateGames add foreign key (Sponsor_Advertising_Campaign_Id) references Sponsor_Advertising_Campaigns(id) on update cascade on delete cascade;

--Slot DB
Alter table SlotGame add SlotType enum('Normal','Affiliate') default 'Normal' after Disclaimer;

--Scratcher DB
Alter table Scratch_GPGames add CardType enum('Normal','Affiliate') default 'Normal' after Theme;