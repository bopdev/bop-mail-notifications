<?php 

//Reject if accessed directly
defined( 'ABSPATH' ) || die( 'Our survey says: ... X.' );

global $wpdb;

$limit = get_option( 'bop_mail_send_limit', 10 );

$notifs = $wpdb->get_results( $wpdb->prepare(
    "SELECT t.notification_id AS id,
      t.template_id AS template_id,
      t.created AS created,
      t.to_address AS to_address,
      t.send_count AS send_count,
      t.to_send AS to_send
    FROM {$wpdb->bop_mail_notifications} AS t
    WHERE t.to_send = TRUE
    LIMIT %d",
    $limit
  ),
  ARRAY_A
);

for( $i = 0; $i < count( $notifs ); $i++ ){
  $mail = new Bop_Mail_Notification();
  $mail->fill_object( $notifs[$i] );
  $mail->send();
}
