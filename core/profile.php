<?php

/**
 * Profile Change Alerts - Profile class
 *
 * @package Profile Change Alerts
 * @subpackage Profile Change Alerts Core
 */
final class PRFCHN_Core_Profile {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * WP User object properties
	 */
	private $userProperties = array(
		'user_pass' 	=> 'Password',
		'user_nicename' => 'Nicename',
		'user_email'	=> 'User email',
		'user_url'		=> 'User URL',
		'user_status'	=> 'User status',
		'display_name' 	=> 'Display name',
	);



	/**
	 * User profile meta data
	 */
	private $userMetaData = array(
		'nickname' 				=> 'Nickname',
		'first_name' 			=> 'First name',
		'last_name' 			=> 'Last name',
		'description' 			=> 'Biographical Info',
		'rich_editing' 			=> 'Visual Editor',
		'comment_shortcuts' 	=> 'Keyboard shortcuts',
		'admin_color' 			=> 'Admin Color Scheme',
		'show_admin_bar_front' 	=> 'Show front Toolbar',
	);



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create or retrieve instance
	 */
	public static function instance() {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self;

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct() {}



	// Public methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Save profile data and metadata
	 *
	 * @param WP_User $userProfileWP The current WP_User object.
	 */
	public function save($userProfileWP) {

		// Initialize
		$userProfileData = array();

		// Collect WP User object data
		foreach ($this->userProperties as $property => $label)
			$userProfileData['user_'.$property] = isset($userProfileWP->data->$property)? $userProfileWP->data->$property : '';

		// WP stored user metadata
		$metaDataWP = get_user_meta($userProfileWP->ID);
		if (empty($metaDataWP) || !is_array($metaDataWP))
			$metaDataWP = array();

		// Collect WP User metadata
		foreach ($this->userMetaData as $key => $label)
			$userProfileData['meta_'.$key] = isset($metaDataWP[$key][0])? $metaDataWP[$key][0] : '';

		// Save selected profile data
		$this->setUserProfileData($userProfileWP->ID, $userProfileData);
	}



	/**
	 * Check saved profile data with current data
	 *
	 * @param int    $userId       User ID.
	 */
	public function check($userId) {

		// Retrieve decoded user profile data
		if (false === ($userProfileData = $this->getUserProfileData($userId)))
			return;

		// Retrieve user
		$userProfileWP = get_user_by('id', $userId);
		if (empty($userProfileWP) || !is_object($userProfileWP) || !is_a($userProfileWP, 'WP_User'))
			return;

		// Initialize
		$changed = array();

		// Check object properties changes
		foreach ($this->userProperties as $property => $label) {

			// Previous and current values
			$old = isset($userProfileData['user_'.$property])? $userProfileData['user_'.$property] : '';
			$value = isset($userProfileWP->data->$property)? $userProfileWP->data->$property : '';

			// Compare values
			if ($value != $old) {
				$changed[] = array($label, $old, $value);
				$userProfileData['user_'.$property] = $value;
			}
		}

		// WP stored user metadata
		$metaDataWP = get_user_meta($userId);
		if (empty($metaDataWP) || !is_array($metaDataWP))
			$metaDataWP = array();

		// Check object metadata changes
		foreach ($this->userMetaData as $key => $label) {

			// Previous and current values
			$old = isset($userProfileData['meta_'.$key ])? $userProfileData['meta_'.$key ] : '';
			$value = isset($metaDataWP[$key][0])? $metaDataWP[$key][0] : '';

			// Compare values
			if ($value != $old) {
				$changed[] = array($label, $old, $value);
				$userProfileData['meta_'.$key] = $value;
			}
		}

		// Check changes
		if (!empty($changed)) {

			// Save current data
			$this->setUserProfileData($userId, $userProfileData);

			// Notify by email
			// ..
		}
	}



	// Internal functions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Get user profile data
	 */
	private function getUserProfileData($userId) {
		$userProfileData = get_user_meta($userId, 'prfchn_profile', true);
		$userProfileData = empty($userProfileData)? false : @json_decode($userProfileData, true);
		return (empty($userProfileData) || !is_array($userProfileData))? false : $userProfileData;
	}



	/**
	 * Saves user profile data
	 *
	 * @param $data Array
	 */
	private function setUserProfileData($userId, $userProfileData) {
		update_user_meta($userId, 'prfchn_profile', @json_encode($userProfileData));
	}



}