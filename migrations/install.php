<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\migrations;

class install extends \phpbb\db\migration\migration {

	/**
	 * Check installation
	 */
	public function effectively_installed() {

		return ( $this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verified' ) && $this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verify_visibility' ) );

	}

	/**
	 * Require 3.3.x or later
	 */
	static public function depends_on() {

		return [ '\phpbb\db\migration\data\v33x\v331' ];

	}

	/**
	 * Install
	 */
	public function update_schema() {

		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_verified' => [
						'UINT:1', 0
					]
				],
				$this->table_prefix . 'users' => [
					'user_verify_visibility' => [
						'UINT:1', 1
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
				],
				$this->table_prefix . 'users' => [
					'user_verify_visibility'
				]
			]
		];

	}

}
