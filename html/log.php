<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly*/ ?>
<h1><?php _e( 'Custom Emailer For Rezgo', $domain ) ?></h1>

<h3><?php echo sprintf(__( 'Last %s log records', $domain ), $this->max_log_records ) ?></h3>

<br /><br />

<table class="rezgo_notifications_table">

		<thead>

			<tr>

				<th class="rezgo_log_datetime"><span class="nobr"><?php _e( 'Date/Time', $domain ); ?></span></th>

				<th class="rezgo_log_message"><span class="nobr"><?php _e( 'Message', $domain ); ?></span></th>

			</tr>

		</thead>



		<tbody><?php

			foreach ( $this->logs as $log) {

				?><tr class="log_record">

					<td class="rezgo_datetime">

						<?php echo date_i18n( get_option( 'date_format' )." ".get_option( 'time_format' ), $log->timestamp ); ?>

					</td>

					<td class="rezgo_log_message">

						<?php echo $log->message?>

					</td>

				</tr><?php

			}

		?></tbody>



	</table>



<script type="text/javascript" >

jQuery(document).ready(function($) {

    

    

    // keys

    $( "#submitAddNotify" ).click(function() {

	window.location= "<?php echo $this->page_url?>&edit=0";

	return false;

    });	

    

    $( ".deleteAction" ).click(function() {

	return confirm("<?php _e( 'Are you sure?', $domain )?>");

    });	

    

});

</script>