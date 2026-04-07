<?php

/**
 * @package Verified Profiles
 * @copyright (c) 2026 Daniel James
 * @license https://opensource.org/license/gpl-2-0
 */

namespace danieltj\verifiedprofiles\notification\type;

class verified extends \phpbb\notification\type\base {

	/**
	 * @var user_loader
	 */
	protected $user_loader;

	/**
	 * @var functions
	 */
	protected $functions;

	/**
	 * Notification options data.
	 * 
	 * @var array $notification_option An array of notification data.
	 */
	static public $notification_option = [
		'group'		=> 'NOTIFICATION_GROUP_MISCELLANEOUS',
		'lang'		=> 'VERIFIED_PROFILES_NOTIFICATIONS_SAMPLE',
	];

	/**
	 * Set the user loader object.
	 * 
	 * @param \phpbb\user_loader $user_loader The user_loader object.
	 * 
	 * @return void
	 */
	public function set_user_loader( \phpbb\user_loader $user_loader ) {

		$this->user_loader = $user_loader;

	}

	/**
	 * Set the extension functions object.
	 * 
	 * @param \danieltj\verifiedprofiles\includes\functions $functions The functions object.
	 * 
	 * @return void
	 */
	public function set_functions( \danieltj\verifiedprofiles\includes\functions $functions ) {

		$this->functions = $functions;

	}

	/**
	 * Returns the type of notification.
	 * 
	 * @return string  The notification type.
	 */
	public function get_type() {

		return 'danieltj.verifiedprofiles.notification.type.verified';

	}

	/**
	 * Returns a boolean value checking if the user can access this notification.
	 * 
	 * @return boolean  Always returns true.
	 */
	public function is_available() {

		return true;

	}

	/**
	 * Return the notification item ID.
	 * 
	 * @param array $data The item data passed to the notification handler.
	 * 
	 * @return integer  The notification item ID.
	 */
	public static function get_item_id( $data ) {

		return $data[ 'item_id' ];

	}

	/**
	 * Return the ID of the parent.
	 * 
	 * @param array $data The item data passed to the notification handler.
	 * 
	 * @return integer  Always returns 0.
	 */
	public static function get_item_parent_id( $data ) {

		return 0;

	}

	/**
	 * Returns a collection of user IDs that want this notification.
	 * 
	 * @param array $data    An array containing notification data.
	 * @param array $options An array of options to filter users.
	 * 
	 * @return $user_methods An array containing notification methods for each user.
	 */
	public function find_users_for_notification( $data, $options = [] ) {

		$options = array_merge( [
			'ignore_users' => [ ANONYMOUS ]
		], $options );

		$user_methods = $this->check_user_notification_options( [ $data[ 'user_id' ] ], $options );

		return $user_methods;

	}

	/**
	 * Return an array of users for this notification.
	 * 
	 * @return array  An array containing user IDs.
	 */
	public function users_to_query() {

		return [ $this->get_data( 'user_id' ) ];

	}

	/**
	 * Return the avatar of the user.
	 * 
	 * @return string  HTML string to display the user avatar.
	 */
	public function get_avatar() {

		return $this->user_loader->get_avatar( $this->get_data( 'user_id' ), true, true );

	}

	/**
	 * Return the title of the notification.
	 * 
	 * @return string  The title for this notification.
	 */
	public function get_title() {

		return $this->language->lang( 'VERIFIED_PROFILES_NOTIFICATIONS_VERIFIED_TITLE' );

	}

	/**
	 * Return the reference of the notification.
	 * 
	 * @return string  The reference for this notification.
	 */
	public function get_reference() {

		return $this->language->lang( 'VERIFIED_PROFILES_NOTIFICATIONS_VERIFIED_REASON' );

	}

	/**
	 * Return the URL of the notification.
	 * 
	 * @return string  The URL for this notification.
	 */
	public function get_url() {

		return append_sid( $this->phpbb_root_path . 'memberlist.' . $this->php_ext, [
			'mode'	=> 'viewprofile',
			'u'		=> $this->get_data( 'user_id' )
		] );

	}

	/**
	 * Return the email template.
	 * 
	 * @return string  The notification template file location.
	 */
	public function get_email_template() {

		return '@danieltj_verifiedprofiles/verified';

	}

	/**
	 * Return the template variables for the email.
	 * 
	 * @return array  An array of template variables for the email.
	 */
	public function get_email_template_variables() {

		return [];

	}

	/**
	 * Prepare notification the data.
	 * 
	 * @param  array $data            The notification data.
	 * @param  array $pre_create_data The array data from pre_create_insert_array().
	 * 
	 * @return void
	 */
	public function create_insert_array( $data, $pre_create_data = [] ) {

		$this->set_data( 'item_id', $data[ 'item_id' ] );
		$this->set_data( 'user_id', $data[ 'user_id' ] );

		parent::create_insert_array( $data, $pre_create_data );

	}

	/**
	 * Function for getting the data for insertion in an SQL query.
	 *
	 * @return array  An array of data ready ready to be inserted into the database.
	 */
	public function get_insert_array() {

		return parent::get_insert_array();

	}

}
