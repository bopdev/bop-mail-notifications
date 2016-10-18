<?php 

//Reject if accessed directly
defined( 'ABSPATH' ) || die( 'Our survey says: ... X.' );

function bop_mail_update_wp_cron(){
  $use = get_option( 'bop_mail_use_wp_cron' );
  $schedule = get_option( 'bop_mail_wp_cron_schedule' );
  
  wp_clear_scheduled_hook( 'bop_mail_wp_cron' );
  if( $use )
    wp_schedule_event( time(), $schedule, 'bop_mail_wp_cron' );
}

add_action( 'bop_mail_wp_cron', function(){
  require bop_mail_plugin_path( '/send-mail.php' );
} );
