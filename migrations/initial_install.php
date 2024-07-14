<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2023 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\migrations;

class initial_install extends \phpbb\db\migration\migration {

	/**
	 * Check extension is installed.
	 */
	public function effectively_installed() {

		return $this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verified' );

	}

	/**
	 * Require 3.3.0 or later.
	 */
	static public function depends_on() {

		return [ '\phpbb\db\migration\data\v330\v330' ];

	}

	/**
	 * Run the 'up' function.
	 */
	public function update_schema() {

		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_verified'	=> [
						'UINT', 0
					]
				]
			]
		];

	}

	/**
	 * Run the 'down' function.
	 */
	public function revert_schema() {

		return [
			'drop_columns' => [
				$this->table_prefix . 'users' => [
					'user_verified'
				]
			]
		];

	}

}
