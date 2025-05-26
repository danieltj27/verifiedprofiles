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
	'ACP_VERIFIED_PROFILES'					=> 'Verified Profiles',
	'ACP_VERIFIED_PROFILES_MODULE_TITLE'	=> 'Settings',

	'ACP_SETTINGS_CUSTOM_IMAGE_LABEL'					=> 'Custom Image',
	'ACP_SETTINGS_CUSTOM_IMAGE_DESCRIPTION'				=> 'Upload a custom image to replace the default verified icon. The recommended file type is <code>PNG</code>.',
	'ACP_SETTINGS_DELETE_CUSTOM_IMAGE_LABEL'			=> 'Delete existing image',
	'ACP_SETTINGS_LOCATION_MANAGE_LABEL'				=> 'Locations',
	'ACP_SETTINGS_LOCATION_MANAGE_DESCRIPTION'			=> 'Select which pages you would like a verification badge to be displayed on.',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_APP'			=> 'Extensions &amp; FAQ pages (<code>app.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_INDEX'			=> 'Index &amp; Administration Control Panel (<code>index.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_MCP'			=> 'Moderator Control Panel (<code>mcp.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_MEMBERLIST'		=> 'Profiles, groups &amp; member list (<code>memberlist.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_POSTING'		=> 'Text editor (<code>posting.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_REPORT'			=> 'Reports page (<code>report.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_SEARCH'			=> 'Search results (<code>search.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_UCP'			=> 'User Control Panel (<code>ucp.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWFORUM'		=> 'Forum list (<code>viewforum.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWONLINE'		=> 'Online list (<code>viewonline.php</code>)',
	'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWTOPIC'		=> 'Topic list (<code>viewtopic.php</code>)',
	'ACP_SETTINGS_SUCCESS_MESSAGE'						=> 'Extension settings saved successfully.',
	'ACP_SETTINGS_ERROR_BADGE_SIZE'						=> 'The image you uploaded has an invalid file size.',
	'ACP_SETTINGS_ERROR_BADGE_TYPE'						=> 'The image you uploaded has an invalid file type.',
	'ACP_SETTINGS_ERROR_BADGE_UPLOAD'					=> 'The image you uploaded could not be saved on the server.',

	'ACP_LOGS_SETTINGS_SAVED'			=> '<strong>Verified Profiles</strong>:<br />» Extension settings saved successfully.',
	'ACP_LOGS_BADGE_SIZE_ERROR'			=> '<strong>Verified Profiles</strong>:<br />» [ERROR] Failed to upload custom image due to bad size.',
	'ACP_LOGS_BADGE_TYPE_ERROR'			=> '<strong>Verified Profiles</strong>:<br />» [ERROR] Failed to upload custom image due to bad type.',
	'ACP_LOGS_BADGE_UPLOAD_ERROR'		=> '<strong>Verified Profiles</strong>:<br />» [ERROR] Failed to upload custom image to the server.',

	'ACP_USER_SETTING_LABEL'			=> 'Verified Profile',
	'ACP_USER_SETTING_DESCRIPTION'		=> 'Select <code>ENABLE</code> to display a verified badge next to their username.',
	'ACP_GROUP_SETTING_LABEL'			=> 'Verified Group',
	'ACP_GROUP_SETTING_DESCRIPTION'		=> 'Select <code>ENABLE</code> to display a verified badge next to all group member\'s usernames.',

	'ACP_VERIFY_SETTING_ENABLE'			=> 'Enable',
	'ACP_VERIFY_SETTING_DISABLE'		=> 'Disable',
] );
