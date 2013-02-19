<?php
/*
Plugin Name: 		DW Members Only
Plugin URI: 		http://www.danielwoolnough.com/portfolio/dw-members-only/
Description: 		Do you have a private blog that you only want your friends and family to read? Then this plugin is for you. It will redirect any users who aren't logged in to the site to the login form.
Requires at least: 	3.0
Tested up to: 		3.2.1
Version: 			1.1
Tags: 				dw, members, only, registered, users, daniel, woolnough
Author: 			Daniel Woolnough
Author 				URI: http://www.danielwoolnough.com/
*/

/* This gets the current page URL. */
	function ewc_get_current_url() {
		$protocol = 'http';
		if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
			$protocol .= 's';
			$protocol_port = $_SERVER['SERVER_PORT'];
		} else {
			$protocol_port = 80;
		}
		$host = $_SERVER['HTTP_HOST'];
		$port = $_SERVER['SERVER_PORT'];
		$request = $_SERVER['PHP_SELF'];
		$query = substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1);
		$toret = $protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request . (empty($query) ? '' : '?' . $query);
		return $toret;
	}
/* This looks to see if the user is logged in, and if not, redirects them to the login page. */
	function ewc_check_login() {
		$loginurl = wp_login_url();
		$currenturl = ewc_get_current_url();
		if($currenturl == $loginurl) {
			// do nothing
		} elseif(is_admin() ) {
			// do nothing
		} elseif(is_feed() ) {
			// do nothing
		} else {
		$loggedin = is_user_logged_in();
			if($loggedin == false){
				$siteurl = get_bloginfo('url') . "/wp-login.php";
				wp_redirect($siteurl); exit ;
			}
		}
	}
	add_action('template_redirect', 'ewc_check_login');
/* This takes the user to the Home page after loggin in rather than the Admin Dashboard. */
	function ewc_redirect_to_front_page() {
		global $redirect_to;
		if (!isset($_GET['redirect_to'])) {
			$redirect_to = get_option('siteurl');
		}
	}
	add_action('login_form', 'ewc_redirect_to_front_page');
?>