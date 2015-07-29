<?php

/*

Plugin Name: Custom Emails for Rezgo

Plugin URI: http://wordpress.org/plugins/custom-emails-for-rezgo/

Description: The plugin contains a web hook API endpoint that is triggered when a booking is made in your Rezgo account.  The plugin gives you the ability to create a custom email for every tour and option combination available in your account.

Version: 1.4

Author: alexvp

Author URI: http://alexvp.elance.com

*/





$RezgoEmailerObj= new RezgoEmailer();

register_activation_hook( __FILE__, array($RezgoEmailerObj,'install') );

register_deactivation_hook( __FILE__, array($RezgoEmailerObj,'uninstall') );

add_action('admin_menu', array($RezgoEmailerObj,'admin_menu') );

add_action('wp_ajax_rezgo_mailer', array($RezgoEmailerObj, 'ajax_rezgo_mailer') );

add_action('admin_head', array($RezgoEmailerObj,'admin_head') );

// required !

add_action('phpmailer_init', array($RezgoEmailerObj,'set_email_text_body') );

add_action('init', array($RezgoEmailerObj,'try_call_webhook') );



class RezgoEmailer {

	var $text_domain = "RezgoEmailer";

	var $api_endpoint = "http://xml.rezgo.com/xml";

	var $setting_names = "rezgo_account_cid|rezgo_api_key|rezgo_from_email|rezgo_from_name|rezgo_last_updated";

	var $max_log_records = 100;

	var $max_bookings_records = 50;



	function RezgoEmailer() {

		global $wpdb;

		$this->load_settings();

		$this->plugin_base_url = plugins_url("/", __FILE__);

		$this->table_tours = $wpdb->prefix."rezgo_emailer_tours";

		$this->table_notifications = $wpdb->prefix."rezgo_emailer_notifications";

		$this->table_bookings = $wpdb->prefix."rezgo_emailer_bookings";

		$this->table_log = $wpdb->prefix."rezgo_emailer_log";

	}

	

	function uninstall() {

		global $wpdb;

		foreach( explode("|",$this->setting_names) as $key)

			delete_option($key);

		$wpdb->query("DROP TABLE IF EXISTS `{$this->table_bookings}`,`{$this->table_log}`,`{$this->table_tours}`,`{$this->table_notifications}`");

	}

	

	function install() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		

		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_log}` (

			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,

			`timestamp` int(10) unsigned NOT NULL,

			`message` varchar(255) NOT NULL,

  			PRIMARY KEY (`id`)

			)";

		dbDelta( $sql );



		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_bookings}` (

			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,

			`booking_timestamp` int(10) unsigned NOT NULL,

			`tour_uid` int(10) unsigned NOT NULL,

			`tour_name` varchar(255) NOT NULL,

			`option_name` varchar(255) NOT NULL,

			`trans_num` varchar(255) NOT NULL,

			`first_name` varchar(255) NOT NULL,

			`last_name` varchar(255) NOT NULL,

			`email` varchar(255) NOT NULL,

			`notification_sent` int(11) NOT NULL,

			`notification_error` varchar(255) NOT NULL,

  			PRIMARY KEY (`id`)

			)";

		dbDelta( $sql );

		

		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_notifications}` (

			`tour_uid` int(10) unsigned NOT NULL,

			`tour_name` varchar(255) NOT NULL,

			`author_userid` int(10) unsigned NOT NULL,

			`last_updated` datetime NOT NULL,

			`active` tinyint(4) NOT NULL,

			`emails_sent` int(10) unsigned NOT NULL,

			`subject` varchar(255) NOT NULL,

			`message_text` text NOT NULL,

			`message_html` text NOT NULL,

			PRIMARY KEY (`tour_uid`)

			)";

		dbDelta( $sql );



		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_tours}` (

			`uid` int(10) unsigned NOT NULL,

			`name` varchar(255) NOT NULL,

			PRIMARY KEY (`uid`)

			)";

		dbDelta( $sql );

	}

	

	function admin_menu() {
		
		$version = get_bloginfo('version');
		$vparts = explode('.', $version);
		if ((int)$vparts[0] >= 3 && (int)$vparts[1] >= 8) {
			$plugin_icon = 'dashicons-email-alt';
		} else {
			$plugin_icon = '';
		}
		

		add_menu_page(

		'Custom Emailer For Rezgo', 

		__('Rezgo Emailer', $this->text_domain), 

		'manage_options',

		'rezgo-emailer-menu', 

		array(&$this, 'settings_page'),

		$plugin_icon);



		add_submenu_page(

		'rezgo-emailer-menu',

		'Custom Emailer For Rezgo', 

		__('Settings', $this->text_domain),

		'manage_options',

		'rezgo-emailer-menu', 

		array(&$this, 'settings_page'));



		add_submenu_page(

		'rezgo-emailer-menu',

		'Custom Emailer For Rezgo', 

		__('Notifications', $this->text_domain), 

		'manage_options',

		'rezgo-emailer-notifications', 

		array(&$this, 'notifications_page'));



		add_submenu_page(

		'rezgo-emailer-menu',

		'Custom Emailer For Rezgo', 

		__('Bookings', $this->text_domain), 

		'manage_options',

		'rezgo-emailer-bookings', 

		array(&$this, 'bookings_page'));



		add_submenu_page(

		'rezgo-emailer-menu',

		'Custom Emailer For Rezgo', 

		__('Log', $this->text_domain), 

		'manage_options',

		'rezgo-emailer-log', 

		array(&$this, 'log_page'));

	}



	function load_settings() {

		$this->settings=array();

		foreach( explode("|",$this->setting_names) as $key)

			$this->settings[$key]= get_option($key,"");

	}



	function admin_head() {

		wp_register_style( 'rezgo-style', plugins_url('style.css', __FILE__) );

		wp_enqueue_style( 'rezgo-style' );

	}



	// load html pages for menu

	// $domain is text domain

	// for http://codex.wordpress.org/Function_Reference/_e

	function show_page($page) {

		$domain = $this->text_domain;

		include "html/$page.php";

	}

	function settings_page() {

		$this->show_page("settings");

	}

	function notifications_page() {

		global $wpdb;

		$this->page_url=admin_url("admin.php?page=rezgo-emailer-notifications");

	

		if( @$_GET['delete'] ) {

			$wpdb->delete( $this->table_notifications, array( 'tour_uid' => $_GET['delete'] ), array( '%d' )); 

			wp_redirect( add_query_arg( 'delete_ok','1',$this->page_url));

		}

		if( isset($_GET['edit']) ) {

			$this->notification= $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_notifications} WHERE tour_uid=%d", $_GET['edit']) );

			$this->tours = $wpdb->get_results("SELECT * FROM {$this->table_tours}");

			$this->show_page("edit_notification");

		}

		else

		{

			$this->notifications = $wpdb->get_results("SELECT * FROM {$this->table_notifications}");

			$this->tours = $wpdb->get_results("SELECT * FROM {$this->table_tours}");

			$this->show_page("notifications");

		}

	}	

	

	function log_page() {

		global $wpdb;

		$this->logs = $wpdb->get_results("SELECT * FROM {$this->table_log} ORDER BY id DESC");

		$this->show_page("log");

	}



	function bookings_page() {

		global $wpdb;

		$this->bookings = $wpdb->get_results("SELECT * FROM {$this->table_bookings} ORDER BY id DESC");

		$this->show_page("bookings");

	}



	//ajax route to methods

	function ajax_reply($is_success,$args) {

		$args['result'] = $is_success ? 'success': 'failed';

		echo json_encode($args);

		die();

	}



	function ajax_rezgo_mailer() {

		if(!empty($_POST['method']) AND method_exists($this,$_POST['method']))

			$this->$_POST['method']();

		else

			_e('non-valid method', $this->text_domain );

	}



	function ajax_get_notification() {

		global $wpdb;

		$notification= $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_notifications} WHERE tour_uid=%d", $_POST['tour_uid']) );

		$this->ajax_reply(true, array('subject'=>$notification->subject, 'message_html'=>$notification->message_html, 'message_text'=>$notification->message_text) );

	}



	function ajax_save_notification() {

		global $wpdb;

		$notification = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_notifications} WHERE tour_uid=%d", $_POST['tour_uid']) );

		$tour = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_tours} WHERE uid=%d", $_POST['tour_uid']) );

	

		$data = array();

		$data['tour_uid'] = $_POST['tour_uid'];

		$data['author_userid'] = get_current_user_id();

		$data['last_updated'] =current_time('mysql');

		$data['subject'] =$_POST['subject'];

		$data['message_text'] =$_POST['message_text'];

		$data['message_html'] =$_POST['message_html'];

		$data['tour_name'] =$tour->name;

		if($notification)

			$wpdb->update( $this->table_notifications,$data , array('tour_uid' => $notification->tour_uid ), array( '%d','%d','%s','%s','%s','%s','%s' ),array('%d'));

		else

			$wpdb->insert( $this->table_notifications,$data , array( '%d','%d','%s','%s','%s','%s','%s' ));

		$this->ajax_reply(true,array());

	}



	function ajax_set_keys() {

		$url = $this->api_endpoint . '?transcode=' . $_POST['account_cid'] . '&key=' . $_POST['api_key'] . '&i=company' ;

		$reply= wp_remote_get( $url );

		if ( is_wp_error( $reply) ) 

			$this->ajax_reply(false, array('connect_problem'=>$reply->get_error_message()) );



		$xml = simplexml_load_string($reply['body']);

		if(empty($xml->domain))// we get only string with error message

			$this->ajax_reply(false, array('connect_problem'=>(string)$xml) );



		update_option( 'rezgo_account_cid', $_POST['account_cid']);

		update_option( 'rezgo_api_key', $_POST['api_key']);

		$this->ajax_reply(true, array('company_website'=>"http://{$xml->domain}.rezgo.com") );

	}



	function ajax_set_from_email() {

		update_option( 'rezgo_from_email', $_POST['from_email']);

		update_option( 'rezgo_from_name', $_POST['from_name']);

		$this->ajax_reply(true, array('message'=>__('name/email updated', $this->text_domain )) );

	}



	function ajax_sync_tours() {

		global $wpdb;

		$url = $this->api_endpoint . '?transcode=' . get_option( 'rezgo_account_cid') . '&key=' . get_option( 'rezgo_api_key') . '&i=search_items' ;

		$reply= wp_remote_get( $url );

		if ( is_wp_error( $reply) ) 

			$this->ajax_reply(false, array('message'=>$reply->get_error_message()) );



		$xml = simplexml_load_string($reply['body']);

		if(empty($xml->item))// we get only string with error message

			$this->ajax_reply(false, array('message'=>__("No active tours?", $this->text_domain )) );



		$wpdb->query("TRUNCATE {$this->table_tours}");

		foreach($xml->item as $i) {

			$name = trim( (string)$i->name ." @ ". (string)$i->time );

			$wpdb->insert( $this->table_tours, array( 'uid' => (string)$i->uid, 'name' => $name), array( '%d', '%s' ) );

			//we update tour's title in notification rules

			$wpdb->update( $this->table_notifications, array( 'tour_name' => $name), array('tour_uid' => (string)$i->uid ), array( '%s' ),array('%d'));

		}



		$last_updated = date_i18n('j F Y @ H:i A', time()); 

		update_option( 'rezgo_last_updated', $last_updated );

		$this->ajax_reply(true, array('message'=>count($xml->item )." " .__("tours imported", $this->text_domain), 'last_updated'=>$last_updated) );

	}

	

	// MAIN function works with table_log structure

	function send_email($b,&$reason)

	{

		global $wpdb;



		$notification = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_notifications} WHERE tour_uid=%d", $b['tour_uid']) );

		if(!$notification or empty($notification->subject)) {

			$reason = __('Notification is not defined or subject is empty', $this->text_domain);

			return false;

		}

		

		$from_email= get_option('rezgo_from_email');

		if(!$from_email) {

			$reason = __('From Email is not defined in Settings', $this->text_domain);

			return false;

		}

		$from_name= get_option('rezgo_from_name');

		if(!$from_name) {

			$reason = __('From Name is not defined in Settings', $this->text_domain);

			return false;

		}



		// make subst map

		$subst=array();

		foreach($b as $k=>$v)

			$subst['{'.$k.'}']=$v;

		$subst['{booking_date}']= date_i18n(get_option('date_format'),$b['booking_timestamp']);

		

		$subject = strtr($notification->subject,$subst);

		$message_html = strtr($notification->message_html,$subst);

		//will send as html 

		add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

		// we remember plain version and set it in hook!

		$this->message_text = strtr($notification->message_text,$subst);

		$result = wp_mail($b['email'], $subject, $message_html, "From: $from_name<$from_email>\r\n" );

		if(!$result )

			$reason = __("WP_mail failed!", $this->text_domain);

		else 

			$wpdb->query($wpdb->prepare("UPDATE {$this->table_notifications} SET emails_sent=emails_sent+1 WHERE tour_uid=%d", $b['tour_uid']) );

		

		unset($this->message_text);

		return $result;

	}

	

	// carefull, we set alt/text body for html emails only !

	function set_email_text_body($phpmailer) {

		if( $phpmailer->ContentType == 'text/html' AND !empty($this->message_text)) {

			$phpmailer->AltBody = $this->message_text;

			//print_r($phpmailer);die();

		}

	}



	function try_call_webhook(){

		if(isset($_GET['rezgo-webhook'])) {

			include dirname( __FILE__ ) . "/webhook.php";

			die();

		}

	}

	

}

?>