<?php

$config           = parse_ini_file( 'inc/config.ini' );
$GLOBALS['token'] = $config['key'];

$api_url        = 'https://api.getresponse.com/v3';
$tag_name = 'approved';
$tag_id = 'Pa9u';
$email = 'louisl04@hotmail.com';
$contact_id = 'SHO5QT';
$campaign_name = 'zip';
$affiliate_code = 'fake';
$affiliate_link = 'https://wonkasoft.com';

$prep_data = array(
	'email'             => $email,
	'tags'            => array(
		$tag_id,
	),
	'campaign_name'     => array(
		'campaignId'    => $campaign_name,
	),
	'customFieldValues' => array(
		'affiliate_code'    => $affiliate_code,
		'affiliate_link'    => $affiliate_link,
	),
);
$prep_data = json_decode( json_encode( $prep_data ) );

$headerdata = array(
	'X-Auth-Token: api-key ' . $GLOBALS['token'],
	'Content-Type: application/json',
);

$tags = array();
foreach ( $prep_data->tags as $value ) :
	$tag_add = array(
		'tagId' => $value,
	);
	$tag_add = json_decode( json_encode( $tag_add ) );
	array_push( $tags, $tag_add );
endforeach;
  $custom_fields = array();
foreach ( $prep_data->customFieldValues as $key => $value ) :
	$custom_add = array(
		'customFieldId' => $key,
		'value'         => array(
			$value,
		),
	);
	$custom_add = json_decode( json_encode( $custom_add ) );
	array_push( $custom_fields, $custom_add );
  endforeach;

  $payload  = array(
	  'name'              => null,
	  'campaign'          => $prep_data->campaign_name,
	  'email'             => $prep_data->email,
	  'dayOfCycle'        => null,
	  'note'              => '',
	  'scoring'           => null,
	  'ipAddress'         => '',
	  'tags'              => $tags,
	  'customFieldValues' => $custom_fields,
  );

  $payload = json_encode( $payload );
  echo "<pre>\n";
	print_r( $payload );
	echo "</pre>\n";

  $ch         = curl_init();
  $url        = $api_url . '/contacts/' . $contact_id;
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
  curl_setopt( $ch, CURLOPT_HEADER, false );
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
	  return $response;
  endif;
