<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2024 Daniel James
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
		$this->page_title = $language->lang( 'ACP_VERIFIED_PROFILES_MODULE_TITLE' );

		add_form_key( 'verified_profiles_settings_csrf' );

		// The location of where the file will get stored.
		$file_location = $phpbb_root_path . 'images';

		// The file name of the custom badge (if one is uploaded).
		$badge_file_name = $config[ 'verified_profiles_custom_badge' ];

		/**
		 * Create an array of possible badge locations based on the
		 * forum file structure and include the language string and
		 * whether that location has been enabled (to display the badge).
		 */
		$available_locations = [
			'app'			=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_APP', 'checked' => false ],
			'index'			=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_INDEX', 'checked' => false ],
			'mcp'			=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_MCP', 'checked' => false ],
			'memberlist'	=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_MEMBERLIST', 'checked' => false ],
			'posting'		=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_POSTING', 'checked' => false ],
			'report'		=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_REPORT', 'checked' => false ],
			'search'		=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_SEARCH', 'checked' => false ],
			'ucp'			=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_UCP', 'checked' => false ],
			'viewforum'		=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWFORUM', 'checked' => false ],
			'viewonline'	=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWONLINE', 'checked' => false ],
			'viewtopic'		=> [ 'language' => 'ACP_SETTINGS_LOCATION_MANAGE_VALUE_VIEWTOPIC', 'checked' => false ]
		];

		// Fetch current set locations to compare against the full list.
		$set_locations = json_decode( $config[ 'verified_profiles_badge_locations' ] );

		if ( NULL != $set_locations ) {

			foreach ( $available_locations as $key => $value ) {

				if ( in_array( $key, $set_locations, true ) ) {

					$available_locations[ $key ][ 'checked' ] = true;

				}

			}

		}

		if ( $request->is_set_post( 'submit' ) ) {

			if ( ! check_form_key( 'verified_profiles_settings_csrf' ) ) {
			
				trigger_error( 'FORM_INVALID' );
			
			}

			// Delete the existing badge before trying to add another.
			$delete_badge = $request->variable( 'custom_badge_delete', false );

			if ( true === $delete_badge ) {

				if ( '' !== $badge_file_name && file_exists( $file_location . '/' . $badge_file_name ) ) {

					unlink( $file_location . '/' . $badge_file_name );

					$config->set( 'verified_profiles_custom_badge', '' );

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

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_LOGS_BADGE_SIZE_ERROR', time(), [] );

					trigger_error( $language->lang( 'ACP_SETTINGS_ERROR_BADGE_SIZE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				// Get the file type of the uploaded image to compare.
				$image_ext_num = exif_imagetype( $file_upload[ 'tmp_name' ] );

				$allowed_image_types = [ IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF ];

				// Is the image using an acceptable file type?
				if ( ! in_array( $image_ext_num, $allowed_image_types, true ) ) {

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_LOGS_BADGE_TYPE_ERROR', time(), [] );

					trigger_error( $language->lang( 'ACP_SETTINGS_ERROR_BADGE_TYPE' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				// Try and upload the image to the file system.
				if ( ! move_uploaded_file( $file_upload[ 'tmp_name' ], $file_location . '/' . $file_upload[ 'name' ] ) ) {

					$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_LOGS_BADGE_UPLOAD_ERROR', time(), [] );

					trigger_error( $language->lang( 'ACP_SETTINGS_ERROR_BADGE_UPLOAD' ) . adm_back_link( $this->u_action ), E_USER_WARNING );

				}

				$config->set( 'verified_profiles_custom_badge', $file_upload[ 'name' ] );
				$badge_file_name = $file_upload[ 'name' ];

			}

			// Empty array used for saving set locations.
			$enabled_locations = [];

			/**
			 * Loop through each available location and see if it has been
			 * checked to enable verification badges on it. If so, add it to an
			 * array that we save to the database.
			 */
			foreach ( $available_locations as $key => $value ) {

				$input = $request->variable( 'verify_location_' . $key, 'off' );

				if ( 'on' === $input ) {

					$enabled_locations[] = $key;

				}

			}

			$enabled_locations = json_encode( $enabled_locations );

			$config->set( 'verified_profiles_badge_locations', $enabled_locations );

			$log->add( 'admin', $user->data[ 'user_id' ], $user->data[ 'user_ip' ], 'ACP_LOGS_SETTINGS_SAVED', time(), [] );

			trigger_error( $language->lang( 'ACP_SETTINGS_SUCCESS_MESSAGE' ) . adm_back_link( $this->u_action ) );

		}

		$custom_badge_url = false;

		// Check if there is a custom badge uploaded.
		if ( '' !== $badge_file_name && file_exists( $file_location . '/' . $badge_file_name ) ) {

			$custom_badge_url = $file_location . '/' . $badge_file_name;

		}

		$template->assign_vars([
			'CUSTOM_BADGE_URL'	=> $custom_badge_url,
			'LOCATIONS'			=> $available_locations,
			'U_ACTION'			=> $this->u_action
		]);

	}

}
