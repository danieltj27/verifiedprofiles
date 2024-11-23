<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\event;

use phpbb\auth\auth;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\language\language;
use phpbb\user;
use danieltj\verifiedprofiles\includes\functions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var functions
	 */
	protected $functions;

	/**
	 * Constructor
	 */
	public function __construct( auth $auth, request $request, template $template, language $language, user $user, functions $functions ) {

		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->language = $language;
		$this->user = $user;
		$this->functions = $functions;

	}

	/**
	 * Register Events
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'						=> 'add_languages',
			'core.permissions'						=> 'add_permissions',
			'core.modify_username_string'			=> 'update_username_string',
			'core.acp_users_modify_profile'			=> 'acp_modify_profile',
			'core.acp_users_profile_modify_sql_ary'	=> 'acp_user_sql_ary',
			'core.acp_manage_group_display_form'	=> 'add_group_verified_setting',
			'core.acp_manage_group_initialise_data'	=> 'initialise_group_verified_data',
			'core.acp_manage_group_request_data'	=> 'request_group_verified_data',
			'core.ucp_prefs_modify_common'			=> 'ucp_add_temp_vars',
			'core.ucp_prefs_personal_update_data'	=> 'ucp_update_user_sql',
		];

	}

	/**
	 * Add Languages
	 */
	public function add_languages( $event ) {

		$this->language->add_lang( [ 'acp', 'common', 'permissions', 'ucp' ], 'danieltj/verifiedprofiles' );

	}

	/**
	 * Add Permissions
	 */
	public function add_permissions( $event ) {

		$event->update_subarray( 'permissions', 'u_hide_verified_badge', [ 'lang' => 'ACL_U_HIDE_VERIFIED_BADGE', 'cat' => 'profile' ] );

	}

	/**
	 * includes/functions_content:get_username_string
	 */
	public function update_username_string( $event ) {

		$current_page = $this->user->page[ 'page_name' ];

		// Modes to ignore
		$bad_modes = [
			'colour', 'username', 'profile'
		];

		// Check if the current page can show verification.
		if ( $this->functions->is_location_enabled( $current_page ) ) {

			if ( $this->functions->is_user_verified( $event[ 'user_id' ] ) && false === $this->functions->is_badge_hidden( $event[ 'user_id' ] ) && ! in_array( $event[ 'mode' ], $bad_modes, true ) ) {

				$custom_badge = $this->functions->has_custom_badge( true );
				$custom_badge_html = '';

				if ( false !== $custom_badge ) {

					$custom_badge_html = ' style="background-image: url(' . $custom_badge . ');"';

				}

				$event[ 'username_string' ] .= ' <span class="vp-verified-badge"' . $custom_badge_html . ' aria-label="' . $this->language->lang( 'VERIFIED_PROFILE_ARIA_LABEL' ) . '" title="' . $this->language->lang( 'VERIFIED_PROFILE_ARIA_LABEL' ) . '">' . $this->language->lang( 'VERIFIED_PROFILE' ) . '</span>';

			}

		}

	}

	/**
	 * includes/acp/acp_users:main
	 */
	public function acp_modify_profile( $event ) {

		$verified = $this->request->variable( 'user_verified', (int) $event[ 'user_row' ][ 'user_verified' ] );

		$event[ 'data' ] = array_merge( $event[ 'data' ], [
			'user_verified' => $verified
		] );

		$this->template->assign_vars( [
			'S_USER_VERIFIED' => $verified
		] );

	}

	/**
	 * includes/acp/acp_users:main
	 */
	public function acp_user_sql_ary( $event ) {

		$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
			'user_verified' => $event[ 'data' ][ 'user_verified' ],
		] );

	}

	/**
	 * includes/acp/acp_groups:main
	 */
	public function add_group_verified_setting( $event ) {

		$this->template->assign_vars( [
			'S_GROUP_VERIFIED' => $this->functions->is_group_verified( $event[ 'group_id' ] )
		] );

	}

	/**
	 * includes/acp/acp_groups:main
	 */
	public function initialise_group_verified_data( $event ) {

		$event->update_subarray( 'test_variables', 'verified', 'int' );

	}

	/**
	 * includes/acp/acp_groups:main
	 */
	public function request_group_verified_data( $event ) {

		$event->update_subarray( 'submit_ary', 'verified', $this->request->variable( 'group_verified', 0 ) );

	}

	/**
	 * includes/ucp/ucp_prefs:main
	 */
	public function ucp_add_temp_vars( $event ) {

		$user_id = $this->user->data[ 'user_id' ];

		$verified = $this->functions->is_user_verified( $user_id );
		$hidden = $this->functions->is_badge_hidden( $user_id );

		$this->template->assign_vars( [
 			'S_USER_VERIFIED' => ( $verified && $this->auth->acl_get( 'u_hide_verified_badge' ) ),
 			'S_VERIFY_HIDE' => $hidden,
 		] );

	}

	/**
	 * includes/ucp/ucp_prefs:main
	 */
	public function ucp_update_user_sql( $event ) {

		$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
			'user_verify_visibility' => $this->request->variable( 'user_verify_visibility', 1 ),
		] );

	}

}
