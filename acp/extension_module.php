<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\acp;

class extension_module {

	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main( $id, $mode ) {

		global $language, $template, $request, $config;

		$this->tpl_name = 'verified_profile_settings';
		$this->page_title = $language->lang( 'ACP_VERIFIED_PROFILE_SETTINGS' );

		add_form_key( 'verified_profile_settings' );

		if ( $request->is_set_post( 'submit' ) ) {

			if ( ! check_form_key( 'verified_profile_settings' ) ) {
			
				trigger_error( 'FORM_INVALID' );
			
			}

			//die( 'form submitted' );

			trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_SAVED' ) . adm_back_link( $this->u_action ) );

		}

		$template->assign_vars([
			'U_ACTION' => $this->u_action,
		]);

	}

}
