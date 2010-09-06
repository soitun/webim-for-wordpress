<?php

require( dirname( __FILE__ ) . '/lib/webim.class.php' );

/** DB */
global $wpdb;
$table_name = $wpdb->prefix . "webim_histories";

/** Config */
$_IMC = array(
	"domain" => get_option("webim_domain"),
	"apikey" => get_option("webim_apikey"),
	"host" => get_option("webim_host"),
	"port" => get_option("webim_port"),
);

/** User */

$user = webim_get_current_user();
if ( isset( $user->visitor ) && gp('visitorstatus') ){
	$user->status = gp('visitorstatus');
}

$user->show = gp('show') ? gp('show') : "available";

//Common $ticket

$ticket = gp('ticket');
if($ticket){
	$ticket = stripslashes($ticket);
}

/**
 * Get history message
 *
 * @param string $type unicast or multicast
 * @param string $id
 *
 * Example:
 * 	history('unicast', 'webim');
 * 	history('multicast', '36');
 *
 */

function history( $type, $id ){
	global $wpdb;
	$user = webim_get_current_user();
	$user_id = $user->id;
	$table_name = $wpdb->prefix . "webim_histories";
	$list = array();
	if($type == "unicast"){
		$sql = $wpdb->prepare("SELECT * FROM $table_name 
			WHERE `type` = 'unicast' 
			AND ((`to`='%s' AND `from`='%s' AND `fromdel` != 1) 
			OR (`send` = 1 AND `from`='%s' AND `to`='%s' AND `todel` != 1))  
			ORDER BY timestamp DESC LIMIT 30", $id, $user_id, $id, $user_id);
		foreach( $wpdb->get_results( $sql ) as $value ){
			array_unshift($list, log_item($value));
		}
	}
	return $list;
}

/**
 * Get new message
 *
 */

function new_message() {
	global $wpdb;
	$user = webim_get_current_user();
	$id = $user->id;
	$table_name = $wpdb->prefix . "webim_histories";
	$list = array();
	$sql = $wpdb->prepare("SELECT * FROM $table_name
		WHERE `to`='%s' and send = 0 
		ORDER BY timestamp DESC LIMIT 100", $id);
	foreach( $wpdb->get_results( $sql ) as $value ){
		array_unshift($list, log_item($value));
	}
	return $list;
}

/**
 * mark the new message as read.
 *
 */

function new_message_to_histroy() {
	global $wpdb;
	$user = webim_get_current_user();
	$table_name = $wpdb->prefix . "webim_histories";
	$wpdb->update($table_name, array("send" => 1), array("to" => $user->id, "send" => 0));
}

function log_item($value){
	return (object)array(
		'to' => $value->to,
		'nick' => $value->nick,
		'from' => $value->from,
		'style' => $value->style,
		'body' => $value->body,
		'type' => $value->type,
		'timestamp' => $value->timestamp
	);
}


function ids_array($ids){
	return ($ids===NULL || $ids==="") ? array() : (is_array($ids) ? array_unique($ids) : array_unique(explode(",", $ids)));
}
