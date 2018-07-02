<?php
require '../vendor/autoload.php';
// You can specify the scheme, host and port to connect as an array
// to the constructor.
try {
    $redis = phpiredis_pconnect("pub-redis-15657.us-east-mz.2.ec2.garantiadata.com", "15657");
    /*$redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => 'pub-redis-15657.us-east-mz.2.ec2.garantiadata.com',
        "port" => 15657));*/
    print_r($redis);
    echo "Successfully connected to Redis";
}
catch (Exception $e) {
    echo "Couldn't connected to Redis";
    echo $e->getMessage();
}