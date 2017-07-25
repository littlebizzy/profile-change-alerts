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
		'user_pass' 			=> 'Password',
		'user_nicename' 		=> 'Nicename',
		'user_email'			=> 'User email',
		'user_url'				=> 'User URL',
		'user_status'			=> 'User status',
		'display_name' 			=> 'Display name',
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



	/**
	 * Woocommerce Billing data
	 */
	private $userWCBilling = array(
		'billing_first_name' 	=> 'First name',
		'billing_last_name' 	=> 'Last name',
		'billing_company'		=> 'Company',
		'billing_address_1'		=> 'Address line 1',
		'billing_address_2'		=> 'Address line 2',
		'billing_city'			=> 'City',
		'billing_postcode' 		=> 'Postcode / ZIP',
		'billing_country' 		=> 'Country',
		'billing_state' 		=> 'State / County',
		'billing_phone' 		=> 'Phone',
		'billing_email' 		=> 'Email address',
	);



	/**
	 * Woocommerce Shipping data
	 */
	private $userWCShipping = array(
		'shipping_first_name' 	=> 'First name',
		'shipping_last_name' 	=> 'Last name',
		'shipping_company'		=> 'Company',
		'shipping_address_1'	=> 'Address line 1',
		'shipping_address_2'	=> 'Address line 2',
		'shipping_city'			=> 'City',
		'shipping_postcode' 	=> 'Postcode / ZIP',
		'shipping_country' 		=> 'Country',
		'shipping_state' 		=> 'State / County',
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

		// Retrieve previous user profile data
		if (false === ($userProfileData = $this->getUserProfileData($userProfileWP->ID)))
			$userProfileData = array();


		/* WP User profile object */

		// Collect WP User object data
		foreach ($this->userProperties as $property => $label)
			$userProfileData['user_'.$property] = isset($userProfileWP->data->$property)? $userProfileWP->data->$property : '';


		/* WP User profile metadata */

		// WP stored user metadata
		$metaDataWP = get_user_meta($userProfileWP->ID);
		if (empty($metaDataWP) || !is_array($metaDataWP))
			$metaDataWP = array();

		// Collect WP User metadata
		foreach ($this->userMetaData as $key => $label)
			$userProfileData['meta_'.$key] = isset($metaDataWP[$key][0])? $metaDataWP[$key][0] : '';


		/* WooCommerce Profile data */

		// Copy Billing fields
		foreach ($this->userWCBilling as $key => $label)
			$userProfileData['wc_billing_'.$key] = ''.get_user_meta($userProfileWP->ID, $key, true);

		// Copy Shipping fields
		foreach ($this->userWCShipping as $key => $label)
			$userProfileData['wc_shipping_'.$key] = ''.get_user_meta($userProfileWP->ID, $key, true);


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


		/* WP User profile object */

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


		/* WP User profile metadata */

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


		/* WooCommerce Profile data */

		// Check WC profile class and user permissions
		if (class_exists('WC_Admin_Profile') && current_user_can('manage_woocommerce')) {

			// Check Billing changes
			foreach ($this->userWCBilling as $key => $label) {

				// Previous and current values
				$old = isset($userProfileData['wc_billing_'.$key ])? $userProfileData['wc_billing_'.$key ] : '';
				$value = ''.get_user_meta($userId, $key, true);

				// Compare values
				if ($value != $old) {
					$changed[] = array($label.' - Customer billing address', $old, $value);
					$userProfileData['wc_billing_'.$key] = $value;
				}
			}

			// Check Shipping changes
			foreach ($this->userWCShipping as $key => $label) {

				// Previous and current values
				$old = isset($userProfileData['wc_shipping_'.$key ])? $userProfileData['wc_shipping_'.$key ] : '';
				$value = ''.get_user_meta($userId, $key, true);

				// Compare values
				if ($value != $old) {
					$changed[] = array($label.' - Customer shipping address', $old, $value);
					$userProfileData['wc_shipping_'.$key] = $value;
				}
			}
		}


		/* Check the changes */

		// Check changes
		if (!empty($changed)) {

			// Save current data
			$this->setUserProfileData($userId, $userProfileData);

			// Notify by email
			// ..
		}
	}



	// WC Front Account
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Save WC address when the proper template is displayed
	 */
	public function saveWCAccountAddress($type) {

		// Check current user
		$userId = get_current_user_id();
		if (empty($userId))
			return;

		// Retrieve previous user profile data
		if (false === ($userProfileData = $this->getUserProfileData($userId)))
			$userProfileData = array();

		// Prepare fields
		$fields = ('billing' == $type)? $this->userWCBilling : $this->userWCShipping;
		$prefix = ('billing' == $type)? 'wc_billing_' : 'wc_shipping_';

		// Copy fields
		foreach ($fields as $key => $label)
			$userProfileData[$prefix.$key] = ''.get_user_meta($userId, $key, true);

		// Update profile data
		$this->setUserProfileData($userId, $userProfileData);
	}



	/**
	 * Check if saved data has changes
	 */
	public function checkWCAccountAddress($type) {

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