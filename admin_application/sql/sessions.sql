Create table Sessions (
id varchar(500) NOT NULL primary key,
device_id int unsigned NOT NULL,
player_data varchar(2000) NOT NULL,
session_data varchar(2000) NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);