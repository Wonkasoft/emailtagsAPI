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
$GLOBALS['api_url'] = 'https://api.getresponse.com/v3';
if ( empty( $_GET ) ) {
	header( 'Location: https://aperabags.com/' );
	die();
} elseif ( empty( $_GET['nonce'] ) ) {
	header( 'Location: https://aperabags.com/' );
	die();
} elseif ( $_GET['nonce'] != 'ubHk73twHt6L' ) {
	exit( 'Invalid request' );
} else {
	$email      = ( isset( $_GET['email'] ) ) ? $_GET['email'] : null;
	$tag        = ( isset( $_GET['tag'] ) ) ? $_GET['tag'] : null;
	$nonce      = ( isset( $_GET['nonce'] ) ) ? $_GET['nonce'] : null;
	$update_contact   = ( isset( $_GET['update_contact'] ) ) ? $_GET['update_contact'] : null;
	$campaign_name   = ( isset( $_GET['campaign_name'] ) ) ? $_GET['campaign_name'] : null;
	$custom_field_code  = ( isset( $_GET['custom_field_code'] ) ) ? $_GET['custom_field_code'] : null;
	$affiliate_code  = ( isset( $_GET['affiliate_code'] ) ) ? $_GET['affiliate_code'] : null;
	$custom_field_link  = ( isset( $_GET['custom_field_link'] ) ) ? $_GET['custom_field_link'] : null;
	$affiliate_link  = ( isset( $_GET['affiliate_link'] ) ) ? $_GET['affiliate_link'] : null;
	$contact_id = null;
	$tag_id = null;

	if ( $email ) :
		$contact_list = get_contact_list( $email );

		if ( $contact_list ) :
			foreach ( $contact_list as $current_contact ) {
				if ( $email === $current_contact->email ) :
					$contact_obj = $current_contact;
			   endif;
			}

			$contact_id = $contact_obj->contactId;
			$contact_name = $contact_obj->name;
		endif;
	endif;

	if ( null === $contact_id ) {
		echo 'error 101';
	} else {
		if ( $tag ) :
			$tag_list = get_the_list_of_tags( $tag );

			if ( $tag_list ) :
				foreach ( $tag_list as $current_tag ) {
					if ( $tag === $current_tag->name ) :
						$tag_obj = $current_tag;
				   endif;
				}
				$tag_id = $tag_obj->tagId;
			endif;
		endif;

		if ( $campaign_name ) :
			$campaign_list = get_a_list_of_campaigns( $campaign_name );

			if ( $campaign_list ) :
				foreach ( $campaign_list as $campaign ) :
					if ( $campaign_name === $campaign->name ) :
						$campaign_obj = $campaign;
					endif;
				endforeach;
			endif;
		endif;

		if ( $custom_field_code ) :
			$prep_fields = array(
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
			$prep_fields = json_decode( json_encode( $prep_fields ) );
			upsert_the_custom_fields_of_a_contact( $contact_id, $prep_fields );
		endif;

		if ( $update_contact && $contact_name && $campaign_name && $affiliate_code && $affiliate_link ) :
			$prep_data = array(
				'contact_name'      => $contact_name,
				'email'             => $email,
				'tags'            => array(
					$tag_id,
				),
				'campaign_name'     => array(
					'campaignId'    => $campaign_obj->campaignId,
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
			update_contact_details( $prep_data, $contact_id, $useragent );
		endif;

		if ( upsert_the_tags_of_contact( $email, $tag_id, $contact_id ) ) {
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
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Content-Type: application/json',
	);

	$tags = array();
	foreach ( $data->tags as $cur_tag ) :
		$tag_add = array(
			'tagId' => $cur_tag,
		);
		array_push( $tags, $tag_add );
	endforeach;
	$custom_fields = array();
	$custom_field_values = $data->customFieldValues;
	foreach ( $custom_field_values as $cur_field ) :
		$custom_add = array(
			'customFieldId' => $cur_field->id,
			'name'          => $cur_field->name,
			'value'         => $cur_field->value,
		);
		array_push( $custom_fields, $custom_add );
	endforeach;

	$payload  = array(
		'name'              => $data->contact_name,
		'campaign'          => $data->campaign_name,
		'email'             => $data->email,
		'tags'              => $tags,
		'customFieldValues' => $custom_fields,
	);

	$payload = json_encode( $payload );
	echo "<pre>\n";
	print_r( $payload );
	echo "</pre>\n";

	$ch         = curl_init();
	$url        = $GLOBALS['api_url'] . '/contacts/' . $contact_id;
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = curl_exec( $ch );

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
	$url        = $GLOBALS['api_url'] . '/contacts/' . $contact_id . '/tags';
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = curl_exec( $ch );

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
	$url        = $GLOBALS['api_url'] . '/contacts?query[email]=info@wonkasoft.com&query[origin]=api';
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = json_decode( json_encode( curl_exec( $ch ) ), false );
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

		return true;
	endif;
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
	$url        = $GLOBALS['api_url'] . '/contacts?query[email]=info@wonkasoft.com&query[origin]=api';
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = json_decode( json_encode( curl_exec( $ch ) ), false );
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

		return true;
	endif;
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
	$url        = $GLOBALS['api_url'] . '/tags?' . $post_data;
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
	curl_setopt( $ch, CURLPROTO_HTTPS, true );

	$response = curl_exec( $ch );

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

		return $response;
	endif;
}

/**
 * This functions gets a list of the contacts from passed query.
 *
 * @rest_endpoint GET /contacts
 * @param  string $email contains the email of the contact passed in.
 * @return object        return an object of a list of contacts.
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
	  $url        = $GLOBALS['api_url'] . '/contacts?' . $post_data;
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

		return $response;
		endif;
}

/**
 * This function will get a list of campaigns.
 *
 * @rest_endpoint GET /campaigns
 * @param  string $campaign_name contains the name of the campaign passed in.
 * @return object                returns object of campaigns.
 */
function get_a_list_of_campaigns( $campaign_name ) {
	  $headerdata = array(
		  'X-Auth-Token: api-key ' . $GLOBALS['token'],
		  'Content-Type: application/x-www-form-urlencoded',
	  );

		$post_data  = array(
			'query' => array(
				'name'           => $campaign_name,
				'isDefault'     => null,
			),
			'sort'             => array(
				'name'          => null,
				'createdOn'      => null,
			),
			'fields'           => null,
			'perPage'          => null,
			'page'             => null,
		);

		$post_data  = json_decode( json_encode( $post_data ) );
		$post_data  = http_build_query( $post_data );

		$ch         = curl_init();
		$url        = $GLOBALS['api_url'] . '/campaigns?' . $post_data;
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

			return true;
		endif;
}

function get_a_list_of_custom_fields() {
	$headerdata = array(
		'X-Auth-Token: api-key ' . $GLOBALS['token'],
		'Content-Type: application/x-www-form-urlencoded',
	);

	$query  = array(
		'query' => array(
			'name'           => null,
		),
		'sort'             => array(
			'name'          => null,
		),
		'fields'           => null,
		'perPage'          => null,
		'page'             => null,
	);

	$query  = json_decode( json_encode( $query ) );
	$query  = http_build_query( $query );

	$ch         = curl_init();
	$url        = $GLOBALS['api_url'] . '/custom-fields?' . $query;
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

		return true;
	endif;
}

/**
 * Upsert (add or update) the custom fields of a contact. This method doesn't remove (unassign) custom fields.
 *
 * @rest_endpoint POST /contacts/{contactId}/custom-fields
 * @param  string $contact_id contains the contacts id.
 * @return boolean             returns errors or true on success.
 */
function upsert_the_custom_fields_of_a_contact( $contact_id, $data ) {
		$headerdata = array(
			'X-Auth-Token: api-key ' . $GLOBALS['token'],
			'Content-Type: application/json',
		);

		$custom_fields = array();
		$custom_field_values = $data->customFieldValues;
		foreach ( $custom_field_values as $cur_field ) :
			$custom_add = array(
				'customFieldId' => $cur_field->id,
				'name'          => $cur_field->name,
				'value'         => $cur_field->value,
			);
			array_push( $custom_fields, $custom_add );
		endforeach;

		$payload    = array(
			'customFieldValues'  => $custom_fields,
		);

		$payload  = json_encode( $payload );

		$ch         = curl_init();
		$url        = $GLOBALS['api_url'] . '/contacts/' . $contact_id . '/custom-fields';
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLPROTO_HTTPS, true );

		$response = curl_exec( $ch );

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

			return true;
		endif;
}
