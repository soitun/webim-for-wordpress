<?php

/*
 * @post $show
 */

include_once('common.php');
$im = new WebIM($user, null, $_IMC['domain'], $_IMC['apikey'], $_IMC['host'], $_IMC['port']);

$new_messages = new_message();

$im_buddies = array();//For online.
$active_buddies = ids_array(gp('buddy_ids'));
$buddies = webim_get_buddies( get_option( "webim_buddies" ) );
$cache_buddies = array();
foreach( $buddies as $buddy ) {
	$id = $buddy->id;
	$im_buddies[] = $id;
	$cache_buddies[$id] = $buddy;
	$buddy->presence = "offline";
	$buddy->show = "unavailable";
}

foreach($active_buddies as $id){
	if ( ! isset( $cache_buddies[$id] ) ) {
		$im_buddies[] = $id;
		$buddy = (object)array(
			"id" => $id,
			"nick" => $id,
			"pic_url" => webim_get_avatar(""),
			"presence" => "offline",
			"show" => "unavailable"
		);
		$cache_buddies[$id] = $buddy;
		$buddies[] = $buddy;
	}
}

//===============Online===============

$data = $im->online(implode(",", $im_buddies), "");
if($data->success){
	$data->new_messages = $new_messages;
	$data->rooms = array();
	foreach($data->buddies as $k => $v){
		$id = $v->id;
		if(!isset($cache_buddies[$id])){
			$cache_buddies[$id] = (object)array(
				"id" => $id,
				"nick" => $id,
				"incomplete" => true,
			);
		}
		$b = $cache_buddies[$id];
		$b->presence = $v->presence;
		$b->show = $v->show;
		if ( $v->nick )
			$b->nick = $v->nick;
		if ( $v->status )
			$b->status = $v->status;
	}

	//Provide history for active buddies and rooms
	//foreach($active_buddies as $id){
	//	if(isset($cache_buddies[$id])){
	//		$cache_buddies[$id]->history = history("unicast", $id);
	//	}
	//}
	$data->buddies = $buddies;
	new_message_to_histroy();
	echo json_encode($data);
}else{
	header("HTTP/1.0 404 Not Found");
	echo json_encode($data->error_msg);
}

