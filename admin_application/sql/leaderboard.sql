Create table GameLeaderBoards (
game_type enum('Slot', 'Parlay') default 'Slot',
game_sub_type varchar(50) NOT NULL,
place int unsigned NOT NULL,
player_name varchar(50) NOT NULL,
score varchar(50) NOT NULL,
endDate date,
prize varchar(50),
game varchar(30),
created timestamp default current_timestamp);