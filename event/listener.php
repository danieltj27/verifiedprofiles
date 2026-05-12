<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\event;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\event\dispatcher_interface as dispatcher;
use phpbb\language\language;
use phpbb\notification\manager as notifications;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use danieltj\verifiedprofiles\includes\functions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var phpbb\auth\auth
	 */
	protected $auth;

	/**
	 * @var phpbb\db\driver\driver_interface
	 */
	protected $database;

	/**
	 * @var phpbb\event\dispatcher_interface
	 */
	protected $dispatcher;

	/**
	 * @var phpbb\language\language
	 */
	protected $language;

	/**
	 * @var phpbb\notification\manager
	 */
	protected $notifications;

	/**
	 * @var phpbb\request\request
	 */
	protected $request;

	/**
	 * @var phpbb\template\template
	 */
	protected $template;

	/**
	 * @var phpbb\user
	 */
	protected $user;

	/**
	 * @var danieltj\verifiedprofiles\includes\functions
	 */
	protected $functions;

	/**
	 * Constructor
	 */
	public function __construct( auth $auth, database $database, dispatcher $dispatcher, language $language, notifications $notifications, request $request, template $template, user $user, functions $functions ) {

		$this->auth = $auth;
		$this->database = $database;
		$this->dispatcher = $dispatcher;
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
			'core.acp_manage_group_display_form'		=> 'add_group_verified_settings',
			'core.acp_manage_group_initialise_data'		=> 'initialise_group_verified_data',
			'core.acp_manage_group_request_data'		=> 'request_group_verified_data',
			'core.group_add_user_after'					=> 'verify_group_member',
			'core.user_set_group_attributes'			=> 'verify_accepted_group_member',
			'core.ucp_prefs_modify_common'				=> 'ucp_add_template_vars',
			'core.ucp_prefs_personal_update_data'		=> 'ucp_update_user_sql',
			'core.ucp_profile_reg_details_data'			=> 'ucp_reg_details_add_tpl_vars',
			'core.ucp_profile_reg_details_validate'		=> 'ucp_reg_details_verify_update',
			'core.memberlist_view_profile'				=> 'add_profile_template_vars',
		];

	}

	/**
	 * phpbb/user:setup
	 */
	public function add_languages( $event ) {

		$this->language->add_lang( [
			'acp', 'common', 'notifications', 'permissions', 'ucp'
		], 'danieltj/verifiedprofiles' );

		// Create new variable inside \phpbb\user object.
		$this->user->_verified_cache = [];

	}

	/**
	 * phpbb/permissions:__construct
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
		$user_verified = false;

		if ( isset( $this->user->_verified_cache[ $event[ 'user_id' ] ] ) ) {

			$user_verified = $this->user->_verified_cache[ $event[ 'user_id' ] ];

		} else {

			$user_verified = $this->user->_verified_cache[ $user_id ] = $this->functions->is_user_verified( $user_id );

		}

		/**
		 * Handle the user verification status before the badge is displayed.
		 * 
		 * @event danieltj.verifiedprofiles.update_username_string
		 * @since 3.0.0-b3
		 * @since 3.0.0-b6 Updated event name to something more descriptive.
		 * 
		 * @var integer $user_id       The user ID being checked for verification.
		 * @var boolean $user_verified True if the user is verified, false if they are not
		 *                             verified *or* they are hiding their badge.
		 */
		$vars = [ 'user_id', 'user_verified' ];
		extract( $this->dispatcher->trigger_event( 'danieltj.verifiedprofiles.update_username_string', compact( $vars ) ) );

		if ( $user_verified && ! in_array( $event[ 'mode' ], [ 'colour', 'username', 'profile' ], true ) ) {

			$custom_badge_path = $this->functions->has_custom_badge();
			$custom_badge_html = '';

			if ( false !== $custom_badge_path ) {

				$custom_badge_html = ' style="background-image: url(' . $custom_badge_path . ');"';

			}

			$event[ 'username_string' ] .= ' <span class="vp-verified-badge"' . $custom_badge_html . ' aria-label="' . $this->language->lang( 'VERIFIED_PROFILE_LABEL' ) . '" title="' . $this->language->lang( 'VERIFIED_PROFILE_LABEL' ) . '">' . $this->language->lang( 'VERIFIED_PROFILE' ) . '</span>';

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

		/**
		 * Only send this notification if the user is not already verified because
		 * otherwise they'll get notified every single time an admin updates their
		 * profile which we don't want.
		 */
		if ( ! $this->functions->is_user_verified( $event[ 'user_id' ] ) && 1 === $verified ) {

			// Send a new verified notification.
			$this->notifications->add_notifications( 'danieltj.verifiedprofiles.notification.type.verified', [
				'item_id'	=> $this->functions->create_notification_item_id(),
				'user_id'	=> $event[ 'user_id' ],
			] );

		}

	}

	/**
	 * includes/acp/acp_groups:main
	 */
	public function add_group_verified_settings( $event ) {

		/**
		 * Only verify the members of this group if the correct button was
		 * pressed and this group has verification enabled. Otherwise don't.
		 */
		if (
			$this->language->lang( 'ACP_VERIFY_EXISTING_GROUP_MEMBERS_BUTTON' ) === $this->request->variable( 'verify_existing_group_members', '0' ) &&
			1 === (int) $event[ 'group_row' ][ 'group_verified' ]
		) {

			// Fetch existing members (ignore pending membership).
			$result = $this->database->sql_query( 'SELECT * FROM ' . USER_GROUP_TABLE . ' WHERE ' .
				$this->database->sql_build_array( 'SELECT', [
					'group_id'		=> (int) $event[ 'group_id' ],
					'user_pending'	=> 0,
				] )
			);

			$group_members = $this->database->sql_fetchrowset( $result );
			
			$this->database->sql_freeresult( $result );

			if ( ! empty( $group_members ) ) {

				foreach ( $group_members as $user ) {

					$this->functions->verify_user( $user[ 'user_id' ] );

				}

			}

			trigger_error( $this->language->lang( 'ACP_VERIFIED_GROUP_MEMBERS_SUCCESS' ) . adm_back_link(
				append_sid( './index.php', [
					'i'			=> 'acp_groups',
					'icat'		=> $this->request->variable( 'icat', '0' ),
					'mode'		=> 'manage',
					'action'	=> 'edit',
					'g'			=> $event[ 'group_id' ],
				] )
			) );

		}

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

					$this->functions->verify_user( $user_id );

				}

			}

		}

	}

	/**
	 * includes/functions_user:group_user_attributes
	 */
	public function verify_accepted_group_member( $event ) {

		if ( 'approve' === $event[ 'action' ] && $this->functions->is_group_verified( $event[ 'group_id' ] ) && ! empty( $event[ 'user_id_ary' ] ) ) {

			foreach ( $event[ 'user_id_ary' ] as $user_id ) {

				$this->functions->verify_user( $user_id );

			}

		}

	}

	/**
	 * includes/ucp/ucp_prefs:main
	 */
	public function ucp_add_template_vars( $event ) {

		$user_id = $this->user->data[ 'user_id' ];

		$this->template->assign_vars( [
 			'S_VERIFIED_USER'	=> ( 1 === (int) $this->user->data[ 'user_verified' ] && $this->auth->acl_get( 'u_hide_verified_badge' ) ) ? true : false,
 			'S_VERIFIED_HIDDEN'	=> ( 1 === (int) $this->user->data[ 'user_verify_visibility' ] ) ? true : false,
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
	 * includes/ucp/ucp_profile:main
	 */
	public function ucp_reg_details_add_tpl_vars( $event ) {

		if ( $this->functions->is_user_verified( $this->user->data[ 'user_id' ] ) ) {

			$this->template->assign_vars( [
	 			'S_WARN_VERIFIED_CHANGES' => ( $this->functions->require_user_verify_after_update() ) ? true : false,
	 		] );

		}

	}

	/**
	 * includes/ucp/ucp_profile:main
	 */
	public function ucp_reg_details_verify_update( $event ) {

		if ( ! count( $event[ 'error' ] ) && $this->functions->require_user_verify_after_update() ) {

			if ( ( $this->user->data[ 'user_email' ] !== $event[ 'data' ][ 'email' ] ) || ( $this->user->data[ 'username' ] !== $event[ 'data' ][ 'username' ] ) ) {

				$this->database->sql_query( 'UPDATE ' . USERS_TABLE . ' SET ' . $this->database->sql_build_array( 'UPDATE', [
					'user_verified'	=> 0,
				] ) . ' WHERE ' . $this->database->sql_build_array( 'UPDATE', [
					'user_id'		=> (int) $this->user->data[ 'user_id' ],
				] ) );

			}

		}

	}

	/**
	 * memberlist
	 */
	public function add_profile_template_vars( $event ) {

		$custom_badge = $this->functions->has_custom_badge();

		$this->template->assign_vars( [
 			'S_USER_VERIFIED' => $this->functions->is_user_verified( $event[ 'member' ][ 'user_id' ] ),
 			'U_CUSTOM_BADGE' => ( false !== $custom_badge ) ? 'style="background-image: url(' . $custom_badge . ');"' : ''
 		] );

	}

}
