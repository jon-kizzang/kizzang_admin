Create database rightSignature;
Use rightSignature;

Create table documents (
id char(36) primary key,
playerId int unsigned,
status varchar(50) NOT NULL,
processingState varchar(50),
tags varchar(200),
thumbUrl varchar(500),
signedUrl varchar(500),
createdDate datetime,
completedDate datetime,
expirationDate datetime,
updated timestamp default current_timestamp on update current_timestamp);

Create table audits (
documentId char(36) NOT NULL,
sequence int unsigned NOT NULL,
message varchar(500) NOT NULL,
created datetime NOT NULL,
primary key (documentId, sequence));

Create table attachments (
id char(36) primary key,
documentId char(36) NOT NULL,
action varchar(200),
downloadUrl varchar(500),
updated timestamp default current_timestamp on update current_timestamp);

Create table signins (
id char(36) primary key,
playerId int unsigned NOT NULL,
templateId int unsigned NOT NULL,
status enum('Pending','Complete','Expired') default 'Pending',
expirationDate datetime,
created timestamp default current_timestamp);

Create table templates (
id int unsigned primary key auto_increment,
type varchar(30) NOT NULL,
guid varchar(50) NOT NULL,
updated timestamp default current_timestamp on update current_timestamp);

INSERT INTO templates (id,type,guid) VALUES (1,'W9','a_8116970_3412ed545f794cc1b839e6ea6b6cdff5');
INSERT INTO templates (id,type,guid) VALUES (2,'DL','a_16925660_6020442fbc704858889d070f547ca32c');
INSERT INTO templates (id, type, guid) VALUES (3, 'Notarize', 'a_17323739_1c157f6c4dec459d8ab46b79b0303626');
