<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2023 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'VERIFIED' => 'Verified',
	'VERIFIED_TOOLTIP' => 'User is verified',
	'VERIFIED_LABEL' => 'Verification',
	'VERIFIED_SETTING_YES' => 'Enable',
	'VERIFIED_SETTING_NO' => 'Disable',
	'VERIFIED_EXPLAIN' => 'Enable to display a verified badge next to the username.',
] );
