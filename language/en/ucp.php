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
	'UCP_VERIFICATION_VISIBILITY_LABEL'			=> 'Verified badge',
	'UCP_VERIFICATION_VISIBILITY_DESCRIPTION'	=> 'Choose whether or not you want to hide your verification badge from other users.',
	'UCP_VERIFICATION_VISIBILITY_VALUE_SHOW'	=> 'Show',
	'UCP_VERIFICATION_VISIBILITY_VALUE_HIDE'	=> 'Hide',

	'UCP_VERIFICATION_UPDATE_REG_DETAILS_WARNING'	=> 'Updating your email address or username will remove your verification badge from your profile.',
] );
