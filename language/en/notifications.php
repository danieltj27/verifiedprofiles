<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'VERIFIED_PROFILES_NOTIFICATIONS_VERIFIED_TITLE'	=> '<strong>Profile Verification</strong>',
	'VERIFIED_PROFILES_NOTIFICATIONS_VERIFIED_REASON'	=> 'Your profile has been successfully verified.',

	// UCP
	'VERIFIED_PROFILES_NOTIFICATIONS_SAMPLE'			=> 'Your proifle becomes verified',
] );
