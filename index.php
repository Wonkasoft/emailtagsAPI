<?php

session_start();

/**
 * This file is the main API for GetResponse
 *
 * @category   GetResponse API
 * @package    GetResponse
 * @author     Wonkasoft <Support@Wonkasoft.com>
 * @copyright  2019 Wonkasoft
 * @version    Release: 1.0.0
 * @since      file available since Release 1.0.0
 */

$config = parse_ini_file( 'inc/config.ini' );

if ( empty( $_GET ) ) {
	header( 'Location: https://aperabags.com/' );
	die();
} elseif ( empty( $_GET['nonce'] ) ) {
	header( 'Location: https://aperabags.com/' );
	die();
} elseif ( $_GET['nonce'] != 4646 ) {
	exit( 'Invalid request' );
} else {
	$email     = $_GET['email'];
	$status    = $_GET['status'];
	$nonce     = $_GET['nonce'];
	$useragent = $_SERVER['HTTP_USER_AGENT'];

	$token = $config['key'];

	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $token,
		'Referer: localhost',
		'Content-Type: multipart/form-data',
	);

	$post_data = array(
		'email'  => $email,
		'status' => $status,
	);

	$ch = curl_init();

	$url = 'https://api.getresponse.com/v3/contacts?query[email]=info@wonkasoft.com&query[origin]=api';

	curl_setopt( $ch, CURLOPT_URL, $url );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );

	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

	curl_setopt( $ch, CURLOPT_HEADER, true );

	// curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );

	$output = json_decode( json_encode( curl_exec( $ch ) ), false );

	if ( $output === false ) {
		echo 'cURL Error: ' . curl_error( $ch );
	}

	curl_close( $ch );

	echo $output;
}



