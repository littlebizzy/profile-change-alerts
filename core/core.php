<?php

/**
 * Profile Change Alerts - Core class
 *
 * @package Profile Change Alerts
 * @subpackage Profile Change Alerts Core
 */
final class PRFCHN_Core {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Custom profile object
	 */
	private $profile;



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
	 * Declare WP hooks
	 */
	private function __construct() {

		// This action only fires if the current user is editing their own profile.
		add_action('show_user_profile', array(&$this, 'showUserProfile'));

		// Fires after the 'About the User' settings table on the 'Edit User' screen.
		add_action('edit_user_profile', array(&$this, 'showUserProfile'));

		// Fires immediately after an existing user is updated (from wp_insert_user function)
		add_action('profile_update', array(&$this, 'updateProfile'), 10, 2);
	}



	// Handle WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show user profile hook. Supports both user profile owner and other users access.
	 * Expected pages: /wp-admin/user-edit.php and /wp-admin/profile.php
	 *
	 * @param WP_User $userProfile The current WP_User object.
	 */
	public function showUserProfile($userProfile) {
		$this->loadProfileObject();
		$this->profile->save($userProfile);
	}



	/**
	 * Update user profile hook
	 *
	 * @param int    $userId       User ID.
	 * @param object $oldUserData Object containing user's data prior to update.
	 */
	public function updateProfile($userId, $oldUserData) {
		$this->loadProfileObject();
		$this->profile->check($userId);
	}



	// Internal functions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load custom Profile object
	 */
	private function loadProfileObject() {
		if (!isset($this->profile)) {
			require_once(PRFCHN_PATH.'/core/profile.php');
			$this->profile = PRFCHN_Core_Profile::instance();
		}
	}



}