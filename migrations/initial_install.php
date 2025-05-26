<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2025 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\migrations;

class initial_install extends \phpbb\db\migration\migration {

	/**
	 * Check installation status.
	 */
	public function effectively_installed() {

		return $this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verified' );

	}

	/**
	 * Requires phpBB 3.3 migration.
	 */
	static public function depends_on() {

		return [ '\phpbb\db\migration\data\v330\v330' ];

	}

	/**
	 * Install
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
	 * Uninstall
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
