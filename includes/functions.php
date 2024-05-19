<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\includes;

use phpbb\db\driver\driver_interface as database;

final class functions {

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * Constructor
	 */
	public function __construct( database $db ) {

		$this->db = $db;

	}

	/**
	 * Check the user's verification status.
	 *
	 * @param  integer $user_id The user ID to check against.
	 * @return boolean          The verification status.
	 */
	public function is_user_verified( $user_id = 0 ) {

		if ( 0 === $user_id ) {

			return false;

		}

		$user_id = intval( $user_id );

		$sql = 'SELECT user_verified FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
			'user_id' => $user_id
		] );

		$result = $this->db->sql_query( $sql );
		$user = $this->db->sql_fetchrow( $result );
		$this->db->sql_freeresult( $result );

		if ( ! empty( $user ) && '1' === $user[ 'user_verified' ] ) {

			return true;

		}

		return false;

	}

	/**
	 * Check if user has hidden their badge.
	 * 
	 * @param  integer $user_id The user ID to check against.
	 * @return boolean          The badge visibility status (true or false).
	 */
	public function is_badge_hidden( $user_id = 0 ) {

		if ( 0 === $user_id ) {

			return true;

		}

		$user_id = intval( $user_id );

		$sql = 'SELECT user_verify_visibility FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
			'user_id' => $user_id
		] );

		$result = $this->db->sql_query( $sql );
		$user = $this->db->sql_fetchrow( $result );
		$this->db->sql_freeresult( $result );

		if ( ! empty( $user ) && '1' === $user[ 'user_verify_visibility' ] ) {

			return false;

		}

		return true;

	}

}
