<?php 

//Reject if accessed directly
defined( 'BOP_PLUGIN_UPDATING' ) || die( 'Our survey says: ... X.' );

//Update (or install) script

//DB
global $wpdb;

//Guide: https://codex.wordpress.org/Creating_Tables_with_Plugins
//Check https://core.trac.wordpress.org/browser/trunk/src/wp-admin/includes/schema.php#L0 for example sql


$charset_collate = $wpdb->get_charset_collate();
$max_index_length = 191;

$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->bop_mail_notifications} (
		notification_id bigint(20) unsigned NOT NULL auto_increment,
		template_id bigint(20) unsigned NOT NULL default '0',
    created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		to_address varchar(255) NOT NULL default '',
		send_count smallint(5) unsigned NOT NULL default '0',
    to_send boolean NOT NULL default TRUE,
    PRIMARY KEY  (notification_id),
		KEY template_id (template_id),
		KEY to_address (to_address)
	) $charset_collate;
  CREATE TABLE IF NOT EXISTS {$wpdb->bop_mail_notificationmeta} (
    meta_id bigint(20) unsigned NOT NULL auto_increment,
    bop_mail_notification_id bigint(20) unsigned NOT NULL default '0',
    meta_key varchar(255) default NULL,
    meta_value longtext,
    PRIMARY KEY  (meta_id),
    KEY notification_id (notification_id),
    KEY meta_key (meta_key($max_index_length))
  ) $charset_collate;
		";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

unset( $sql, $charset_collate );

add_option( 'bop_mail_send_limit', 50, '', 'no' );

$schedules = array_keys( wp_get_schedules() );
add_option( 'bop_mail_wp_cron_schedule', $schedules[0], '', 'yes' );

add_option( 'bop_mail_use_wp_cron', true, '', 'yes' );


//WP Cron
wp_schedule_event( time(), $schedules[0], 'bop_mail_wp_cron' );
