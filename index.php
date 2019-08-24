<?php
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
} elseif ( $_GET['nonce'] != 'ubHk73twHt6L' ) {
	exit( 'Invalid request' );
} else {
	$api_url        = 'https://api.getresponse.com/v3';
	$email      = ( isset( $_GET['email'] ) ) ? unslash( $_GET['email'] ) : null;
	$tag        = ( isset( $_GET['tag'] ) ) ? unslash( $_GET['tag'] ) : null;
	$nonce      = ( isset( $_GET['nonce'] ) ) ? unslash( $_GET['nonce'] ) : null;
	$useragent  = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? unslash( $_SERVER['HTTP_USER_AGENT'] ) : null;
	$campaign_name   = ( isset( $_GET['campaign_name'] ) ) ? unslash( $_GET['campaign_name'] ) : null;
	$affiliate_code  = ( isset( $_GET['affiliate_code'] ) ) ? unslash( $_GET['affiliate_code'] ) : null;
	$affiliate_link  = ( isset( $_GET['affiliate_link'] ) ) ? unslash( $_GET['affiliate_link'] ) : null;
	$contact_id = null;

	if ( $email ) :
		$contact_list = get_contact_list( $email );

		foreach ( $contact_list as $current_contact ) {
			if ( $email === $current_tag->email ) :
				$contact_obj = $current_contact;
		   endif;
		}

		$contact_id = $contact_obj->contactId;
	endif;

	if ( null === $contact_id ) {
		echo 'error 101';
	} else {
		if ( $tag ) :
			$tag_list = get_the_list_of_tags( $tag );

			foreach ( $tag_list as $current_tag ) {
				if ( $tag === $current_tag->name ) :
					$tag_obj = $current_tag;
			   endif;
			}
		endif;

		if ( $campaign_name && $affiliate_code && $affiliate_link ) :
			$prep_data = array(
				'email'             => $email,
				'tag_id'            => $tag_obj->tagId,
				'campaign_name'     => $campaign_name,
				'affiliate_code'    => $affiliate_code,
				'affiliate_link'    => $affiliate_link,
			);
			$prep_data = json_decode( json_encode( $prep_data ) );
			update_contact_details( $prep_data, $contact_id, $useragent );
		endif;

		if ( upsert_the_tags_of_contact( $email, $tag_obj->tagId, $contact_id ) ) {
			echo '<div>You have added <h3 align="center">' . $tag . '</h3> to <h3 align="center">' . $email . '</h3> you may close your browser now</div>';
		}
	}
}


/**
 * This function will update the current contacts details.
 *
 * @rest_endpoint POST /contacts/{contactId}
 * @param  object $data       contains the payload data.
 * @param  string $contact_id contains the contact id to be updated.
 * @param  string $useragent  contains the referrer agent.
 * @return object             contains the response data from getResponse.
 */
function update_contact_details( $data, $contact_id, $useragent ) {
	$headerdata = array(
		'User-Agent:' . $useragent,
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Referer: localhost',
		'Content-Type: application/json',
	);

	$payload  = array(
		'name'              => null,
		'campaign'          => $data->campaign_name,
		'email'             => $data->email,
		'dayOfCycle'        => null,
		'note'              => null,
		'scoring'           => null,
		'ipAddress'         => null,
		'tags'              => array(
			array(
				'tadId'         => $data->tag_id,
			),
		),
		'customFieldValues' => array(
			array(
				'customFieldId' => key( $data->affiliate_code ),
				'value'         => $data->affiliate_code,
			),
			array(
				'customFieldId' => key( $data->affiliate_link ),
				'value'         => $data->affiliate_link,
			),
		),
	);

	$payload = json_encode( $payload );

	$ch         = curl_init();
	$url        = $api_url . '/contacts/' . $contact_id;
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
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
		return $response;
	endif;
}

/**
 * This function adds updates the tags for a contact.
 *
 * @rest_endpoint POST /contacts/{contactId}/tags
 * @param  string $email      contains the email of this contact.
 * @param  string $tag_id     contains the tag id to be added to contact.
 * @param  string $contact_id contains the contacts id.
 * @return boolean             Will return an error or true on success.
 */
function upsert_the_tags_of_contact( $email, $tag_id, $contact_id ) {
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

	return true;
endif;
}

function add( $email ) {
	$headerdata = array(
		'X - Auth - Token: api - key ' . $GLOBALS['token'],
		'Content - Type: application/json',
	);

	$payload  = array(
		'email'  => $email,
		'status' => $status,
	);

	$ch         = curl_init();
	$url        = $api_url . '/contacts?query[email]=info@wonkasoft.com&query[origin]=api';
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = json_decode( json_encode( curl_exec( $ch ) ), false );
	if ( $response === false ) {
		echo 'cURL Error: ' . curl_error( $ch );
	}
	curl_close( $ch );
	echo $response;
}

function delete( $email ) {
	$headerdata = array(
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Content-Type: application/json',
	);
	$post_data  = array(
		'email'  => $email,
		'status' => $status,
	);

	$ch         = curl_init();
	$url        = $api_url . '/contacts?query[email]=info@wonkasoft.com&query[origin]=api';
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$output = json_decode( json_encode( curl_exec( $ch ) ), false );
	if ( $output === false ) {
		echo 'cURL Error: ' . curl_error( $ch );
	}
	curl_close( $ch );
	echo $output;
}

/**
 * This returns the object of the tag query that is send in.
 *
 * @rest_endpoint GET /tags
 * @param  string $tag       contains tag name to query.
 * @return object            contains the object found from query.
 */
function get_the_list_of_tags( $tag ) {
	$headerdata = array(
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Content-Type: application/x-www-form-urlencoded',
	);

	$post_data  = array(
		'query' => array(
			'name' => $tag,
			'createdAt' => array(
				'from'    => null,
				'to'      => null,
			),
		),
		'sort' => array(
			'createdAt' => null,
		),
		'fields'      => null,
		'perPage'     => null,
		'page'        => null,
	);

	$post_data  = json_decode( json_encode( $post_data ) );
	$post_data  = http_build_query( $post_data );

	$ch         = curl_init();
	$url        = $api_url . '/tags?' . $post_data;
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
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

		return $response;
	endif;
}

/**
 * This functions gets a list of the contacts from passed query.
 *
 * @rest_endpoint GET /contacts
 * @param  [type] $email [description]
 * @return [type]        [description]
 */
function get_contact_list( $email ) {
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

		  return $response;
	  endif;
}
