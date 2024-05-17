<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\event;

use phpbb\request\request;
use phpbb\template\template;
use danieltj\verifiedprofiles\includes\functions;

class listener {

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var functions
	 */
	protected $functions;

	/**
	 * Constructor.
	 */
	public function __construct( request $request, template $template, functions $functions ) {

		$this->request = $request;
		$this->template = $template;
		$this->functions = $functions;

	}

	/**
	 * Register events
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'								=> 'extension_init',
			'core.page_header_after'						=> 'add_template_vars',
			'core.acp_users_modify_profile'					=> 'acp_modify_profile',
			'core.acp_users_profile_modify_sql_ary'			=> 'acp_user_sql_ary',
			'core.display_forums_modify_template_vars'		=> 'forumlist_template_vars',
			'core.viewtopic_modify_post_row'				=> 'viewtopic_add_template_vars',
			'core.memberlist_prepare_profile_data'			=> 'memberlist_prepare_profile_data',
			'core.memberlist_view_profile'					=> 'memberlist_profile_template_vars',
			'core.memberlist_team_modify_template_vars'		=> 'memberlist_team_template_vars',
			'core.ucp_pm_view_messsage'						=> 'ucp_message_template_vars',
			'core.viewforum_modify_topicrow'				=> 'viewforum_topics_modify_vars',
			'core.viewonline_modify_user_row'				=> 'viewonline_modify_template_vars'
		];

	}

	/**
	 * Start the extension
	 */
	public function extension_init( $event ) {

		// Add language file
		$this->language->add_lang( [ 'common', 'permissions' ], 'danieltj/verifiedprofiles' );

	}

	/**
	 * Add verified template variable
	 */
	public function add_template_vars( $event ) {

		global $user;

		if ( isset( $user->data[ 'user_id' ] ) ) {

			$verified = $this->functions->is_user_verified( $user->data[ 'user_id' ] );

			$this->template->assign_vars( [
				'U_CURRENT_USER_VERIFIED' => $verified
			] );

		}

	}

	/**
	 * Modify user data.
	 */
	public function acp_modify_profile( $event ) {

		$verified = $this->request->variable( 'user_verified', (int) $event[ 'user_row' ][ 'user_verified' ] );

		$event[ 'data' ] = array_merge( $event[ 'data' ], [
			'user_verified' => $verified
		] );

		$this->template->assign_vars( [
			'USER_VERIFIED' => $verified
		] );

	}

	/**
	 * SQL stuff, apparently.
	 */
	public function acp_user_sql_ary( $event ) {

		$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
			'user_verified' => $event[ 'data' ][ 'user_verified' ],
		] );

	}

	/**
	 * Add forum list template vars.
	 */
	public function forumlist_template_vars( $event ) {

		$verified = $this->functions->is_user_verified( $event[ 'forum_row' ][ 'LAST_POSTER' ], false );

		$event[ 'forum_row' ] = array_merge( $event[ 'forum_row' ], [
			'LAST_POST_USER_VERIFIED' => $verified
		] );

	}

	/**
	 * Add view topic template variable.
	 */
	public function viewtopic_add_template_vars( $event ) {

		$user_id = $event[ 'poster_id' ];

		$verified = $this->functions->is_user_verified( $user_id );

		$event[ 'post_row' ] = array_merge( $event[ 'post_row' ], [
			'USER_VERIFIED' => $verified
		] );

	}

	/**
	 * Add memberlist template variable.
	 */
	public function memberlist_prepare_profile_data( $event ) {

		$user_name = $event[ 'template_data' ][ 'USERNAME' ];

		$verified = $this->functions->is_user_verified( $user_name, false );

		$event[ 'template_data' ] = array_merge( $event[ 'template_data' ], [
			'USER_VERIFIED' => $verified
		] );

	}

	/**
	 * Add profile template variable.
	 */
	public function memberlist_profile_template_vars( $event ) {

		$user_id = $event[ 'member' ][ 'user_id' ];

		$verified = $this->functions->is_user_verified( $user_id );

		$this->template->assign_vars( [
 			'USER_VERIFIED' => $verified
 		] );

	}

	/**
	 * Add the team template variable.
	 */
	public function memberlist_team_template_vars( $event ) {

		$user_id = $event[ 'template_vars' ][ 'USER_ID' ];

		$verified = $this->functions->is_user_verified( $user_id );

		$event[ 'template_vars' ] = array_merge( $event[ 'template_vars' ], [
			'USER_VERIFIED' => $verified
		] );

	}

	/**
	 * Add UCP PM template variable.
	 */
	public function ucp_message_template_vars( $event ) {

		$user_id = $event[ 'user_info' ][ 'user_id' ];

		$verified = $this->functions->is_user_verified( $user_id );

		$event[ 'msg_data' ] = array_merge( $event[ 'msg_data' ], [
 			'USER_VERIFIED' => $verified
 		] );

	}

	/**
	 * Add view forum template variable.
	 */
	public function viewforum_topics_modify_vars( $event ) {

		$topic_id = $event[ 'topic_row' ][ 'TOPIC_ID' ];

		$author_verified = $this->functions->is_user_verified( $this->functions->get_topic_author_id( $topic_id ) );
		$last_poster_verified = $this->functions->is_user_verified( $this->functions->get_topic_last_poster_id( $topic_id ) );

		$event[ 'topic_row' ] = array_merge( $event[ 'topic_row' ], [
			'AUTHOR_USER_VERIFIED' => $author_verified,
			'LAST_POST_USER_VERIFIED' => $last_poster_verified
  		] );

	}

	/**
	 * Add view online template variable.
	 */
	public function viewonline_modify_template_vars( $event ) {

		$user_id = $this->functions->get_user_id_by_username( $event[ 'template_row' ][ 'USERNAME' ] );

		$verified = $this->functions->is_user_verified( $user_id );

		$event[ 'template_row' ] = array_merge( $event[ 'template_row' ], [
			'USER_VERIFIED' => $verified,
  		] );

	}

}
