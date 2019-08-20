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

$config           = parse_ini_file( 'inc/config.ini' );
$GLOBALS['token'] = $config['key'];

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
	$tag       = $_GET['tag'];
	$nonce     = $_GET['nonce'];
	$useragent = $_SERVER['HTTP_USER_AGENT'];

	$contact_id = search( $email, $useragent );
	if ( $contact_id == null ) {
		echo 'error 101';
	} else {
		addtag( $email, $tag, $useragent, $contact_id );
	}
}


function update( $email ) {
	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
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

function addtag( $email, $tag, $useragent, $contact_id ) {
	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Referer: localhost',
		'Content-Type: multipart/form-data',
	);

	$post_data = array(
		'contact' => $contact_id,
		'tags'    => array(
			'tagId' => 'Pa9u',
			'name'  => 'aproved',
		),
	);

	$ch = curl_init();

	$url = 'https://api.getresponse.com/v3/contacts/' . $contact_id . '/tags/';

	curl_setopt( $ch, CURLOPT_URL, $url );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );

	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

	curl_setopt( $ch, CURLOPT_HEADER, false );

	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );

	$output = curl_exec( $ch );

	$output = json_decode( $output, false );

	if ( $output === false ) {
		echo 'cURL Error: ' . curl_error( $ch );
	}

	curl_close( $ch );

	var_dump( $output );
}

function add( $email ) {
	$headerdata = array(
		'User - Agent:' . $useragent,
		'X - Auth - Token: api - key ' . $GLOBALS['token'],
		'Referer: localhost',
		'Content - Type: multipart / form - data',
	);

	$post_data = array(
		'email'  => $email,
		'status' => $status,
	);

	$ch = curl_init();

	$url = 'https:// api.getresponse.com/v3/contacts?query[email]=info@wonkasoft.com&query[origin]=api';

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

function delete( $email ) {
	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
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

function search( $email, $useragent ) {
	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Referer: localhost',
		'Content-Type: multipart/form-data',
	);

	$post_data = array(
		'email' => $email,
	);

	$ch = curl_init();

	$url = 'https://api.getresponse.com/v3/contacts?query[email]=' . $email . '&query[origin]=api';

	curl_setopt( $ch, CURLOPT_URL, $url );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );

	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

	curl_setopt( $ch, CURLOPT_HEADER, false );

	$output = curl_exec( $ch );

	$output = json_decode( $output, false );

	$output = array_shift( $output );

	$output = ( ! empty( $output->contactId ) ) ? $output->contactId : null;

	if ( $output === false ) {
		echo 'cURL Error: ' . curl_error( $ch );
	}

	curl_close( $ch );

	return $output;
}
