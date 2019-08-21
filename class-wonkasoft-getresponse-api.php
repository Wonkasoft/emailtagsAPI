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

defined('ABSPATH') || exit;


/**
 * 
 */
class Wonkasoft_GetResponse_API {


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
			$tag_id = get_list_of_tags( $useragent, $tag );

			addtag( $email, $tag_id, $useragent, $contact_id );
		}
	}


	public function update( $email ) {
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

	public function addtag( $email, $tag_id, $useragent, $contact_id ) {

		$headerdata = array(
			'User-Agent:' . $useragent,
			'X-Auth-Token: api-key ' . $GLOBALS['token'],
			'Referer: localhost',
			'Content-Type: application/json',
		);

		$post_data = array(
			'tags' => array(
				'tagId' => $tag_id,
			),
		);

		$post_data = json_encode( $post_data );

		$ch = curl_init();

		$url = 'https://api.getresponse.com/v3/contacts/' . $contact_id . '/tags';

		curl_setopt( $ch, CURLOPT_URL, $url );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

		curl_setopt( $ch, CURLOPT_HEADER, false );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );

		curl_setopt( $ch, CURLOPT_POST, true );

		$output = curl_exec( $ch );

		// $output = json_decode( $output, false );

		if ( $output === false ) {
			echo 'cURL Error: ' . curl_error( $ch );
		}

		curl_close( $ch );

		echo "<pre>\n";
		print_r( $output );
		echo "</pre>\n";

		return $output;
	}

	public function add( $email ) {
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

	public function delete( $email ) {
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

	public function get_list_of_tags( $useragent, $tag ) {

		$headerdata = array(
			'User-Agent:' . $useragent,
			'X-Auth-Token: api-key ' . $GLOBALS['token'],
			'Referer: localhost',
			'Content-Type: application/x-www-form-urlencoded',
		);

		$post_data = array(
			'query' => array(
				'name' => $tag,
			),
		);

		$post_data = json_encode( $post_data );

		$post_data = json_decode( $post_data );

		$post_data = http_build_query( $post_data );

		$ch = curl_init();

		$url = 'https://api.getresponse.com/v3/tags?' . $post_data;

		curl_setopt( $ch, CURLOPT_URL, $url );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt( $ch, CURLOPT_HEADER, false );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

		$output = curl_exec( $ch );

		$output = json_decode( $output, false );

		$tagid = ( ! empty( $output[0]->tagId ) ) ? $output[0]->tagId : null;

		if ( $output === false ) {
			echo 'cURL Error: ' . curl_error( $ch );
		}

		curl_close( $ch );

		return $tagid;
	}

	public function search( $email, $useragent ) {

		$headerdata = array(
			'User-Agent:' . $useragent,
			'X-Auth-Token: api-key ' . $GLOBALS['token'],
			'Referer: localhost',
			'Content-Type: application/x-www-form-urlencoded',
		);

		$post_data = array(
			'query' => array(
				'email' => $email,
			),
		);

		$post_data = json_encode( $post_data );

		$post_data = json_decode( $post_data );

		$post_data = http_build_query( $post_data );

		$ch = curl_init();

		$url = 'https://api.getresponse.com/v3/contacts?' . $post_data;

		curl_setopt( $ch, CURLOPT_URL, $url );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt( $ch, CURLOPT_HEADER, false );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerdata );

		$output = curl_exec( $ch );

		$output = json_decode( $output, false );

		$contact_id = ( ! empty( $output[0]->contactId ) ) ? $output[0]->contactId : null;

		if ( $output === false ) {
			echo 'cURL Error: ' . curl_error( $ch );
		}

		curl_close( $ch );

		return $contact_id;
	}
}
