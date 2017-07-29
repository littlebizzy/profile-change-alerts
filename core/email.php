<?php

/**
 * Profile Change Alerts - Email class
 *
 * @package Profile Change Alerts
 * @subpackage Profile Change Alerts Core
 */
final class PRFCHN_Core_Email {



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
	 * Profile changes notification
	 */
	public function notify($userId, $changed) {

		// Retrieve user
		if (false === ($user = get_user_by('id', (int) $userId)))
			return;

		// Prepare emails
		$admin_email = get_option('admin_email');
		$user_email  = $user->user_email;

		// Check emails
		if (empty($admin_email) && empty($user_email))
			return;
error_log($admin_email);
error_log($user_email);
		// Message header
		$message  = 'The following changes have been detected:'."\n";
		$message .= "\n".'User: '.$user->user_login;
		$message .= "\n".'Profile: '.add_query_arg('user_id', $userId, self_admin_url('user-edit.php'));

		// Enum changes
		foreach ($changed as $change) {
			$message .= "\n\n".'- '.$change[0];
			$message .= "\n".'Old value: '.$change[1];
			$message .= "\n".'New value: '.$change[2];
		}
error_log($message);
		// Send to administrator
		if (!empty($admin_email))
			wp_mail($admin_email, 'Profile Change Alerts: detected changes in user profile', $message);

		// Send to user email
		if (!empty($admin_email) && $user_email != $admin_email)
			wp_mail($admin_email, 'Profile Change Alerts: detected changes in your profile', $message);
	}



}