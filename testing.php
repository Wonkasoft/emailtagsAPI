<?php
echo "<pre>\n";
print_r( 'Hello!' );
echo "</pre>\n";

$config           = parse_ini_file( 'inc/config.ini' );
$GLOBALS['token'] = $config['key'];

$headerdata = array(
	'Content-Type: application/x-www-form-urlencoded',
);

$post_data = array(
	'email' => 'louisl04@hotmail.com',
	'tag' => 'approved',
	'nonce' => 'ubHk73twHt6L',
);

$post_data = http_build_query( $post_data );

$ch = curl_init();

$url = 'https://aperabags.com/getresponse-api/?' . $post_data;

curl_setopt( $ch, CURLOPT_URL, $url );

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

curl_setopt( $ch, CURLOPT_HEADER, false );

curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

curl_setopt( $ch, CURLPROTO_HTTPS, true );

$output = curl_exec( $ch );

if ( $output === false ) {
	echo 'cURL Error: ' . curl_error( $ch );
}

curl_close( $ch );

echo "<pre>\n";
print_r( $output );
echo "</pre>\n";
