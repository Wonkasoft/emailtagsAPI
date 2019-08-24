<?php


$headerdata = array(
	'Content-Type: application/x-www-form-urlencoded',
);

$post_data = array(
	'email' => 'louisl04@hotmail.com',
	'tag'   => 'zipcompleted',
	'nonce' => 'ubHk73twHt6L',
);


$post_data = json_decode( json_encode( $post_data ) );
$post_data = http_build_query( $post_data );
$ch  = curl_init();
$url = 'https://aperabags.com/getresponse-api/?' . $post_data;
echo "<pre>\n";
print_r( $url );
echo "</pre>\n";
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HEADER, false );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
$output = curl_exec( $ch );

if ( $output === false ) {
	echo 'cURL Error: ' . curl_error( $ch );
}
curl_close( $ch );

echo "<pre>\n";
print_r( $output );
echo "</pre>\n";


