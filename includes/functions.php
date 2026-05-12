<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\includes;

use phpbb\config\config;
use phpbb\db\driver\driver_interface as database;
use phpbb\notification\manager as notifications;

final class functions {

	/**
	 * @var phpbb\config\config
	 */
	protected $config;

	/**
	 * @var phpbb\db\driver\driver_interface
	 */
	protected $database;

	/**
	 * @var phpbb\notification\manager
	 */
	protected $notifications;

	/**
	 * Constructor
	 */
	public function __construct( config $config, database $database, notifications $notifications ) {

		$this->config = $config;
		$this->database = $database;
		$this->notifications = $notifications;

	}

	/**
	 * Returns whether the user is verified or not.
	 *
	 * @param int  $user_id    The user ID to check if they are verified.
	 * @param bool $hide_check (optional) A flag that determines whether the badge
	 *                          visibility should be checked too. Defaults to true,
	 *                          pass false to just check if they are verified.
	 * 
	 * @return bool  True if user is verified, false if not.
	 */
	public function is_user_verified( int $user_id, bool $hide_check = true ) : bool {

		$user_id = (int) $user_id;

		$where = [
			'user_id'		=> $user_id,
			'user_verified'	=> 1,
		];

		if ( true === $hide_check ) {

			$where[ 'user_verify_visibility' ] = 1;

		}

		$result = $this->database->sql_query(
			'SELECT * FROM ' . USERS_TABLE . ' WHERE ' . $this->database->sql_build_array( 'SELECT', $where )
		);

		$user = $this->database->sql_fetchrow( $result );
		
		$this->database->sql_freeresult( $result );

		if ( false === $user ) {

			return false;

		}

		if ( true === $hide_check ) {

			$new_auth = new \phpbb\auth\auth();
			$new_auth->acl( $user );

			if ( $new_auth->acl_get( 'u_hide_verified_badge' ) && 0 === $user[ 'user_verify_visibility' ] ) {

				return false;

			}

		}

		return true;

	}

	/**
	 * Returns whether a group auto verifies it's members.
	 * 
	 * @param int $group_id The group ID to check for verification.
	 * 
	 * @return bool  True if groupdoes verify members, false if not.
	 */
	public function is_group_verified( int $group_id ) : bool {

		$group_id = (int) $group_id;

		$result = $this->database->sql_query( 'SELECT group_verified FROM ' . GROUPS_TABLE . ' WHERE ' .
			$this->database->sql_build_array( 'SELECT', [
				'group_id'			=> $group_id,
				'group_verified'	=> 1,
			]
		) );

		$group = $this->database->sql_fetchrow( $result );
		
		$this->database->sql_freeresult( $result );

		if ( false === $group ) {

			return false;

		}

		return true;

	}

	/**
	 * Verify a user profile.
	 * 
	 * @since int  $user_id The user ID used to verify the user.
	 * @since bool $notify  Flag to send notification or not. Defaults tot true.
	 * 
	 * @return bool  True if verified, false if not.
	 */
	public function verify_user( int $user_id, bool $notify = true ) : bool {

		$this->database->sql_query( 'UPDATE ' . USERS_TABLE . ' SET ' . $this->database->sql_build_array( 'UPDATE', [
			'user_verified'	=> 1,
		] ) . ' WHERE ' . $this->database->sql_build_array( 'SELECT', [
			'user_id'		=> $user_id,
			'user_verified'	=> 0,
		] ) );

		if ( ! $this->database->sql_affectedrows() ) {

			return false;

		}

		if ( true === $notify ) {

			$this->notifications->add_notifications( 'danieltj.verifiedprofiles.notification.type.verified', [
				'item_id'	=> $this->create_notification_item_id(),
				'user_id'	=> $user_id,
			] );

		}

		return true;

	}

	/**
	 * Return the URL of a custom verification badge.
	 * 
	 * @param bool $strict (optional) A flag used to decided whether to check just the
	 *                        database or the file system as well for the presence of a
	 *                        custom badge. It's strongly advised that this is set to true.
	 * 
	 * @return bool|string $url The URL of the custom badge or false if one is not set.
	 */
	public function has_custom_badge( bool $strict = true ) : bool|string {

		global $phpbb_root_path;

		// Default location for custom badges.
		$file_location = $phpbb_root_path . 'images';

		if ( true === $strict ) {

			if ( '' === $this->config[ 'verified_profiles_custom_badge' ] || ! file_exists( $file_location . '/' . $this->config[ 'verified_profiles_custom_badge' ] ) ) {

				return false;

			}

		}

		$badge_url = generate_board_url() . '/images/' . $this->config[ 'verified_profiles_custom_badge' ];

		return $badge_url;

	}

	/**
	 * Return whether users lose their badge if they change their
	 * registration details (email & username).
	 * 
	 * @return bool  True if verification is required again, false if not.
	 */
	public function require_user_verify_after_update() : bool {

		if ( 1 === (int) $this->config[ 'verified_profiles_reg_update_verify_again' ] ) {

			return true;

		}

		return false;

	}

	/**
	 * Return a unique identifier for notifications.
	 * 
	 * @return int  The integer for an notification item ID.
	 */
	public function create_notification_item_id() : int {

		$item_id = (int) $this->config[ 'verified_profiles_notify_item_id' ];

		$item_id += 1;

		$this->config->set( 'verified_profiles_notify_item_id', $item_id );

		return $item_id;

	}

}
