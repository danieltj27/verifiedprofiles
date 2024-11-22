<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'UCP_VERIFIED_PROFILE_VISIBILITY'			=> 'Verified badge',
	'UCP_VERIFIED_PROFILE_VISIBILITY_EXPLAIN'	=> 'Choose whether or not you want to hide your verification badge from other users.',

	'UCP_VERIFY_SETTING_SHOW'	=> 'Show',
	'UCP_VERIFY_SETTING_HIDE'	=> 'Hide'
] );