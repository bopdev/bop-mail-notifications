<?php 

add_action( 'admin_init', function(){
  
  add_settings_section(
    'bop_mail_settings',
    __( 'Bop Mail Notifications', 'bop-mail' ),
    function(){ //introduction callable
      //exploit this to update cron
      bop_mail_update_wp_cron();
    },
    'general'
  );
  
  register_setting(
    'general',
    'bop_mail_send_limit',
    function( $dirty = 50 ){
      $clean = abs( (int)$dirty );
      return $clean;
    }
  );
  
  add_settings_field(
    'bop_mail_send_limit_field',
    __( 'Send Limit', 'bop-mail' ),
    function(){
      $setting = get_option( 'bop_mail_send_limit' );
      ?>
      <input type="text" name="bop_mail_send_limit" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
      <small><?php _e( 'maximum number of emails to send at a time.' ) ?></small>
      <?php
    },
    'general',
    'bop_mail_settings'
  );
  
  register_setting(
    'general',
    'bop_mail_use_wp_cron',
    function( $dirty = null ){
      $clean = false;
      if( $dirty ){
        $clean = true;
      }
      
      return $clean;
    }
  );
  
  add_settings_field(
    'bop_mail_use_wp_cron_field',
    __( 'Use WP Cron?', 'bop-mail' ),
    function(){
      $setting = get_option( 'bop_mail_use_wp_cron' );
      ?>
      <input type="checkbox" name="bop_mail_use_wp_cron"<?php echo isset( $setting ) && $setting ? ' checkbox' : ''; ?>>
      <?php
    },
    'general',
    'bop_mail_settings'
  );
  
  register_setting(
    'general',
    'bop_mail_wp_cron_schedule',
    function( $dirty = 'hourly' ){
      $schedules = array_keys( wp_get_schedules() );
      $clean = in_array( $dirty, $schedules ) ? $dirty : $schedules[0];
      return $clean;
    }
  );
  
  add_settings_field(
    'bop_mail_wp_cron_schedule_field',
    __( 'WP Cron Send Frequency', 'bop-mail' ),
    function(){
      $setting = get_option( 'bop_mail_wp_cron_schedule' );
      $schedules = wp_get_schedules();
      ?>
      <select name="bop_mail_wp_cron_schedule">
        <?php foreach( $schedules as $schedule => $schedule_details ): ?>
          <option value="<?php echo esc_attr( $schedule ) ?>"><?php echo esc_html( $schedule_details['display'] ) ?></option>
        <?php endforeach ?>
      </select>
      <?php
    },
    'general',
    'bop_mail_settings'
  );
});
