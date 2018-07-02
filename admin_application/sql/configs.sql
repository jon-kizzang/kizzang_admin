Create table Configs (
id int unsigned primary key auto_increment,
main_type varchar(20) NOT NULL,
sub_type varchar(20) NOT NULL,
data_type enum('Numeric','Text','JSON','XML','Serialized') default 'Text',
info text,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Alter table Configs add unique index (main_type, sub_type);
Insert into kizzang.Configs (main_type, sub_type, data_type, info) values ('Map', 'Popup', 'JSON', '{"theme":"sibiggame"}');