<?php

//Reject if accessed directly or when not uninstalling
defined( 'WP_UNINSTALL_PLUGIN' ) || die( 'Our survey says: ... X.' );

delete_site_option( 'bop_mail_version' );

//Uninstall code - remove everything with wiping
global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->bop_mail_notifications}" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->bop_mail_notificationmeta}" );

delete_site_option( 'bop_mail_send_limit' );
delete_site_option( 'bop_mail_wp_cron_schedule' );
delete_site_option( 'bop_mail_use_wp_cron' );
