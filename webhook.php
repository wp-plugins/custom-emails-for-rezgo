<?php
//this file called inside class method!
global $wpdb,$RezgoEmailerObj;

//get raw POST
$post = file_get_contents("php://input"); 
// if php added extra \
if( get_magic_quotes_gpc() )
	$post = stripslashes( $post );

$xml = @simplexml_load_string($post);
if(!$xml OR !isset($xml->booking))
	rezgo_die_log("ERR:Bad XML passed");

//check if got correct values
$bookings = array();
$req_fields = array("date","item_id","tour_name","trans_num","email_address");
foreach($xml->booking as $b) {
	$errors = array();
	foreach($req_fields  as $f)
		if(empty($b->$f))
			$errors[]="$f is required";
	if($errors)
		rezgo_die_log("ERR:".join(". ", $errors));
		
	//get fields
	$t = array();
	$t['booking_timestamp']=$b->date;
	$t['trans_num']=$b->trans_num;
	$t['tour_uid']=$b->item_id;
	$t['tour_name']=$b->tour_name;
	$t['option_name']=$b->option_name;
	$t['first_name']=$b->first_name;
	$t['last_name']=$b->last_name;
	$t['email']=$b->email_address;
	$t= array_map("strval", $t);
	$bookings[] = $t;
}
if(empty($bookings))	
	rezgo_die_log("ERR:No bookings. Format updated?");

//rezgo_log(count($bookings)." bookings parsed");
foreach($bookings as $b) {
	if($RezgoEmailerObj->send_email($b,$error))
		$b['notification_sent']=1;
	else{
		$b['notification_error']=$error;
		rezgo_log("$b[tour_name]->$error");
	}
	$wpdb->insert($RezgoEmailerObj->table_bookings,$b);
}
//remove old log records
$last_id=$wpdb->get_var("SELECT id FROM {$RezgoEmailerObj->table_bookings} ORDER BY id DESC LIMIT {$RezgoEmailerObj->max_bookings_records},1");
if($last_id)
	$wpdb->query("DELETE FROM {$RezgoEmailerObj->table_bookings} WHERE id<=$last_id");

//done
rezgo_die_log("OK:".count($bookings));


function rezgo_die_log($s) {
	rezgo_log($s);
	die();
}


function rezgo_log($s) {
	global $wpdb,$RezgoEmailerObj; 
	echo "$s\n";
	
	$b = array("timestamp"=>time(),"message"=>$s);
	$wpdb->insert($RezgoEmailerObj->table_log,$b);
	//remove old log records
	$last_id = $wpdb->get_var("SELECT id FROM {$RezgoEmailerObj->table_log} ORDER BY id DESC LIMIT {$RezgoEmailerObj->max_log_records},1");
	if($last_id)
		$wpdb->query("DELETE FROM {$RezgoEmailerObj->table_log} WHERE id<=$last_id");
}
?>