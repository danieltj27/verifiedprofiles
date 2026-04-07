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
	'ACP_VERIFIED_PROFILES'					=> 'Verification',
	'ACP_VERIFIED_PROFILES_SETTINGS'		=> 'Settings',

	'ACP_VERIFY_USER_LABEL'					=> 'Verify user',
	'ACP_VERIFY_USER_DESCRIPTION'			=> 'Select <code>YES</code> to display a verified badge next to their username.',
	'ACP_VERIFY_GROUP_LABEL'				=> 'Verify group members',
	'ACP_VERIFY_GROUP_DESCRIPTION'			=> 'Select <code>YES</code> to verify a user when they are added as a member to this group.',

	'ACP_CUSTOM_VERIFY_BADGE_LABEL'			=> 'Custom verification badge',
	'ACP_CUSTOM_VERIFY_BADGE_DESCRIPTION'	=> 'Upload a custom image to replace the default verified icon. The recommended file type is <code>PNG</code>.',
	'ACP_CUSTOM_VERIFY_BADGE_DELETE'		=> 'Delete existing image',

	// CONFIRMATIONS
	'ACP_VERIFICATION_SETTINGS_SAVED'		=> 'Verification settings saved successfully.',
	'ACP_VERIFICATION_BADGE_ERROR_SIZE'		=> 'The image you uploaded has an invalid file size.',
	'ACP_VERIFICATION_BADGE_ERROR_TYPE'		=> 'The image you uploaded has an invalid file type.',
	'ACP_VERIFICATION_BADGE_ERROR_SAVe'		=> 'The image you uploaded cannot be uploaded to the server.',

	// LOGS
	'ACP_VERIFIED_PROFILES_LOG_SAVED'		=> '<strong>Verified Profiles</strong>:<br />» Verification settings have been updated.',
	'ACP_VERIFIED_PROFILES_LOG_ERROR_SIZE'	=> '<strong>Verified Profiles</strong>:<br />» Custom verification image is an invalid size.',
	'ACP_VERIFIED_PROFILES_LOG_ERROR_TYPE'	=> '<strong>Verified Profiles</strong>:<br />» Custom verification image is an invalid type.',
	'ACP_VERIFIED_PROFILES_LOG_ERROR_SAVE'	=> '<strong>Verified Profiles</strong>:<br />» An error occurred whilst uploading verification image.',
] );
