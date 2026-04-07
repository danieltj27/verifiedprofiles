<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\event;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\language\language;
use phpbb\notification\manager as notifications;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use danieltj\verifiedprofiles\includes\functions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var database
	 */
	protected $database;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var notifications
	 */
	protected $notifications;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

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
	public function __construct( auth $auth, database $database, language $language, notifications $notifications, request $request, template $template, user $user, functions $functions ) {

		$this->auth = $auth;
		$this->database = $database;
		$this->language = $language;
		$this->notifications = $notifications;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->functions = $functions;

	}

	/**
	 * Register Events
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup_after'						=> 'add_languages',
			'core.permissions'							=> 'add_permissions',
			'core.modify_username_string'				=> 'update_username_string',
			'core.acp_users_modify_profile'				=> 'acp_update_profile_data',
			'core.acp_users_profile_modify_sql_ary'		=> 'acp_update_sql_data',
			'core.acp_manage_group_display_form'		=> 'add_group_verified_setting',
			'core.acp_manage_group_initialise_data'		=> 'initialise_group_verified_data',
			'core.acp_manage_group_request_data'		=> 'request_group_verified_data',
			'core.group_add_user_after'					=> 'verify_group_member',
			'core.ucp_prefs_modify_common'				=> 'ucp_add_template_vars',
			'core.ucp_prefs_personal_update_data'		=> 'ucp_update_user_sql',
			'core.memberlist_view_profile'				=> 'add_profile_template_vars',
		];

	}

	/**
	 * Add Languages
	 */
	public function add_languages( $event ) {

		$this->language->add_lang( [
			'acp', 'common', 'notifications', 'permissions', 'ucp'
		], 'danieltj/verifiedprofiles' );

	}

	/**
	 * Add Permissions
	 */
	public function add_permissions( $event ) {

		$event->update_subarray( 'permissions', 'u_hide_verified_badge', [
			'lang' => 'ACL_U_HIDE_VERIFIED_BADGE',
			'cat' => 'profile'
		] );

	}

	/**
	 * includes/functions_content:get_username_string
	 */
	public function update_username_string( $event ) {

		$user_id = (int) $event[ 'user_id' ];

		if (
			$this->functions->is_user_verified( $user_id ) &&
			false === $this->functions->is_badge_hidden( $user_id ) &&
			! in_array( $event[ 'mode' ], [ 'colour', 'username', 'profile' ], true )
		) {

			$custom_badge = $this->functions->has_custom_badge();
			$custom_badge_html = '';

			if ( false !== $custom_badge ) {

				$custom_badge_html = ' style="background-image: url(' . $custom_badge . ');"';

			}

			$event[ 'username_string' ] .= ' <span class="vp-verified-badge"' . $custom_badge_html . ' aria-label="' . $this->language->lang( 'VERIFIED_PROFILE_ARIA_LABEL' ) . '" title="' . $this->language->lang( 'VERIFIED_PROFILE_ARIA_LABEL' ) . '">' . $this->language->lang( 'VERIFIED_PROFILE' ) . '</span>';

		}

	}

	/**
	 * includes/acp/acp_users:main
	 */
	public function acp_update_profile_data( $event ) {

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
	public function acp_update_sql_data( $event ) {

		$verified = ( 1 === (int) $event[ 'data' ][ 'user_verified' ] ) ? 1 : 0;

		$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
			'user_verified' => $verified,
		] );

		if ( 1 === $verified ) {

			// Send a new verified notification.
			$this->notifications->add_notifications( 'danieltj.verifiedprofiles.notification.type.verified', [
				'item_id'	=> $this->functions->create_notification_item_id(),
				'user_id'	=> $event[ 'data' ][ 'user_id' ],
			] );

		}

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
	 * includes/functions_user:group_user_add
	 */
	public function verify_group_member( $event ) {

		if ( $this->functions->is_group_verified( $event[ 'group_id' ] ) && 1 !== (int) $event[ 'pending' ] ) {

			if ( is_array( $event[ 'user_id_ary' ] ) && ! empty( $event[ 'user_id_ary' ] ) ) {

				foreach ( $event[ 'user_id_ary' ] as $user_id ) {

					$this->database->sql_query( 'UPDATE ' . USERS_TABLE . ' SET ' . $this->database->sql_build_array( 'UPDATE', [
						'user_verified'	=> 1,
					] ) . ' WHERE ' . $this->database->sql_build_array( 'UPDATE', [
						'user_id'		=> $user_id,
					] ) );

					// Trigger new 'verified' notification event.
					$this->notifications->add_notifications( 'danieltj.verifiedprofiles.notification.type.verified', [
						'item_id'	=> $this->functions->create_notification_item_id(),
						'user_id'	=> $user_id,
					] );

				}

			}

		}

	}

	/**
	 * includes/ucp/ucp_prefs:main
	 */
	public function ucp_add_template_vars( $event ) {

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

	/**
	 * memberlist
	 */
	public function add_profile_template_vars( $event ) {

		$user_id = (int) $event[ 'member' ][ 'user_id' ];

		if ( $this->functions->is_user_verified( $user_id ) && false === $this->functions->is_badge_hidden( $user_id ) ) {

			$custom_badge = $this->functions->has_custom_badge();

			$this->template->assign_vars( [
	 			'S_USER_VERIFIED' => true,
	 			'U_CUSTOM_BADGE' => ( false !== $custom_badge ) ? 'style="background-image: url(' . $custom_badge . ');"' : ''
	 		] );

		}

	}

}
