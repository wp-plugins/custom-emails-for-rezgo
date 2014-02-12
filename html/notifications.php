<?php if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly*/ ?>
<h1><?php _e( 'Custom Emailer For Rezgo', $domain ) ?></h1>

<?php if ( $this->tours ) : ?>

<input type="submit" class="button-primary" value="<?php _e( 'Add Notification', $domain ) ?>" id='submitAddNotify'/>

<?php else: ?>

<?php _e( 'Please, Syncronize Tour/Option List', $domain ) ?>

<?php endif; ?>



<?php if($_GET['delete_ok']){ ?> 

  <div id=rezgo_notification_deleted><?php _e( 'Notification deleted', $domain ) ?></div>

<?php } ?>



<?php if ( $this->notifications ) : ?>

<br /><br />

<?php _e( 'Your active notifications are listed below. Note that you can have only one notification active for each unique tour/option', $domain ) ?>

<br /><br />

<table class="rezgo_notifications_table">

		<thead>

			<tr>

				<th class="rezgo_tour"><span class="nobr"><?php _e( 'Tour/Option', $domain ); ?></span></th>

				<th class="rezgo_author"><span class="nobr"><?php _e( 'Author', $domain ); ?></span></th>

				<th class="rezgo_last_updated"><span class="nobr"><?php _e( 'Last Updated', $domain ); ?></span></th>

				<th class="rezgo_emails_sent"><span class="nobr"><?php _e( 'Emails Sent', $domain ); ?></span></th>

				<th class="rezgo_actions">&nbsp;</th>

			</tr>

		</thead>



		<tbody><?php

			foreach ( $this->notifications as $notification) {

				$user_info = get_userdata($notification->author_userid);

				?><tr class="notification">

					<td class="rezgo_tour">

						<a href="<?php echo add_query_arg( 'edit', $notification->tour_uid, $this->page_url ) ?>"><?php echo $notification->tour_name; ?></a>

					</td>

					<td class="rezgo_author">

						<?php echo $user_info->data->display_name ?>

					</td>

					<td class="rezgo_last_updated">

						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $notification->rezgo_last_updated) ); ?>

					</td>

					<td class="rezgo_emails_sent">

						<?php echo $notification->emails_sent ?>

					</td>

					<td class="rezgo_actions">

						<?php

							$actions = array();

							$actions['editAction'] = array(

								'url'  => add_query_arg( 'edit', $notification->tour_uid, $this->page_url ),

								'name' => __( 'Edit', $domain )

							);

							$actions['deleteAction'] = array(

								'url'  => add_query_arg( 'delete', $notification->tour_uid, $this->page_url ),

								'name' => __( 'Delete', $domain )

							);



							$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );



							foreach( $actions as $key => $action ) {

								echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';

							}

						?>

					</td>

				</tr><?php

			}

		?></tbody>



	</table>

<?php endif; ?>



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