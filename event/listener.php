<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\event;

use phpbb\request\request;
use phpbb\template\template;
use phpbb\language\language;
use danieltj\verifiedprofiles\includes\functions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

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
	 * @var functions
	 */
	protected $functions;

	/**
	 * Constructor
	 */
	public function __construct( request $request, template $template, language $language, functions $functions ) {

		$this->request = $request;
		$this->template = $template;
		$this->language = $language;
		$this->functions = $functions;

	}

	/**
	 * Register events
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'								=> 'add_language_files',
			'core.modify_username_string'					=> 'update_username_string',
			'core.acp_users_modify_profile'					=> 'acp_modify_profile',
			'core.acp_users_profile_modify_sql_ary'			=> 'acp_user_sql_ary',
		];

	}

	/**
	 * Add extension languages
	 */
	public function add_language_files( $event ) {

		$this->language->add_lang( [ 'common', 'permissions' ], 'danieltj/verifiedprofiles' );

	}

	/**
	 * Update username string
	 */
	public function update_username_string( $event ) {

		// Modes to ignore
		$bad_modes = [
			'username', 'profile'
		];

		if ( $this->functions->is_user_verified( $event[ 'user_id' ] ) ) {

			if ( ! in_array( $event[ 'mode' ], $bad_modes, true ) ) {

				$event[ 'username_string' ] .= ' <span class="vp-verified-badge" title="' . $this->language->lang( 'VERIFIED_BADGE_TITLE' ) . '">' . $this->language->lang( 'VERIFIED' ) . '</span>';

			}

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

}
