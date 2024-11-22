<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2023 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\migrations;

class install_v220 extends \phpbb\db\migration\migration {

	/**
	 * Requires 2.1 migration.
	 */
	static public function depends_on() {

		return [ '\danieltj\verifiedprofiles\migrations\install_v210' ];

	}

	/**
	 * Update stored data.
	 */
	public function update_data() {

		return [
			[ 'config.add', [ 'verified_profiles_custom_badge', '' ] ],
			[ 'config.add', [ 'verified_profiles_badge_locations', '' ] ],

			[ 'module.add', [ 'acp', 'ACP_CAT_DOT_MODS', 'ACP_VERIFIED_PROFILES' ] ],
			[ 'module.add', [ 'acp', 'ACP_VERIFIED_PROFILES', [ 'module_basename' => '\danieltj\verifiedprofiles\acp\settings_module', 'modes' => [ 'settings' ] ] ] ],
		];

	}

}
