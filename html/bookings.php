<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly*/ ?>
<h1><?php _e( 'Custom Emailer For Rezgo', $domain ) ?></h1>

<h3><?php echo sprintf(__( 'Last %s bookings (received by webhook.php)', $domain ), $this->max_bookings_records ) ?></h3>

<br /><br />

<table class="rezgo_notifications_table">

		<thead>
			<tr>
				<th class="rezgo_date"><span class="nobr"><?php _e( 'Date', $domain ); ?></span></th>
				<th class="rezgo_tour_name"><span class="nobr"><?php _e( 'Tour', $domain ); ?></span></th>
				<th class="rezgo_option_name"><span class="nobr"><?php _e( 'Option', $domain ); ?></span></th>
				<th class="rezgo_trans_num"><span class="nobr"><?php _e( 'Booking #', $domain ); ?></span></th>
				<th class="rezgo_client"><span class="nobr"><?php _e( 'Client', $domain ); ?></span></th>
				<th class="rezgo_email"><span class="nobr"><?php _e( 'Email', $domain ); ?></span></th>
				<th class="rezgo_notification_sent"><span class="nobr"><?php _e( 'Notification?', $domain ); ?></span></th>
				<th class="rezgo_notification_error"><span class="nobr"><?php _e( 'Error', $domain ); ?></span></th>
			</tr>
		</thead>

		<tbody>
    
    <?php

			foreach ( $this->bookings as $book) {

				?><tr class="log_record">

					<td class="rezgo_date">

						<?php echo date_i18n( get_option( 'date_format' ), $book->booking_timestamp ); ?>

					</td>

					<td class="rezgo_tour_name">

						<?php echo $book->tour_name?>

					</td>

					<td class="rezgo_option_name">

						<?php echo $book->option_name?>

					</td>

					<td class="rezgo_trans_num">

						<?php echo $book->trans_num?>

					</td>

					<td class="rezgo_client">

						<?php echo $book->first_name?> <?php echo $book->last_name?>

					</td>

					<td class="rezgo_email">

						<?php echo $book->email?>

					</td>

					<td class="rezgo_notification_sent">

						<?php if($book->notification_sent): ?>

						<img src="<?php echo $this->plugin_base_url?>images/success.gif">

						<?php else: ?>

						<img src="<?php echo $this->plugin_base_url?>images/failure.png">

						<?php endif; ?>

					</td>

					<td class="rezgo_notification_error">

						<?php echo $book->notification_error?>

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