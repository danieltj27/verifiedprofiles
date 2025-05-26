<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2025 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'VERIFIED_PROFILE'				=> 'Verified',
	'VERIFIED_PROFILE_ARIA_LABEL'	=> 'This user is verified.',
] );
