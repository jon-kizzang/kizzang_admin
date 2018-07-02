<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('GENDER_MALE',		1);
define('GENDER_FEMALE',		2);
define('GENDER_UNKNOWN',	3);

define('ACTION_NONE',		0);
define('ACTION_DONATE',		1);
define('ACTION_CLAIM',		2);
define('ACTION_FORFEIT',		3);

define('ENCRYPTION_FIELD_FIRST_NAME', 0);
define('ENCRYPTION_FIELD_LAST_NAME', 1);
define('ENCRYPTION_FIELD_ADDRESS', 2);
define('ENCRYPTION_FIELD_PREFIX', 3);
define('ENCRYPTION_FIELD_CITY', 4);
define('ENCRYPTION_FIELD_STATE', 5);
define('ENCRYPTION_FIELD_ZIP', 6);
define('ENCRYPTION_FIELD_HOME_PHONE', 7);
define('ENCRYPTION_FIELD_MOBILE_PHONE', 8);
define('ENCRYPTION_FIELD_EMAIL', 9);
define('ENCRYPTION_FIELD_DOB', 10);
define('ENCRYPTION_FIELD_GENDER	', 11);
define('ENCRYPTION_FIELD_ADDRESS2', 12);

define('ENCRYPTION_SEPARATOR', '::');
define('ENCRYPTION_KEY', 'KizfDkj353');

define('IRS_W9_DOLLAR_LIMIT', 600);	// >'= $600.00 in a year requires a W9

// The number of hours the player has to make a selection on what to do with a win
define('PLAYER_CONFIRMATION_TIME_OUT_IN_HOURS',1);

// The nuymber of hours the player has to submit W9 tax documents after a win that requires them
define('PLAYER_W9_DOCUMENT_TIME_OUT_IN_HOURS',96);	// 4 days

define('SESSION_TABLE', "CREATE TABLE IF NOT EXISTS kizzangslot.Session_%d (SessionID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, Token CHAR(40), PlayerID BIGINT UNSIGNED, GameID INT UNSIGNED, StartTime BIGINT NOT NULL, 
                    PRIMARY KEY (SessionID), KEY `Token` (`Token`), KEY `Player` (`PlayerID`)) 
                    /*!50100 PARTITION BY RANGE ( SessionID MOD 24) 
                    (PARTITION p0 VALUES LESS THAN (1) ENGINE = InnoDB,
                    PARTITION p1 VALUES LESS THAN (2) ENGINE = InnoDB,
                    PARTITION p2 VALUES LESS THAN (3) ENGINE = InnoDB,
                    PARTITION p3 VALUES LESS THAN (4) ENGINE = InnoDB,
                    PARTITION p4 VALUES LESS THAN (5) ENGINE = InnoDB,
                    PARTITION p5 VALUES LESS THAN (6) ENGINE = InnoDB,
                    PARTITION p6 VALUES LESS THAN (7) ENGINE = InnoDB,
                    PARTITION p7 VALUES LESS THAN (8) ENGINE = InnoDB,
                    PARTITION p8 VALUES LESS THAN (9) ENGINE = InnoDB,
                    PARTITION p9 VALUES LESS THAN (10) ENGINE = InnoDB,
                    PARTITION p10 VALUES LESS THAN (11) ENGINE = InnoDB,
                    PARTITION p11 VALUES LESS THAN (12) ENGINE = InnoDB,
                    PARTITION p12 VALUES LESS THAN (13) ENGINE = InnoDB,
                    PARTITION p13 VALUES LESS THAN (14) ENGINE = InnoDB,
                    PARTITION p14 VALUES LESS THAN (15) ENGINE = InnoDB,
                    PARTITION p15 VALUES LESS THAN (16) ENGINE = InnoDB,
                    PARTITION p16 VALUES LESS THAN (17) ENGINE = InnoDB,
                    PARTITION p17 VALUES LESS THAN (18) ENGINE = InnoDB,
                    PARTITION p18 VALUES LESS THAN (19) ENGINE = InnoDB,
                    PARTITION p19 VALUES LESS THAN (20) ENGINE = InnoDB,
                    PARTITION p20 VALUES LESS THAN (21) ENGINE = InnoDB,
                    PARTITION p21 VALUES LESS THAN (22) ENGINE = InnoDB,
                    PARTITION p22 VALUES LESS THAN (23) ENGINE = InnoDB,
                    PARTITION p23 VALUES LESS THAN (24) ENGINE = InnoDB) */");

define('LOG_TABLE', "CREATE TABLE IF NOT EXISTS kizzangslot.Log_%d (SessionID BIGINT UNSIGNED NOT NULL, Gen INT UNSIGNED NOT NULL, GameData TEXT, SpinsLeft SMALLINT UNSIGNED NOT NULL, SpinsTotal SMALLINT UNSIGNED NOT NULL, SecsLeft SMALLINT UNSIGNED NOT NULL,
                    SecsTotal SMALLINT UNSIGNED NOT NULL, CurrentNumberOfBonusHits INT UNSIGNED DEFAULT 0, WinCurrent INT UNSIGNED NOT NULL, WinTotal BIGINT UNSIGNED NOT NULL, CreateTime BIGINT UNSIGNED NOT NULL, ReelOffsets BINARY(11) NOT NULL, FSTriggers BINARY (13),
                    PRIMARY KEY (SessionID, Gen, CreateTime), KEY `SessionID` (`SessionID`)) /*!50100 PARTITION BY RANGE (( CreateTime DIV 3600000 ) MOD 24) 
                   (PARTITION p0 VALUES LESS THAN (1) ENGINE = InnoDB,
                   PARTITION p1 VALUES LESS THAN (2) ENGINE = InnoDB,
                   PARTITION p2 VALUES LESS THAN (3) ENGINE = InnoDB,
                   PARTITION p3 VALUES LESS THAN (4) ENGINE = InnoDB,
                   PARTITION p4 VALUES LESS THAN (5) ENGINE = InnoDB,
                   PARTITION p5 VALUES LESS THAN (6) ENGINE = InnoDB,
                   PARTITION p6 VALUES LESS THAN (7) ENGINE = InnoDB,
                   PARTITION p7 VALUES LESS THAN (8) ENGINE = InnoDB,
                   PARTITION p8 VALUES LESS THAN (9) ENGINE = InnoDB,
                   PARTITION p9 VALUES LESS THAN (10) ENGINE = InnoDB,
                   PARTITION p10 VALUES LESS THAN (11) ENGINE = InnoDB,
                   PARTITION p11 VALUES LESS THAN (12) ENGINE = InnoDB,
                   PARTITION p12 VALUES LESS THAN (13) ENGINE = InnoDB,
                   PARTITION p13 VALUES LESS THAN (14) ENGINE = InnoDB,
                   PARTITION p14 VALUES LESS THAN (15) ENGINE = InnoDB,
                   PARTITION p15 VALUES LESS THAN (16) ENGINE = InnoDB,
                   PARTITION p16 VALUES LESS THAN (17) ENGINE = InnoDB,
                   PARTITION p17 VALUES LESS THAN (18) ENGINE = InnoDB,
                   PARTITION p18 VALUES LESS THAN (19) ENGINE = InnoDB,
                   PARTITION p19 VALUES LESS THAN (20) ENGINE = InnoDB,
                   PARTITION p20 VALUES LESS THAN (21) ENGINE = InnoDB,
                   PARTITION p21 VALUES LESS THAN (22) ENGINE = InnoDB,
                   PARTITION p22 VALUES LESS THAN (23) ENGINE = InnoDB,
                   PARTITION p23 VALUES LESS THAN (24) ENGINE = InnoDB) */");


/* End of file constants.php */
/* Location: ./application/config/constants.php */