<?php
/*
 *	Defines the configuration for the Memcached server to use
 */
if (1)//file_exists("development_server.php"))
{
	// DEVELOPMENT machine
	$GLOBALS['MEMCACHED_ENPOINT'] = "localhost";//"teamdevcache.3etfnk.cfg.usw1.cache.amazonaws.com";
	$GLOBALS['MEMCACHED_PORT'] = 11211;
	
}
else
{
	// RELEASE/DEPLOYMENT machine
	$GLOBALS['MEMCACHED_ENPOINT'] = "kizzangcache.3etfnk.cfg.usw1.cache.amazonaws.com";
	$GLOBALS['MEMCACHED_PORT'] = 11211;	
}


// Create and return a memcached server, null on failure
function createMemcached ()
{
	$memcache = new Memcached ();
	if ($GLOBALS['MEMCACHED_ENPOINT'] != 'localhost')
	{
		// AWS memcached servers need this option, local host cannot use it
		$memcache->setOption(Memcached::OPT_CLIENT_MODE, Memcached::DYNAMIC_CLIENT_MODE);
	}
	if (false == $memcache->addServer($GLOBALS['MEMCACHED_ENPOINT'], $GLOBALS['MEMCACHED_PORT']))
	{
		echo "Cannot connect to memcached server" . PHP_EOL;
		$memcache = null;
	}
	return $memcache;
}


