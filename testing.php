<?php

$config           = parse_ini_file( 'inc/config.ini' );
$GLOBALS['token'] = $config['key'];

$api_url        = 'https://api.getresponse.com/v3';
$tag_name = 'approved';
$tag_id = 'Pa9u';
$email = 'louisl04@hotmail.com';
$contact_id = 'SHO5QT';

$headerdata = array(
	'X-Auth-Token: api-key ' . $GLOBALS['token'],
	'Content-Type: application/json',
);
$payload    = array(
	'tags'      => array(
		array(
			'tagId' => $tag_id,
		),
	),
);

$payload  = json_encode( $payload );

$ch         = curl_init();
$url        = $api_url . '/contacts/' . $contact_id . '/tags';
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HEADER, false );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLPROTO_HTTPS, true );

$response = curl_exec( $ch );

if ( false === $response ) :
	curl_close( $ch );
	$error_obj = array(
		'error' => curl_error( $ch ),
		'status'    => 'failed',
	);
	$error_obj = json_decode( $error_obj );
	return $error_obj;
else :
	curl_close( $ch );
	$response = json_decode( $response );

	echo "<pre>\n";
	print_r( $response );
	echo "</pre>\n";
	return true;
endif;

