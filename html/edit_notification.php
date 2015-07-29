<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly*/ ?>
<h1><?php _e( 'Custom Emailer For Rezgo', $domain ) ?></h1>

   <div id="rezgo_saved_success"><img src="<?php echo $this->plugin_base_url?>images/success.gif"><?php _e( 'Your changes have been saved', $domain ) ?></div>
   <div id="rez_custom_email_settings">
	<div class="field_frame">
    <fieldset>

        <legend><?php _e( 'Notification Details', $domain ) ?></legend>
		<div class="field_contents">
        <?php _e( 'Tour / Option', $domain ) ?> <select id="rezgo_tour_uid">

	  <?php foreach($this->tours as $tour){?>

	    <option value="<?php echo $tour->uid?>" <?php selected($tour->uid, $this->notification->tour_uid, 1);?> ><?php echo $tour->name?></option>

	  <?php } ?>

        </select>

        <br />

        <br />

        <?php _e( 'The subject/message can contain variables. Here are a list of the variables that are supported', $domain ) ?>

        <ul>

        <li>{tour_name} {option_name}</li>

        <li>{first_name} {last_name} {email}</li>

        <li>{trans_num} {booking_date}</li>

        </ul>

        <div id=rezgo_loader><?php _e( 'Loading notification...', $domain ) ?></div>

        <label><?php _e( 'Subject', $domain ) ?></label>&nbsp;<input id="rezgo_notification_subject" type="text" size="80" value="<?php echo esc_html($this->notification->subject)?>"><br />

        <label><?php _e( 'TEXT message', $domain ) ?></label><br />

        <textarea id="rezgo_notification_text"><?php echo esc_html($this->notification->message_text)?></textarea><br />
				<br />
        <label><?php _e( 'HTML message', $domain ) ?></label><br />

        <textarea id="rezgo_notification_html"><?php echo esc_html($this->notification->message_html)?></textarea><br />

        <input type="submit" class="button-primary" value="<?php _e( 'Save Notification', $domain ) ?>" id="submitSaveNotify" />
		</div>
    </fieldset>
	</div>
   </div> 

<script type="text/javascript" >

var rezgo_modified_id=0;

jQuery(document).ready(function($) {

    // keys
	$("#submitSaveNotify").show();
	
    $( "#rezgo_notification_text,#rezgo_notification_html,#rezgo_notification_subject" ).bind('input propertychange', function() {

		if(!rezgo_modified_id)

		{

			$('#rezgo_saved_success').hide();

			//$("#submitSaveNotify").show();

			rezgo_modified_id=$( "#rezgo_tour_uid" ).val();

		}

    });	

    

    $( "#submitSaveNotify" ).click(function() {

		regzo_save_notification(rezgo_modified_id);

		return false;

    });	

    

    $( "#rezgo_tour_uid" ).change(function() {

		if(rezgo_modified_id && confirm("<?php _e('Notification was updated! Save Changes?',$domain)?>"))

		{

			regzo_save_notification(rezgo_modified_id);

			//return ;

		}

		

		$('#rezgo_notification_subject').val("");

		$('#rezgo_notification_text').val("");

		$('#rezgo_notification_html').val("");

		//$('#submitSaveNotify').hide();

		$('#rezgo_loader').show();

		var data = { action: 'rezgo_mailer','method': 'ajax_get_notification','tour_uid':$(this).val()}

		$.post(ajaxurl, data, function(response) {

			$('#rezgo_saved_success').hide();

			$('#rezgo_loader').hide();

			if(response.result=='success')

			{

				rezgo_modified_id=0;

				$('#rezgo_notification_subject').val(response.subject);

				$('#rezgo_notification_text').val(response.message_text);

				$('#rezgo_notification_html').val(response.message_html);

			}

			else

			{

				alert(response.message);

			}

		}

		,"json"

		);

    });	

    

    function regzo_save_notification(modified_id)

    {
		if(  $("#rezgo_notification_subject").val()==""  || $("#rezgo_notification_text").val()=="" && $("#rezgo_notification_html").val()=="" ) {
			alert("<?php _e('Please, fill in both Subject and Message!',$domain)?>");
			return;
			
		}

		var data = { action: 'rezgo_mailer','method': 'ajax_save_notification','tour_uid':modified_id,'subject':$("#rezgo_notification_subject").val(),'message_text':$("#rezgo_notification_text").val(),'message_html':$("#rezgo_notification_html").val()}

		$.post(ajaxurl, data, function(response) {

			if(response.result=='success')

			{

				rezgo_modified_id=0;

				//$('#submitSaveNotify').hide();

				$('#rezgo_saved_success').show();

			}

			else

			{

				alert(response.message);

			}

		}

		,"json"

		);

    }

    

    $( "#rezgo_tour_uid" ).change();



});

</script>