<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\migrations;

class install_v300 extends \phpbb\db\migration\migration {

	/**
	 * Requires the 2.2 migration.
	 */
	static public function depends_on() {

		return [ '\danieltj\verifiedprofiles\migrations\install_v220' ];

	}

	/**
	 * Update database
	 */
	public function update_data() {

		return [
			[ 'config.remove', [ 'verified_profiles_badge_locations' ] ],
			
			[ 'module.remove', [ 'acp', 'ACP_VERIFIED_PROFILES', [
				'module_basename'	=> '\danieltj\verifiedprofiles\acp\settings_module',
				'modes'				=> [ 'settings' ],
			] ] ],
			
			[ 'module.remove', [ 'acp', 'ACP_CAT_DOT_MODS', 'ACP_VERIFIED_PROFILES' ] ],

			[ 'module.add', [ 'acp', 'ACP_CAT_USERS', [
				'module_auth'		=> 'ext_danieltj/verifiedprofiles && acl_a_extensions',
				'module_basename'	=> '\danieltj\verifiedprofiles\acp\settings_module',
				'module_langname'	=> 'ACP_VERIFIED_PROFILES',
				'module_mode'		=> 'settings'
			] ] ],
		];

	}

}
