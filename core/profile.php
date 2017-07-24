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
	 * @param WP_User $userProfile The current WP_User object.
	 */
	public function save($userProfile) {

	}



	/**
	 * Check saved profile data with current data
	 *
	 * @param int    $user_id       User ID.
	 */
	public function check($user_id) {

	}



	// Internal functions
	// ---------------------------------------------------------------------------------------------------



}