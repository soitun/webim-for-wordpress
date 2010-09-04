<?php

/*
  Plugin Name: WebIM
  Plugin URI: http://www.webim20.cn
  Description: Chat with the visitors
  Author: Hidden
  Author URI: http://www.webim20.cn
  Version: 1.0.0
 */

/** Install */
register_activation_hook( __FILE__, 'webim_install' );

/** Display webim in blog page */
add_action( 'wp_print_styles', 'webim_stylesheet' );
add_action( 'wp_footer', 'webim_footer' );

if(is_admin()){
	/** Admin link */
	add_action( 'admin_menu', 'webim_admin_menu' );

	/** Display webim in admin page */
	add_action( 'admin_print_styles', 'webim_stylesheet' );
	add_action( 'admin_footer', 'webim_footer' );
}

/** Handle webim request */
foreach( array( 'online', 'offline', 'status', 'message', 'presence', 'refresh', 'buddies', 'history' ) as $webim_action ) {
	add_action( 'wp_ajax_webim_' . $webim_action, 'webim_request' );
	add_action( 'wp_ajax_nopriv_webim_' . $webim_action, 'webim_request' );
}

function webim_request() {
	$action = explode( "_", $_GET['action'] );
	$action = $action[1];
	$file = dirname( __FILE__ ) . '/' . $action . '.php';
	if ( file_exists($file) ) {
		require_once( $file );
	}
	die();
}

/**
 * Get buddies
 */

function webim_get_buddies($names = null) {
	global $wpdb;
	$users = array();
	$current_user = webim_get_current_user();
	if( $names ){
		$names = "'".implode("','", explode(",", $wpdb->prepare($names)))."'";
		$data = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_login IN ($names)" );
	} else {
		$data = get_users_of_blog();
	}
	foreach ( $data as $user ) {
		if( $user->ID != $current_user->uid ){ 
			$users[] = (object)array(
				"uid" => $user->ID,
				"id" => $user->user_login,
				"nick" => $user->display_name,
				"pic_url" => webim_get_avatar($user->user_email)
			);
		}
	}
	return $users;
}

/**
 * Get current visitor id, nick.
 * 
 * Automatic generate visitor info and save it in cookie if not.
 *
 */

function webim_get_visitor() {
	if ( isset($_COOKIE['webim_visitor_id_'.COOKIEHASH]) ) {
		$id = $_COOKIE['webim_visitor_id_'.COOKIEHASH];
	} else {
		$id = uniqid();
		setcookie('webim_visitor_id_'.COOKIEHASH, $id, time() + 3600 * 24, COOKIEPATH, COOKIE_DOMAIN);
	}
	if ( isset($_COOKIE['webim_visitor_nick_'.COOKIEHASH]) ) {
		$nick = $_COOKIE['webim_visitor_nick_'.COOKIEHASH];
	} else {
		$nick = 'guest' . rand(1000, 9999);
		setcookie('webim_visitor_nick_'.COOKIEHASH, $nick, time() + 3600 * 24, COOKIEPATH, COOKIE_DOMAIN);
	}
	return compact('id', 'nick');
}

/**
 * Get current webim user id, nick
 *
 */

function webim_get_current_user() {
	global $webim_current_user;
	if( $webim_current_user )
		return $webim_current_user;
	$user = wp_get_current_user();
	//print_r($user);//user info
	if ( 0 == $user->ID ) {
		$visitor = webim_get_visitor();
		$commenter = wp_get_current_commenter();
		$comment_author = $commenter['comment_author'];
		$nick = $comment_author ? $comment_author : $visitor['nick'];
		$webim_current_user = (object)array(
			"uid" => 0,
			"id" => $visitor['id'],
			"nick" => $nick,
			"visitor" => "true",
			"pic_url" => webim_get_avatar($commenter['comment_author_email'])
		);
	} else {
		$webim_current_user = (object)array(
			"uid" => $user->ID,
			"id" => $user->user_login,
			"nick" => $user->display_name,
			"pic_url" => webim_get_avatar($user->user_email)
		);
	}
	return $webim_current_user;
}

/**
 * Get avatar url
 *
 */
function webim_get_avatar($email){
	preg_match('/http[^\']*/', get_avatar($email, 50), $urls);
	return $urls[0];
}

/** Install webim */
function webim_install() {
	require_once( dirname(__FILE__) . '/install.php' );
}

/** Link stylesheet */
function webim_stylesheet() {
	global $wpdb;
	if ( $_GET['theme'] )
		$theme = $wpdb->escape( $_GET['theme'] );
	else
		$theme = get_option("webim_theme");
	$theme = $theme ? $theme : "base";
	echo '<link rel="stylesheet" href="' . webim_url_for( "static/webim.service.min.css" ) . '" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="' . webim_url_for( "static/themes/$theme/jquery.ui.theme.css" ) . '" type="text/css" media="screen" />';
}

/** Put script */
function webim_footer() {
	$local = get_option("webim_local");
	$local = $local ? $local : "zh-CN";
	echo '<script src="'. webim_url_for( "static/webim.service.min.js" ) . '" type="text/javascript"></script>';
	echo '<script src="'. webim_url_for( "static/i18n/webim-{$local}.js" ) . '" type="text/javascript"></script>';
	echo '<script type="text/javascript">var _webim_ajaxurl = "' . admin_url('admin-ajax.php') . '";var _webim_path = "' . webim_url_for("") . '";</script>';
	echo '<script src="'. webim_url_for( "custom.js" ) . '" type="text/javascript"></script>';
}

if(is_admin()){
	/** Add admin menu */
	require_once( dirname( __FILE__ ) . "/admin.php" );
	function webim_admin_menu() {
		add_menu_page( "webim", "webim", "manage_options", __FILE__, "webim_config");
		add_submenu_page( __FILE__, "webim_config", "webim_config", "manage_options", __FILE__, "webim_config");
		add_submenu_page( __FILE__, "webim_themes", "webim_themes", "manage_options", "webim_themes", "webim_themes");
	}
}

/**  Webim url */

function webim_url_for($path) {
	// Pre-2.6 compatibility
	if ( !defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
	return WP_CONTENT_URL . '/plugins/' . plugin_basename( dirname( __FILE__ ) ) . '/' . $path;
}

?>
