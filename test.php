<?php 

require_once( '../../../wp-load.php' );

global $wpdb;
echo '<pre>';
//$wpdb->query( "ALTER TABLE {$wpdb->bop_mail_notificationmeta} CHANGE notification_id bop_mail_notification_id bigint(20) unsigned NOT NULL default '0'" );
var_dump( $wpdb->get_results( "SELECT * FROM {$wpdb->bop_mail_notificationmeta}" ) );
echo '</pre>';

$mail2 = new Bop_Mail_Notification( 1 );
var_dump( $mail2 );
$tmpl = $mail2->get_template();
$subject = Bop_Mail_Notification::fill_template_vars( $tmpl->post_title, $mail2->get_template_vars() );
$content = Bop_Mail_Notification::fill_template_vars( $tmpl->post_content, $mail2->get_template_vars() );

echo '<pre>' . $subject;
echo '<br><br>';
echo $content . '</pre>';

//$mail2->send();

