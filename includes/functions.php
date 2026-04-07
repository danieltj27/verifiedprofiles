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
	 * @param  integer $user_id The user ID to check if they are verified.
	 * 
	 * @return boolean  True if user is verified, false if not.
	 */
	public function is_user_verified( $user_id ) {

		$user_id = (int) $user_id;

		$result = $this->database->sql_query( 'SELECT user_verified FROM ' . USERS_TABLE . ' WHERE ' .
			$this->database->sql_build_array( 'SELECT', [
				'user_id'		=> $user_id,
				'user_verified'	=> 1,
			]
		) );

		$user = $this->database->sql_fetchrow( $result );
		
		$this->database->sql_freeresult( $result );

		if ( false === $user ) {

			return false;

		}

		return true;

	}

	/**
	 * Verify a collection of users.
	 * 
	 * @param array|integer $user_ids An array containing the user IDs to verify.
	 * 
	 * @return boolean  True if successful or false if not.
	 */
	public function verify_users( $user_ids = [] ) {

		// Don't accept an array or a single integer.
		if ( ( ! is_array( $user_ids ) && ! is_int( $user_ids ) ) || ( is_array( $user_ids ) && empty( $user_ids ) ) ) {

			return false;

		}

		// Convert to array for query later.
		if ( is_int( $user_ids ) ) {

			$user_ids[] = $user_ids;

		}

		$this->database->sql_query( 'UPDATE ' . USERS_TABLE . ' SET ' . $this->database->sql_build_array( 'UPDATE', [
			'user_verified' => 1,
		] ) . ' WHERE ' . $this->database->sql_in_set( 'user_id', $user_ids ) );

		if ( false === $this->database->sql_affectedrows() ) {

			return false;

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
	 * Returns whether a user has hidden their badge.
	 * 
	 * @param  integer $user_id The user ID to check their badge visibility.
	 * 
	 * @return boolean  True if the badge is hidden, false if not.
	 */
	public function is_badge_hidden( $user_id ) {

		$user_id = (int) $user_id;

		// Return everything (*) because of the auth check further below.
		$result = $this->database->sql_query( 'SELECT * FROM ' . USERS_TABLE . ' WHERE ' .
			$this->database->sql_build_array( 'SELECT', [
				'user_id'					=> $user_id,
				'user_verified'				=> 1,
				'user_verify_visibility'	=> 0, // 0 means hide the badge.
			]
		) );

		$user = $this->database->sql_fetchrow( $result );
		
		$this->database->sql_freeresult( $result );

		if ( false === $user ) {

			return false;

		}

		$new_auth = new \phpbb\auth\auth();
		$new_auth->acl( $user );

		if ( ! $new_auth->acl_get( 'u_hide_verified_badge' ) ) {

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

}
