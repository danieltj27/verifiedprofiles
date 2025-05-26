<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2025 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles;

class ext extends \phpbb\extension\base {

	/**
	 * Check the minimum required versions.
	 */
	public function is_enableable() {

		$config = $this->container->get( 'config' );

		return phpbb_version_compare( $config[ 'version' ], '3.3.0', '>=' ) && phpbb_version_compare( PHP_VERSION, '7.4.0', '>=' );

	}

}
