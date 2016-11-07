<?php 

//WP Cron
$schedules = array_keys( wp_get_schedules() );
$schedule = get_option( 'bop_mail_wp_cron_schedule', $schedules[0] );
wp_schedule_event( time(), $schedule, 'bop_mail_wp_cron' );
