<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2023 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\migrations;

class install_v210 extends \phpbb\db\migration\migration {

	/**
	 * Check database changes have been made.
	 */
	public function effectively_installed() {

		return (
			$this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verify_visibility' ) &&
			$this->db_tools->sql_column_exists( $this->table_prefix . 'groups', 'group_verified' )
		);

	}

	/**
	 * Depends on install_install migration.
	 */
	static public function depends_on() {

		return [ '\danieltj\verifiedprofiles\migrations\initial_install' ];

	}

	/**
	 * Run the 'up' function.
	 */
	public function update_schema() {

		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_verify_visibility' => [
						'UINT:1', 1
					],
				],
				$this->table_prefix . 'groups' => [
					'group_verified' => [
						'UINT:1', 0
					]
				],
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
					'user_verify_visibility'
				],
				$this->table_prefix . 'groups' => [
					'group_verified'
				],
			]
		];

	}

	/**
	 * Update forum data.
	 */
	public function update_data() {

		return [
			[ 'permission.add', [ 'u_hide_verified_badge' ] ],
			[ 'if', [
				[ 'permission.role_exists', [ 'ROLE_USER_FULL' ] ],
				[ 'permission.permission_set', [ 'ROLE_USER_FULL', 'u_hide_verified_badge' ] ],
			] ],
		];

	}

}
