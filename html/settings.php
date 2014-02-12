<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly*/ ?>
<h1><?php _e( 'Custom Emailer For Rezgo', $domain ) ?></h1>

<form method="post" action="" id="rez_custom_email_settings">

	<div class="field_frame">
    <fieldset>
        <legend><?php _e( 'Rezgo Account Settings', $domain ) ?>&nbsp;</legend>
        
        <div class="field_contents">

        <div id="rez_custom_email_success">
        
          <div class="rez_custom_email_icon">
            <img src="<?php echo $this->plugin_base_url?>images/success.gif"><br />
            <?php _e( 'Connected', $domain ) ?>
          </div>
          
          <div class="rez_custom_email_msg">
            <?php _e( 'The API Connection is working', $domain ) ?><br />
            <a id="company_website" href="" target=_blank></a>
          </div>
        
        </div>

        <div id="rez_custom_email_error">
        
          <div class="rez_custom_email_icon">
            <img src="<?php echo $this->plugin_base_url?>images/failure.png"><br />
            <?php _e( 'Failed', $domain ) ?>
          </div>
          
          <div class="rez_custom_email_msg">
            <strong><?php _e( 'The API Connection is NOT working', $domain ) ?></strong><br />
            <div id="connect_problem"> </div>
          </div>
        
        </div>
        
				<dl>
        	<dt><label for="account_cid"><?php _e( 'Account CID', $domain ) ?> </label></dt>
          <dd><input id="account_cid" size=20 type="text" value="<?php echo $this->settings['rezgo_account_cid']; ?>" /></dd>
        	<dt><label for="api_key"><?php _e( 'API key', $domain ) ?> </label></dt>
          <dd><input id="api_key" size=20 type="text" value="<?php echo $this->settings['rezgo_api_key']; ?>" /></dd>
          <dt><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $domain ) ?>" id='submitKeys' /></dt>
          <dd>&nbsp;</dd>
        </dl>
        
        </div> <!-- // close field_contents -->
    </fieldset>
    </div>


	<div class="field_frame">
    <fieldset>

        <legend><?php _e( 'Advanced Settings', $domain ) ?></legend>

        <div class="field_contents">   
        
         <dl>
          <dt><label for="from_name"><?php _e( 'From Name', $domain ) ?> </label></dt>
          <dd><input id="from_name" size=40 type="text" value="<?php echo $this->settings['rezgo_from_name']; ?>" /></dd>
          <dt><label for="from_email"><?php _e( 'From Email Address', $domain ) ?> </label></dt>
          <dd><input id="from_email" size=30 type="text" value="<?php echo $this->settings['rezgo_from_email']; ?>" /></dd>
          <dt><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $domain ) ?>" id='submitFromEmail'/></dt>
          <dd>&nbsp;</dd>
         </dl>
          
        <div id="email_note"><?php _e( 'This is the email that all notifications will be sent from. <br />It will also be the reply email so that when people reply to the email, you will receive it directly.', $domain ) ?></div>    


        </div>

    </fieldset>
    </div>


	<div class="field_frame">
    <fieldset>

        <legend><?php _e( 'Rezgo Webhook Endpoint', $domain ) ?></legend>
		<div class="field_contents">
            <?php _e( 'In order for the plugin to send notifications, it requires that Rezgo send notifications to it. The following URL is the Webhook Endpoint. Copy and paste this URL into your Rezgo Webhook Endpoint settings', $domain ) ?>
    
            <br /><br />
    
            <strong><?php echo home_url("/?rezgo-webhook=true"); ?></strong>
		</div>
    </fieldset>
    </div>


	<div class="field_frame">
    <fieldset>

        <legend><?php _e( 'Syncronize Tour/Option List', $domain ) ?></legend>
        
		<div class="field_contents">
        
        <?php _e( 'Notifications are only sent for tour/options that are active in your Rezgo account. If you have deleted or updated your tours or options in Rezgo, you should update your tour/option list', $domain ) ?>

        <div id="lastUpdatedDiv">
    
               <?php _e( 'Last Updated', $domain ) ?> : <span id="last_updated"><?php echo $this->settings['rezgo_last_updated'] ?></span>
    
        </div>

        <br /><br />

        <input type="submit" class="button-primary" value="<?php _e( 'Update Tour/Option List', $domain ) ?>" id='submitSync'/>
        
		</div>
        
    </fieldset>
	</div>
</form>



<script type="text/javascript" >

jQuery(document).ready(function($) {



    <?php if($this->settings['rezgo_last_updated']) { ?>

      $( "#lastUpdatedDiv" ).show();

    <?php } ?>

    

    // keys

    $( "#submitKeys" ).click(function() {

		$('#rez_custom_email_error').hide();

		$('#rez_custom_email_success').hide();

		var data = { action: 'rezgo_mailer','method': 'ajax_set_keys','account_cid':$('#account_cid').val(), 'api_key':$('#api_key').val()}

		$.post(ajaxurl, data, function(response) {

			if(response.result=='success')

			{

				$('#rez_custom_email_success').show();

				$('#company_website').text(response.company_website);

				$('#company_website').attr('href',response.company_website);

			}

			else

			{

				$('#rez_custom_email_error').show();

				$('#connect_problem').html(response.connect_problem);

			}

		}

		,"json"

		);

		return false;

    });	

    

    $( "#submitFromEmail" ).click(function() {

		var data = { action: 'rezgo_mailer','method': 'ajax_set_from_email','from_email':$('#from_email').val(),'from_name':$('#from_name').val()}

		$.post(ajaxurl, data, function(response) {

			if(response.result=='success')

			{

				alert(response.message);

			}

			else

			{

				alert(response.message);

			}

		}

		,"json"

		);

		return false;

    });	



    $( "#submitSync" ).click(function() {

		var data = { action: 'rezgo_mailer','method': 'ajax_sync_tours'}

		$.post(ajaxurl, data, function(response) {

			if(response.result=='success')

			{

			$('#last_updated').text(response.last_updated);

			$("#lastUpdatedDiv").show();

			alert(response.message);

			}

			else

			{

				alert(response.message);

			}

		}

		,"json"

		);

		return false;

    });	

    

});

</script>