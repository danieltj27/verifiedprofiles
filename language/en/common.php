<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'VERIFIED'								=> 'Verified',
	'VERIFIED_ARIA_LABEL'					=> 'This user is verified.',
	'ACP_VERIFY_SETTING_LABEL'				=> 'Verification',
	'ACP_VERIFY_SETTING_LABEL_EXPLAIN'		=> 'Enable to display a verified badge next to the username.',
	'ACP_VERIFY_SETTING_ENABLE'				=> 'Enable',
	'ACP_VERIFY_SETTING_DISABLE'			=> 'Disable',
	'ACP_GROUP_VERIFIED_SETTING'			=> 'Verify group members',
	'ACP_GROUP_VERIFIED_SETTING_EXPLAIN'	=> 'Enable this setting to automatically verify all members in this group.',
	'UCP_VERIFY_HIDE_BADGE'					=> 'Verified badge',
	'UCP_VERIFY_HIDE_BADGE_EXPLAIN'			=> 'Choose whether or not you want to hide your verification badge from other users.',
	'UCP_VERIFY_HIDE_OPTION_SHOW'			=> 'Show',
	'UCP_VERIFY_HIDE_OPTION_HIDE'			=> 'Hide',
] );
