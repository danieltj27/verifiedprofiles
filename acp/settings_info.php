<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\acp;

class settings_info {

	public function module() {

		return [
			'filename'	=> '\danieltj\verifiedprofiles\acp\settings_module',
			'title'		=> 'ACP_VERIFIED_PROFILES',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_VERIFIED_PROFILES',
					'auth'	=> 'ext_danieltj/verifiedprofiles && acl_a_board',
					'cat'	=> [ 'ACP_CAT_USERS' ],
				],
			],
		];

	}

}
