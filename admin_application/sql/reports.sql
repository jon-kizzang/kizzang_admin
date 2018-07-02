Create database reports;

Create table reports.PlayerEvents (
player_id int unsigned NOT NULL,
started timestamp,
game_type enum('Slots','Parlay','Scratchers','Tickets','Login', 'Sweepstakes') NOT NULL,
foreign_id int unsigned,
extra varchar(50),
primary key (player_id, started, game_type)
);

Create table reports.PlayerDay (
player_id int unsigned NOT NULL,
event_date date NOT NULL,
login_ts timestamp,
logout_ts timestamp,
day_data text,
created timestamp default current_timestamp,
primary key (player_id, event_date, login_ts));
