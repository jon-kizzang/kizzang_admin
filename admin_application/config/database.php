<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/
//if(ENVIRONMENT == 'local')
    include_once BASEPATH."../ChefConfig.php";

// Non-provisioned IOPS database
$active_group = 'default';


    $db['default']['hostname'] = getenv("SCRATCHHOST");
    $db['default']['username'] = getenv("SCRATCHUSER");
    $db['default']['password'] = getenv("SCRATCHPASSWORD");
    $db['default']['database'] = getenv("SCRATCHDB");
    $db['default']['dbdriver'] = 'mysqli';
    $db['default']['port'] = "3306";
    $db['default']['dbprefix'] = '';
    $db['default']['pconnect'] = TRUE;
    $db['default']['db_debug'] = TRUE;
    $db['default']['cache_on'] = FALSE;
    $db['default']['cachedir'] = '';
    $db['default']['char_set'] = 'utf8';
    $db['default']['dbcollat'] = 'utf8_general_ci';
    $db['default']['swap_pre'] = '';
    $db['default']['autoinit'] = TRUE;
    $db['default']['stricton'] = FALSE;
    $db['default']['ssl_set'] = true;
    $db['default']['ssl_key'] = NULL;
    $db['default']['ssl_cert'] = NULL;
    $db['default']['ssl_capath'] = NULL;
    $db['default']['ssl_cipher'] = NULL;

    $db['admin']['hostname'] = getenv("MAINHOST");
    $db['admin']['username'] = getenv("MAINUSER");
    $db['admin']['password'] = getenv("MAINPASSWORD");
    $db['admin']['database'] = getenv("MAINDB");
    $db['admin']['dbdriver'] = 'mysqli';
    $db['admin']['port'] 	 = 3306;
    $db['admin']['dbprefix'] = '';
    $db['admin']['pconnect'] = TRUE;
    $db['admin']['db_debug'] = FALSE;
    $db['admin']['cache_on'] = FALSE;
    $db['admin']['cachedir'] = '';
    $db['admin']['char_set'] = 'utf8';
    $db['admin']['dbcollat'] = 'utf8_general_ci';
    $db['admin']['swap_pre'] = '';
    $db['admin']['autoinit'] = TRUE;
    $db['admin']['stricton'] = FALSE;
    $db['admin']['ssl_set']  = true;
    $db['admin']['ssl_key']  = NULL;
    $db['admin']['ssl_cert'] = NULL;
    $db['admin']['ssl_capath'] = NULL;
    $db['admin']['ssl_cipher'] = NULL;

    $db['slots']['hostname'] = getenv("SLOTHOST");
    $db['slots']['username'] = getenv("SLOTUSER");
    $db['slots']['password'] = getenv("SLOTPASSWORD");
    $db['slots']['database'] = getenv("SLOTDB");
    $db['slots']['dbdriver'] = 'mysqli';
    $db['slots']['port'] 	 = 3306;
    $db['slots']['dbprefix'] = '';
    $db['slots']['pconnect'] = TRUE;
    $db['slots']['db_debug'] = FALSE;
    $db['slots']['cache_on'] = FALSE;
    $db['slots']['cachedir'] = '';
    $db['slots']['char_set'] = 'utf8';
    $db['slots']['dbcollat'] = 'utf8_general_ci';
    $db['slots']['swap_pre'] = '';
    $db['slots']['autoinit'] = TRUE;
    $db['slots']['stricton'] = FALSE;
    $db['slots']['ssl_set']  = true;
    $db['slots']['ssl_key']  = NULL;
    $db['slots']['ssl_cert'] = NULL;
    $db['slots']['ssl_capath'] = NULL;
    $db['slots']['ssl_cipher'] = NULL;
/* End of file database.php */
/* Location: ./application/config/database.php */