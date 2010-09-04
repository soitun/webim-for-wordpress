<?php

/*
 * Author: Hidden
 * Date: Tue Aug 24 17:35:20 CST 2010
 *
 * Clear history message
 *
 * @post $id
 *
 */

include_once('common.php');

$id = p("id");
if(empty($id)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty post $id';
}else{
	$pwdb->update($table_name, array("fromdel" => 1, "type" => "unicast"), array("from" => $user->id, "to" => $id));
	$pwdb->update($table_name, array("todel" => 1, "type" => "unicast"), array("to" => $user->id, "from" => $id));
	$pwdb->query("DELETE FROM $table_name WHERE todel = 1 and fromdel = 1");
	echo "ok";
}
