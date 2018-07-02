Create table Store (
id int unsigned primary key auto_increment,
shortTitle varchar(50) NOT NULL,
longTitle varchar(200) NOT NULL,
summary varchar(500) NOT NULL,
imageUrl varchar(200) NOT NULL,
chedda int unsigned NOT NULL,
amount decimal(8,2) NOT NULL,
startDate date NOT NULL,
endDate date NOT NULL,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);