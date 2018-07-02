<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['redis']['library'] = 'predis';

//$config['redis']['use_socket'] = TRUE;
//$config['redis']['socket'] = '/var/run/redis/redis.sock';

$config['redis']['hostname'] = 'pub-redis-15657.us-east-mz.2.ec2.garantiadata.com';
//$config['redis']['username'] = '';

// Kind of a hack - just using the same password here
$config['redis']['password'] = "K1zz4ng!";
$config['redis']['port'] = '15657';
$config['redis']['use_password'] = TRUE;

$config['redis']['pconnect'] = TRUE;


$config['redis_hostname'] = 'pub-redis-15657.us-east-mz.2.ec2.garantiadata.com';
$config['redis_password'] = "K1zz4ng!";
$config['redis_port'] = '15657';
