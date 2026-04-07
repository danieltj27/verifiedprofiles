<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\acp;

class settings_module {

	/**
	 * @var $action;
	 */
	public $u_action;

	/**
	 * @var $tpl_name;
	 */
	public $tpl_name;

	/**
	 * @var $page_title;
	 */
	public $page_title;

	/**
	 * Extension module
	 */
	public function main( $id, $mode ) {

		global $phpbb_container, $phpbb_root_path;

		$config = $phpbb_container->get( 'config' );
		$language = $phpbb_container->get( 'language' );
		$log = $phpbb_container->get( 'log' );
		$request = $phpbb_container->get( 'request' );
		$template = $phpbb_container->get( 'template' );
		$user = $phpbb_container->get( 'user' );

		$this->tpl_name = 'verified_profile_settings';
		$this->page_title = $language->lang( 'ACP_VERIFIED_PROFILES' );

		add_form_key( 'verified_profiles_settings_csrf' );

		// Location to store a custom badge.
		$file_location = $phpbb_root_path . 'images';

		// File name of the custom badge (if one exists).
		$badge_file_name = $config[ 'verified_profiles_custom_badge' ];

		if ( $request->is_set_post( 'submit' ) ) {

			if ( ! check_form_key( 'verified_profiles_settings_csrf' ) ) {
			
				trigger_error( 'FORM_INVALID' );
			
			}

			// Delete the old badge before adding a new one.
			if ( true === $request->variable( 'verify_badge_delete', false ) ) {

				if ( '' !== $badge_file_name && file_exists( $file_location . '/' . $badge_file_name ) ) {

					unlink( $file_location . '/' . $badge_file_name );

					$config->set( 'verified_profiles_custom_badge', '' );

				}

			}

			$file_upload = $request->file( 'verify_badge_upload' );

			// Don't continue if nothing was uploaded.
			if ( '4' !== $file_upload[ 'error' ] ) {

				// Fetch admin defined max attachment size setting.
				$max_size = $config[ 'max_filesize' ];

				if ( '0' !== $file_upload[ 'error' ] || 1 > $file_upload[ 'size' ] || $max_size < $file_upload[ 'size' ] ) {

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_VERIFIED_PROFILES_LOG_ERROR_SIZE', time(), [] );

					trigger_error( $language->lang( 'ACP_VERIFICATION_BADGE_ERROR_SIZE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				$image_ext_num = exif_imagetype( $file_upload[ 'tmp_name' ] );

				$allowed_image_types = [ IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF ];

				// Check the image is an accepted file type.
				if ( ! in_array( $image_ext_num, $allowed_image_types, true ) ) {

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_VERIFIED_PROFILES_LOG_ERROR_TYPE', time(), [] );

					trigger_error( $language->lang( 'ACP_VERIFICATION_BADGE_ERROR_TYPE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				// Attempt to upload the image to the file system.
				if ( ! move_uploaded_file( $file_upload[ 'tmp_name' ], $file_location . '/' . $file_upload[ 'name' ] ) ) {

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_VERIFIED_PROFILES_LOG_ERROR_SAVE', time(), [] );

					trigger_error( $language->lang( 'ACP_VERIFICATION_BADGE_ERROR_SAVe' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				$config->set( 'verified_profiles_custom_badge', $file_upload[ 'name' ] );

				$badge_file_name = $file_upload[ 'name' ];

			}

			$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_VERIFIED_PROFILES_LOG_SAVED', time(), [] );

			trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_SAVED' ) . adm_back_link( $this->u_action ) );

		}

		$custom_badge_url = false;

		// Check for a custom verify badge.
		if ( '' !== $badge_file_name && file_exists( $file_location . '/' . $badge_file_name ) ) {

			$custom_badge_url = $file_location . '/' . $badge_file_name;

		}

		$template->assign_vars([
			'CUSTOM_BADGE_URL'	=> $custom_badge_url,
			'U_ACTION'			=> $this->u_action
		]);

	}

}
