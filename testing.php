<?php
$config           = parse_ini_file( 'inc/config.ini' );
$GLOBALS['token'] = $config['key'];

$api_url        = 'https://api.getresponse.com/v3';
$tag_name = 'approved';
$tag_id = 'Pa9u';
$email = 'louisl04@hotmail.com';
$contact_id = 'SHO5QT';
$campaign_name = 'zip_program_signups';
$campaign_id = 'W6iSC';
$af_code_id = 'VjOQn6';
$custom_field_code = 'affiliate_code';
$affiliate_code = 'fake';
$af_link_id = 'VjOQC8';
$custom_field_link = 'affiliate_link';
$affiliate_link = 'https://wonkasoft.com';

$prep_data = array(
	'email'             => $email,
	'tags'            => array(
		$tag_id,
	),
	'campaign_name'     => array(
		'campaignId'    => $campaign_id,
	),
	'customFieldValues' => array(
		array(
			'id'      => $af_code_id,
			'name'    => $custom_field_code,
			'value'    => array( $affiliate_code ),
		),
		array(
			'id'      => $af_link_id,
			'name'    => $custom_field_link,
			'value'    => array( $affiliate_link ),
		),
	),
);
$prep_data = json_decode( json_encode( $prep_data ) );

$headerdata = array(
	'X-Auth-Token: api-key ' . $GLOBALS['token'],
	'Content-Type: application/x-www-form-urlencoded',
);

	  $post_data  = array(
		  'query' => array(
			  'email'          => $email,
			  'name'           => null,
			  'campaignId'     => null,
			  'origin'         => null,
			  'createdOn'      => array(
				  'from'         => null,
				  'to'           => null,
			  ),
			  'changedOn'      => array(
				  'from'         => null,
				  'to'           => null,
			  ),
		  ),
		  'sort'             => array(
			  'createdOn'      => null,
			  'changedOn'      => null,
			  'campaignId'     => null,
		  ),
		  'additionalFlags'  => null,
		  'fields'           => null,
		  'perPage'          => null,
		  'page'             => null,
	  );

	  $post_data  = json_decode( json_encode( $post_data ) );
	  $post_data  = http_build_query( $post_data );

	  $ch         = curl_init();
	  $url        = $api_url . '/contacts?' . $post_data;
	  curl_setopt( $ch, CURLOPT_URL, $url );
	  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	  curl_setopt( $ch, CURLOPT_HEADER, false );
	  curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
		curl_setopt( $ch, CURLPROTO_HTTPS, true );

	  $response   = curl_exec( $ch );

	  if ( false === $response ) :
			$error_obj = array(
				'error' => curl_error( $ch ),
				'status'    => 'failed',
			);
			curl_close( $ch );
			$error_obj = json_decode( json_encode( $error_obj ) );
			return $error_obj;
	else :
		curl_close( $ch );
		$response = json_decode( $response );

		echo "<pre>\n";
		print_r( $response );
		echo "</pre>\n";
		return $response;
		endif;
