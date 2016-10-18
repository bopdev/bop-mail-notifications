<?php 

//Reject if accessed directly
defined( 'ABSPATH' ) || die( 'Our survey says: ... X.' );

function bop_mail_plugin_url( $path = '' ){
  return plugin_dir_url( __FILE__ ) . ltrim( $path, '/' );
}

require_once bop_mail_plugin_path( 'email-templates.php' );
require_once bop_mail_plugin_path( 'class-bop-mail-notification.php' );
require_once bop_mail_plugin_path( 'settings.php' );
require_once bop_mail_plugin_path( 'wp-cron.php' );
