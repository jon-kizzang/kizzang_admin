Create table Board (
id int unsigned primary key auto_increment,
x int unsigned,
y int unsigned,
config_data text,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);

Create table BoardTiles (
id int unsigned primary key auto_increment,
name varchar(50) NOT NULL,
type enum('Events','Tiles') default 'Tiles',
sub_type enum('Multiplier','Spins','Tickets','Letter','City','Sponsor','Unlock','Detour','Anchor','Blank','Warp'),
x int unsigned default 0,
y int unsigned default 0,
width int unsigned default 0,
height int unsigned default 0,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);