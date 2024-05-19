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
	 * @param integer $user_id The user ID to check against.
	 *
	 * @return boolean The verification status.
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
	 * Fetch a user by the username.
	 *
	 * @param integer $user_name The username to search for the user.
	 *
	 * @return integer|boolean An array of user data or false on failure.
	 */
	public function get_user_id_by_username( $user_name = '' ) {

		/**
		 * Perform some basic sanitisation.
		 *
		 * @todo
		 */
		$user_name = $user_name;

		/**
		 * Fetch the user data.
		 */
		$sql = 'SELECT user_id FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
 			'username_clean' => $user_name
 		] );

 		$result = $this->db->sql_query( $sql );

		$user = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		/**
		 * Does the user exist?
		 */
		if ( $user && isset( $user[ 'user_id' ] ) ) {

			return $user[ 'user_id' ];

		}

		return false;

	}

	/**
	 * Fetch the topic author's ID.
	 *
	 * @param integer $topic_id The id of the topic to fetch.
	 *
	 * @return integer|boolean The author ID or false on failure.
	 */
	public function get_topic_author_id( $topic_id = 0 ) {

		/**
		 * Force to an integer.
		 */
		$topic_id = intval( $topic_id );

		/**
		 * Fetch the user data.
		 */
		$sql = 'SELECT topic_poster FROM ' . TOPICS_TABLE. ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
 			'topic_id' => $topic_id
 		] );

 		$result = $this->db->sql_query( $sql );

		$author = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		/**
		 * Does the user exist?
		 */
		if ( $author && isset( $author[ 'topic_poster' ] ) ) {

			return intval( $author[ 'topic_poster' ] );

		}

		return false;

	}

	/**
	 * Fetch the topic's last poster ID.
	 *
	 * @param integer $topic_id The id of the topic to fetch.
	 *
	 * @return integer|boolean The last poster ID or false on failure.
	 */
	public function get_topic_last_poster_id( $topic_id = 0 ) {

		/**
		 * Force to an integer.
		 */
		$topic_id = intval( $topic_id );

		/**
		 * Fetch the user data.
		 */
		$sql = 'SELECT topic_last_poster_id FROM ' . TOPICS_TABLE. ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
  			'topic_id' => $topic_id
  		] );

  		$result = $this->db->sql_query( $sql );

		$last_poster = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		/**
		 * Does the user exist?
		 */
		if ( $last_poster && isset( $last_poster[ 'topic_last_poster_id' ] ) ) {

			return intval( $last_poster[ 'topic_last_poster_id' ] );

		}

		return false;

	}

}
