<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
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

	/**
	 * Enable notifications.
	 *
	 * @param  mixed $old_state The old state returned from the last call of this method.
	 * @return mixed            Returns a boolean (false) or string value.
	 */
	public function enable_step( $old_state ) {

		if ( false === $old_state ) {

			$notifications = $this->container->get( 'notification_manager' );

			$notifications->enable_notifications( 'danieltj.verifiedprofiles.notification.type.verified' );

			return 'notification';

		}

		return parent::enable_step( $old_state );

	}

	/**
	 * Disable notifications.
	 *
	 * @param  mixed $old_state The old state returned from the last call of this method.
	 * @return mixed            Returns a boolean (false) or string value.
	 */
	public function disable_step( $old_state ) {

		if ( false === $old_state ) {

			$notifications = $this->container->get( 'notification_manager' );

			$notifications->disable_notifications( 'danieltj.verifiedprofiles.notification.type.verified' );

			return 'notification';

		}

		return parent::disable_step( $old_state );

	}

	/**
	 * Purge notifications.
	 *
	 * @param  mixed $old_state The old state returned from the last call of this method.
	 * @return mixed            Returns a boolean (false) or string value.
	 */
	public function purge_step( $old_state ) {

		if ( false === $old_state ) {

			$notifications = $this->container->get( 'notification_manager' );

			$notifications->purge_notifications( 'danieltj.verifiedprofiles.notification.type.verified' );

			return 'notification';

		}

		return parent::purge_step( $old_state );

	}

}
