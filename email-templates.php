<?php 

//Reject if accessed directly
defined( 'ABSPATH' ) || die( 'Our survey says: ... X.' );

add_action( 'init', function(){

	$labels = array(
		'name'                  => _x( 'Bop Email Templates', 'Post Type General Name', 'bop-mail' ),
		'singular_name'         => _x( 'Bop Email Template', 'Post Type Singular Name', 'bop-mail' ),
		'menu_name'             => __( 'Bop Email Templates', 'bop-mail' ),
		'name_admin_bar'        => __( 'Bop Email Templates', 'bop-mail' ),
		'archives'              => __( 'Template Archives', 'bop-mail' ),
		'parent_item_colon'     => __( 'Parent Template:', 'bop-mail' ),
		'all_items'             => __( 'All Templates', 'bop-mail' ),
		'add_new_item'          => __( 'Add New Template', 'bop-mail' ),
		'add_new'               => __( 'Add New', 'bop-mail' ),
		'new_item'              => __( 'New Template', 'bop-mail' ),
		'edit_item'             => __( 'Edit Template', 'bop-mail' ),
		'update_item'           => __( 'Update Template', 'bop-mail' ),
		'view_item'             => __( 'View Template', 'bop-mail' ),
		'search_items'          => __( 'Search Template', 'bop-mail' ),
		'not_found'             => __( 'Not found', 'bop-mail' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'bop-mail' ),
		'featured_image'        => __( 'Featured Image', 'bop-mail' ),
		'set_featured_image'    => __( 'Set featured image', 'bop-mail' ),
		'remove_featured_image' => __( 'Remove featured image', 'bop-mail' ),
		'use_featured_image'    => __( 'Use as featured image', 'bop-mail' ),
		'insert_into_item'      => __( 'Insert into template', 'bop-mail' ),
		'uploaded_to_this_item' => __( 'Uploaded to this template', 'bop-mail' ),
		'items_list'            => __( 'Templates list', 'bop-mail' ),
		'items_list_navigation' => __( 'Templates list navigation', 'bop-mail' ),
		'filter_items_list'     => __( 'Filter templates list', 'bop-mail' ),
	);
	$args = array(
		'label'                 => __( 'Email Template', 'bop-mail' ),
		'description'           => __( 'Email templates for the Bop Mail Notifications Plugin', 'bop-mail' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'author', 'revisions' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 100,
    'menu_icon'             => 'dashicons-email-alt',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => false,
		'capability_type'       => 'page',
    'show_in_rest'           => false
	);
	register_post_type( 'bop_email_template', $args );
  
});

add_action( 'add_meta_boxes', function( $pt ){
  
  add_meta_box(
    'bop_email_template_headers',
    __( 'Email Headers', 'bop-mail' ),
    function( $p ){
      ?>
      <label for="bop-mail-from"><?php _e( 'From: ' ) ?></label>
      <input type="text" name="bop-mail-from" value="<?php echo esc_attr( get_post_meta( $p->ID, 'From_address', true ) ) ?>" class="regular-text"><br>
      <label for="bop-mail-reply-to"><?php _e( 'Reply-To: ' ) ?></label>
      <input type="text" name="bop-mail-reply-to" value="<?php echo esc_attr( get_post_meta( $p->ID, 'Reply-To_address', true ) ) ?>" class="regular-text"><br>
      <label for="bop-mail-ccs"><?php _e( 'Cc: ' ) ?></label>
      <input type="text" name="bop-mail-ccs" value="<?php echo esc_attr( implode( ', ', get_post_meta( $p->ID, 'Cc_address' ) ) ) ?>" class="regular-text"><br>
      <label for="bop-mail-bccs"><?php _e( 'Bcc: ' ) ?></label>
      <input type="text" name="bop-mail-bccs" value="<?php echo esc_attr( implode( ', ', get_post_meta( $p->ID, 'Bcc_address' ) ) ) ?>" class="regular-text"><br>
      <p><?php _e( 'Notes: ', 'bop-mail' ) ?></p>
        <ul>
          <li><?php _e( 'leave empty to omit a header;', 'bop-mail' ) ?></li>
          <li><?php _e( 'fields should be of the form "Person Name &lt;email.address@example.com&gt;";', 'bop-mail' ) ?></li>
          <li><?php _e( 'use commas to separate multiple Ccs and Bccs.', 'bop-mail' ) ?></li>
        </ul>
      <?php
    },
    'bop_email_template',
    'top',
    'high'
  );
  
  add_meta_box(
    'bop_email_template_attachments',
    __( 'Attachments', 'bop-mail' ),
    function( $p ){
      $attachments = get_post_meta( $p->ID, 'bop_mail_attachment_id' );
      $js_attachments = [];
      foreach( $attachments as $attachment ){
        $js_attachments[] = wp_prepare_attachment_for_js( $attachment );
      }
      ?>
      <div class="attachment-thumbs"></div>
      <input type="hidden" name="bop-mail-attachments" value="<?php echo esc_attr( implode( ',', $attachments ) ) ?>">
      <button type="button" class="button button-large" id="bop-mail-attachments-upload-btn"><?php _e( 'Set attachments', 'bop-mail' ) ?></button>
      <script id="bop-mail-attachments-initial-ids" type="application/json"><?php echo json_encode( $js_attachments ) ?></script>
      <?php
    },
    'bop_email_template',
    'side',
    'low'
  );
  
  add_meta_box(
    'bop_email_template_info',
    __( 'Important Information', 'bop-mail' ),
    function( $p ){
      ?>  
      <p><?php _e( 'This template uses the <a href="https://en.wikipedia.org/wiki/Mustache_(template_system)">Mustache templating engine</a> to add details that vary from email to email (e.g., user name and email ). The concept is simple, if you use "{{data_name}}" where you want a particular piece of data, it will insert it there (if the data exists) - e.g., {{email}} might put in the receiver\'s email address.' ) ?></p>
      <?php
    },
    'bop_email_template',
    'normal',
    'high'
  );
  
}, 10, 1 );

add_action('edit_form_after_title', function() {
    if( get_post_type() == 'bop_email_template' )
      do_meta_boxes( 'bop_email_template', 'top', get_post() );
});

//enqueue media upload when on email template page.
add_action( 'admin_enqueue_scripts', function(){
  
  wp_enqueue_media();
  wp_register_script( 'bop-mail-admin', bop_mail_plugin_url( 'assets/js/admin.js' ), ['jquery'], '0.1.0', true );
  wp_register_style( 'bop-mail-admin', bop_mail_plugin_url( 'assets/css/admin.css' ), [], '0.1.0', 'all' );
  
  wp_localize_script( 'bop-mail-admin', 'bop_mail_admin_local', ['attachments_media_modal_title' => __( 'Select Email Attachments', 'bop-mail' ), 'attachments_media_modal_select_button' => __( 'Attach', 'bop-mail' ), 'attachments_file_placeholder_img' => bop_mail_plugin_url( 'assets/img/paperclip.png' )] );
  
  wp_enqueue_script( 'bop-mail-admin' );
  wp_enqueue_style( 'bop-mail-admin' );
  
} );

add_action( 'save_post_bop_email_template', function( $pid ){
  
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  if ( defined('DOING_AJAX') && DOING_AJAX ) return;
  
  // If this is a revision, get real post ID
	if ( $parent_id = wp_is_post_revision( $pid ) ) 
		$pid = $parent_id;
  
  if ( ! current_user_can( 'edit_page', $pid ) ) return;
  
  if( isset( $_POST['bop-mail-from'] ) ){
    update_post_meta( $pid, 'From_address', $_POST['bop-mail-from'] );
  }
  
  if( isset( $_POST['bop-mail-reply-to'] ) ){
    update_post_meta( $pid, 'Reply-To_address', $_POST['bop-mail-reply-to'] );
  }
  
  $multi_adds=['bop-mail-ccs'=>'Cc_address', 'bop-mail-bccs'=>'Bcc_address', 'bop-mail-attachments'=>'bop_mail_attachment_id'];
  foreach( $multi_adds as $post_key => $meta_key ){
    if( isset( $_POST[$post_key] ) ){
      $old_items = get_post_meta( $pid, $meta_key );
      $new_items = explode( ',', $_POST[$post_key] );
      
      //clean input before comparison
      for( $i = 0; $i < count( $new_items ); $i++ ){
        $new_items[$i] = trim( $new_items[$i] );
      }
      
      //check what's new
      $to_add = [];
      foreach( $new_items as $new_item ){
        if( ! in_array( $new_item, $old_items ) ){
          $to_add[] = $new_item;
        }
      }
      
      //replace expired with new or, if no more new, simply delete expired
      $i = 0;
      foreach( $old_items as $old_item ){
        if( ! in_array( $old_item, $new_items ) ){
          if( isset( $to_add[$i] ) ){
            update_post_meta( $pid, $meta_key, $to_add[$i], $old_item );
            ++$i;
          }else{
            delete_post_meta( $pid, $meta_key, $old_item );
          }
        }
      }
      
      //add any remaining new
      while( $i < count( $to_add ) ){
        add_post_meta( $pid, $meta_key, $to_add[$i] );
        ++$i;
      }
    }
  }
  
}, 10, 1 );
