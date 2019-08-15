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
} else {
	$email  = $_GET['email'];
	$status = $_GET['status'];
	$nonce  = $_GET['nonce'];

	echo $email . ' ' . $status . ' ' . $nonce;
}



