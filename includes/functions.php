<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\includes;

use phpbb\config\config;
use phpbb\db\driver\driver_interface as database;

final class functions {

	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var driver_interface
	 */
	protected $database;

	/**
	 * Constructor
	 */
	public function __construct( config $config, database $database ) {

		$this->config = $config;
		$this->database = $database;

	}

	/**
	 * Returns whether the user is verified or not.
	 *
	 * @param integer $user_id    The user ID to check if they are verified.
	 * @param boolean $hide_check (optional) A flag that determines whether the badge
	 *                            visibility should be checked too. Defaults to true,
	 *                            pass false to just check if they are verified.
	 * 
	 * @return boolean  True if user is verified, false if not.
	 */
	public function is_user_verified( $user_id, $hide_check = true ) {

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

			if ( ! $new_auth->acl_get( 'u_hide_verified_badge' ) ) {

				return false;

			}

		}

		return true;

	}

	/**
	 * Returns whether a group auto verifies it's members.
	 * 
	 * @param  integer $group_id The group ID to check for verification.
	 * 
	 * @return boolean  True if groupdoes verify members, false if not.
	 */
	public function is_group_verified( $group_id ) {

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
	 * Return the URL of a custom verification badge.
	 * 
	 * @param boolean $strict (optional) A flag used to decided whether to check just the
	 *                        database or the file system as well for the presence of a
	 *                        custom badge. It's strongly advised that this is set to true.
	 * 
	 * @return boolean|string $url The URL of the custom badge or false if one is not set.
	 */
	public function has_custom_badge( $strict = true ) {

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
	 * Return a unique identifier for notifications.
	 * 
	 * @return integer  The integer for an notification item ID.
	 */
	public function create_notification_item_id() {

		$item_id = (int) $this->config[ 'verified_profiles_notify_item_id' ];

		$item_id += 1;

		$this->config->set( 'verified_profiles_notify_item_id', $item_id );

		return $item_id;

	}

}
