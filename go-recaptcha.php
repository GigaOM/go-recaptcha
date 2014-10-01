<?php
/**
 * Plugin Name: Gigaom Recaptcha
 * Plugin URI: http://gigaom.com
 * Description: Class for using reCaptcha in WP.
 * Version: 1.0
 * Author: Gigaom
 * Author URI: http://gigaom.com
 */


/**
 * Singleton
 */
function go_recaptcha()
{
	global $go_recaptcha;

	if ( ! isset( $go_recaptcha ) || ! $go_recaptcha )
	{
		require_once __DIR__ . '/components/class-go-recaptcha.php';
		$go_recaptcha = new GO_reCaptcha();
	}//end if

	return $go_recaptcha;
} // END go_recaptcha