<?php

// RFC4627-compliant header
header('Content-type: application/json');

// Encode data
if(isset($response)) {
	echo "outcome=".json_encode($response);
}
else
{
	$error_response=array('Error' => true);
	echo "outcome=".json_encode($error_response);		
}
