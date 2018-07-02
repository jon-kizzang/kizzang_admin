Alter table BGPlayerCards change column picksHash picksHash varchar(1000) NOT NULL;
Alter table BGPlayerCards add isEmailed tinyint unsigned default 0 after losses;