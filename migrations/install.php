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

		return (
			$this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verified' ) &&
			$this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_verify_visibility' ) &&
			$this->db_tools->sql_column_exists( $this->table_prefix . 'groups', 'group_verified' )
		);

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
					],
					'user_verify_visibility' => [
						'UINT:1', 1
					],
				],
				$this->table_prefix . 'groups' => [
					'group_verified' => [
						'UINT:1', 0
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
					'user_verified',
					'user_verify_visibility'
				],
				$this->table_prefix . 'groups' => [
					'group_verified'
				]
			]
		];

	}

	/**
	 * Add the new permission
	 */
	public function update_data() {

		return [
			[ 'config.add', [ 'verified_profiles_custom_badge_path', '' ] ],

			[ 'module.add', [ 'acp', 'ACP_CAT_DOT_MODS', 'ACP_VERIFIED_PROFILES' ], ],
			[ 'module.add', [ 'acp', 'ACP_VERIFIED_PROFILES', [ 'module_basename' => '\danieltj\verifiedprofiles\acp\extension_module', 'modes' => [ 'settings' ] ] ] ],

			[ 'permission.add', [ 'u_hide_verified_badge' ] ],
			[ 'if', [
				[ 'permission.role_exists', [ 'ROLE_USER_FULL' ] ],
				[ 'permission.permission_set', [ 'ROLE_USER_FULL', 'u_hide_verified_badge' ] ],
			] ]
		];

	}

}
