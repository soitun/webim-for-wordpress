<?php

/*
 * Author: Hidden
 * Date: Tue Aug 24 17:35:20 CST 2010
 *
 * Get buddies info
 *
 * @get $ids
 *
 */

include_once('common.php');

$ids = g("ids");
if(empty($ids)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty get $ids';
}else{
	$buddies = array_map(map_buddy, ids_array($ids));
	echo json_encode($buddies);
}
function map_buddy( $id ) {
	return (object) array(
		"id" => $id,
		"pic_url" => webim_get_avatar("")
	);
}
