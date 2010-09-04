<?php

/** Install db and add options */

global $wp_version;
if ( version_compare( $wp_version, '3.0', '<' ) ) 
	require_once( ABSPATH . 'wp-admin/upgrade.php' );
else 
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

global $wpdb;
$table_name = $wpdb->prefix . "webim_histories";
$webim_db_version = "1.0";

if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
	/** Install */
	$sql = "CREATE TABLE $table_name (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`send` tinyint(1) DEFAULT NULL,
		`type` varchar(20) DEFAULT NULL,
		`to` varchar(50) NOT NULL,
		`from` varchar(50) NOT NULL,
		`nick` varchar(20) DEFAULT NULL COMMENT 'from nick',
		`body` text,
		`style` varchar(150) DEFAULT NULL,
		`timestamp` double DEFAULT NULL,
		`todel` tinyint(1) NOT NULL DEFAULT '0',
		`fromdel` tinyint(1) NOT NULL DEFAULT '0',
		`created_at` date DEFAULT NULL,
		`updated_at` date DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY `todel` (`todel`),
		KEY `fromdel` (`fromdel`),
		KEY `timestamp` (`timestamp`),
		KEY `to` (`to`),
		KEY `from` (`from`),
		KEY `send` (`send`)
	);";
	dbDelta($sql);
	add_option( "webim_db_version", $webim_db_version );
	add_option( "webim_domain", "" );
	add_option( "webim_apikey", "" );
	add_option( "webim_host", "webim20.cn" );
	add_option( "webim_port", "8000" );
	add_option( "webim_theme", "base" );
	add_option( "webim_local", "zh-CN" );
	add_option( "webim_emot", "default" );
} else {
	/** Upgrade */
}

?>
