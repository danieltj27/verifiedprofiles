<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2023 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\core;

use phpbb\db\driver\driver_interface as database;
use phpbb\notification\manager as notifications;

final class functions {

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * @var string
	 */
	protected $root_path;

	/**
	 * @var string
	 */
	protected $php_ext;

	/**
	 * Constructor.
	 */
	public function __construct( database $db, string $table_prefix, string $root_path, string $php_ext ) {

		$this->db = $db;
		$this->table_prefix = $table_prefix;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

	}

	/**
	 * Fetch a user's verification status.
	 *
	 * @param integer $user_id The id of the user to check.
	 * @param bool    $is_id   Fallback flag to search by username instead of user ID.
	 *
	 * @return boolean True or false values only.
	 */
	public function is_user_verified( $user_id = 0, $is_id = true ) {

		if ( true === $is_id ) {

			/**
			 * Force to an integer.
			 */
			$user_id = intval( $user_id );

			$sql = 'SELECT user_verified FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
				'user_id' => $user_id
			] );

		} else {

			/**
			 * Cast to string.
			 */
			$user_name = strval( $user_id );

			$sql = 'SELECT user_verified FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
				'username' => $user_name
			] );

		}

		$result = $this->db->sql_query( $sql );

		$verified = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		/**
		 * Does the user exist?
		 */
		if ( $verified && isset( $verified[ 'user_verified' ] ) && 1 == $verified[ 'user_verified' ] ) {

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
