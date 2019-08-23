<?php


$headerdata = array(
	'User-Agent:' . $_SERVER['HTTP_USER_AGENT'],
	'Referer: localhost',
	'Content-Type: application/x-www-form-urlencoded',
);

$post_data = array(
	'email' => 'louisl04@hotmail.com',
	'tag'   => 'approved',
	'nonce' => 'ubHk73twHt6L',
);

$post_data = http_build_query( $post_data );



// email=louisl04@hotmail.com&tag=approved&nonce=ubHk73twHt6L
$ch  = curl_init();
$url = 'https://aperabags.com/getresponse-api?email=louisl04@hotmail.com&tag=approved&nonce=ubHk73twHt6L';
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_HEADER, false );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
$output = curl_exec( $ch );
$output = json_decode( $output, false );

if ( $output === false ) {
	echo 'cURL Error: ' . curl_error( $ch );
}
curl_close( $ch );

echo $output;


