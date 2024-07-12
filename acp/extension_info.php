<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\acp;

class extension_info {

	public function module() {

		return [
			'filename'	=> '\danieltj\verifiedprofiles\acp\extension_module',
			'title'		=> 'ACP_VERIFIED_PROFILES',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_VERIFIED_PROFILE_SETTINGS',
					'auth'	=> 'ext_danieltj/verifiedprofiles && acl_a_board',
					'cat'	=> [ 'ACP_VERIFIED_PROFILES' ],
				],
			],
		];

	}

}
