<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\verifiedprofiles\acp;

class extension_module {

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

		global $phpbb_root_path, $language, $template, $request, $config;

		$this->tpl_name = 'verified_profile_settings';
		$this->page_title = $language->lang( 'ACP_VERIFIED_PROFILE_SETTINGS' );

		add_form_key( 'verified_profile_settings' );

		if ( $request->is_set_post( 'submit' ) ) {

			if ( ! check_form_key( 'verified_profile_settings' ) ) {
			
				trigger_error( 'FORM_INVALID' );
			
			}

			// The location of where the file will get stored.
			$file_location = $phpbb_root_path . 'images';

			/**
			 * Delete the existing custom verified badge before we
			 * try adding another.
			 */
			$delete_badge = $request->variable( 'custom_badge_delete', false );

			if ( true === $delete_badge ) {

				$badge_file_name = $config[ 'custom_verified_profiles_badge' ];

				if ( '' !== $badge_file_name && file_exists( $file_location . '/' . $badge_file_name ) ) {

					unlink( $file_location . '/' . $badge_file_name );

				}

			}

			// Get the uploaded file data.
			$file_upload = $request->file( 'custom_badge_upload' );

			// Don't try and upload an image if nothing was uploaded.
			if ( '4' !== $file_upload[ 'error' ] ) {

				/**
				 * Fetch the max file size set in the attachment settings
				 * screen and check the file is within the correct range.
				 */
				$max_size = $config[ 'max_filesize' ];

				if ( '0' !== $file_upload[ 'error' ] || 1 > $file_upload[ 'size' ] || $max_size < $file_upload[ 'size' ] ) {

					trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_BAD_IMAGE_SIZE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				/**
				 * Get the file type of the uploaded image and check that
				 * it's one of the allowed file types.
				 */
				$image_ext_num = exif_imagetype( $file_upload[ 'tmp_name' ] );

				/**
				 * Requires php 8 support!
				 */
				$image_ext_type = match( $image_ext_num ) {
					3 => 'png',
					2 => 'jpg',
					1 => 'gif'
				};

				$allowed_image_types = [
					IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF
				];

				if ( ! in_array( $image_ext_num, $allowed_image_types, true ) ) {

					trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_BAD_IMAGE_TYPE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				if ( ! move_uploaded_file( $file_upload[ 'tmp_name' ], $file_location . '/' . $file_upload[ 'name' ] ) ) {

					$config->set( 'custom_verified_profiles_badge', $file_upload[ 'name' ] );

					trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_BAD_IMAGE_UPLOAD' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

			}

			trigger_error( $language->lang( 'ACP_VERIFICATION_SETTINGS_SAVED' ) . adm_back_link( $this->u_action ) );

		}

		$template->assign_vars([
			'U_ACTION' => $this->u_action,
		]);

	}

}
