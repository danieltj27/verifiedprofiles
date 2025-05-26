<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2025 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\includes;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface as database;

final class functions {

	/**
	 * @var auth
	 */
	protected $auth;

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
	public function __construct( auth $auth, config $config, database $database ) {

		$this->auth = $auth;
		$this->config = $config;
		$this->database = $database;

	}

	/**
	 * Check the user's verification status.
	 *
	 * @param  integer $user_id      The user ID to check against.
	 * @param  boolean $check_groups Flag to check user's groups for verification.
	 * @return boolean               The user's verification status (is or is not).
	 */
	public function is_user_verified( $user_id = 0, $check_groups = true ) {

		if ( 0 === $user_id ) {

			return false;

		}

		$sql = 'SELECT user_verified FROM ' . USERS_TABLE . ' WHERE ' . $this->database->sql_build_array( 'SELECT', [
			'user_id' => $user_id,
			'user_verified' => 1
		] );

		$result = $this->database->sql_query( $sql );
		$user = $this->database->sql_fetchrow( $result );
		$this->database->sql_freeresult( $result );

		if ( ! empty( $user ) ) {

			return true;

		}

		if ( true === $check_groups && $this->is_in_verified_group( $user_id ) ) {

			return true;

		}

		return false;

	}

	/**
	 * Check if the user has hidden their badge.
	 * 
	 * @param  integer $user_id The user ID to check against.
	 * @return boolean          The badge visibility status (is or is not).
	 */
	public function is_badge_hidden( $user_id = 0 ) {

		if ( 0 === (int) $user_id ) {

			return true;

		}

		$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE ' . $this->database->sql_build_array( 'SELECT', [
			'user_id' => (int) $user_id
		] );

		$result = $this->database->sql_query( $sql );
		$user = $this->database->sql_fetchrow( $result );
		$this->database->sql_freeresult( $result );

		// Create new auth object for specific user.
		$user_auth = new auth();
		$user_auth->acl( $user );

		if ( ! $user_auth->acl_get( 'u_hide_verified_badge' ) ) {

			return false;

		}

		if ( ! empty( $user ) && '1' === $user[ 'user_verify_visibility' ] ) {

			return false;

		}

		return true;

	}

	/**
	 * Check the group's verification status.
	 *
	 * @param  integer $group_id The group ID to check against.
	 * @return boolean           The group's verification status (is or is not).
	 */
	public function is_group_verified( $group_id = 0 ) {

		if ( 0 === (int) $group_id ) {

			return false;

		}

		$sql = 'SELECT group_verified FROM ' . GROUPS_TABLE . ' WHERE ' . $this->database->sql_build_array( 'SELECT', [
			'group_id' => (int) $group_id,
			'group_verified' => 1
		] );

		$result = $this->database->sql_query( $sql );
		$group = $this->database->sql_fetchrow( $result );
		$this->database->sql_freeresult( $result );

		if ( ! empty( $group ) ) {

			return true;

		}

		return false;

	}

	/**
	 * Check if the user is in a verified group.
	 * 
	 * @param  integer $user_id The user ID to check against.
	 * @return boolean          The group verification status (is or is not).
	 */
	public function is_in_verified_group( $user_id = 0 ) {

		if ( 0 === $user_id ) {

			return false;

		}

		$sql = 'SELECT group_id FROM ' . USER_GROUP_TABLE . ' WHERE ' . $this->database->sql_build_array( 'SELECT', [
			'user_id' => (int) $user_id
		] );

		$result = $this->database->sql_query( $sql );
		$users_groups = $this->database->sql_fetchrowset( $result );
		$this->database->sql_freeresult( $result );

		if ( empty( $users_groups ) ) {

			return false;

		}

		$group_ids = [];

		foreach ( $users_groups as $group ) {

			$group_ids[] = (int) $group[ 'group_id' ];

		}

		$sql = 'SELECT group_verified FROM ' . GROUPS_TABLE . ' WHERE ' . $this->database->sql_in_set( 'group_id', $group_ids );

		$result = $this->database->sql_query( $sql );
		$groups = $this->database->sql_fetchrowset( $result );
		$this->database->sql_freeresult( $result );

		if ( empty( $groups ) ) {

			return false;

		}

		foreach ( $groups as $group ) {

			if ( '1' === $group[ 'group_verified' ] ) {

				return true;

			}

		}

		return false;

	}

	/**
	 * Check if a custom verification badge has been uploaded.
	 * 
	 * @param  boolean        $strict    Check the database and file system for a custom badge or
	 *                                   just check the database for the file name. Setting this to
	 *                                   true is strongly advised.
	 * @return boolean|string $badge_url Returns false if there isn't a custom badge uploaded or can return
	 *                                   a string is a custom badge has been uploaded.
	 */
	public function has_custom_badge( $strict = false ) {

		global $phpbb_root_path;

		// Create the default file location for custom badges.
		$file_location = $phpbb_root_path . 'images';

		if ( true === $strict ) {

			if ( '' === $this->config[ 'verified_profiles_custom_badge' ] || ! file_exists( $file_location . '/' . $this->config[ 'verified_profiles_custom_badge' ] ) ) {

				return false;

			}

		}

		// Create a full path to the custom badge.
		$badge_url = generate_board_url() . '/images/' . $this->config[ 'verified_profiles_custom_badge' ];

		return $badge_url;

	}

	/**
	 * Check if the current page has badges enabled on it.
	 * 
	 * @param  string  $current_page The page to check if badges are enabled.
	 * @return boolean               Either true or false. True if badges can be displayed
	 *                               and false if badges cannot be displayed.
	 */
	public function is_location_enabled( $current_page = '' ) {

		if ( '' === $this->config[ 'verified_profiles_badge_locations' ] ) {

			return false;

		}

		// Convert from JSON into a php array.
		$enabled_locations = json_decode( $this->config[ 'verified_profiles_badge_locations' ] );

		if ( NULL === $enabled_locations ) {

			return false;

		}

		foreach ( $enabled_locations as $location ) {

			$location_file_name = $location . '.php';

			if ( false !== strpos( $current_page, $location_file_name ) ) {

				return true;

			}

		}

		return false;

	}

}
