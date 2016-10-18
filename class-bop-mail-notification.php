<?php 

//Reject if accessed directly
defined( 'ABSPATH' ) || die( 'Our survey says: ... X.' );

class Bop_Mail_Notification{
  
  public $id = 0;
  
  public $template_id = 0;
  
  public $created;
  
  public $to_address = '';
  
  public $send_count = 0;
  
  public $to_send = true;
  
  public $template;
  
  public $template_vars = [];
  
  public function __construct( $id = 0 ){
    if( $id ){
      $this->load( $id );
    }
    return $this;
  }
  
  public function load( $id ){
    $fields = $this->_fetch_from_db( $id );
    $this->fill_object( $fields );
    return $this;
  }
  
  public function fill_object( $data ){
    if( isset( $data['id'] ) ){
      $this->id = $data['id'];
    }
    
    if( isset( $data['created'] ) ){
      if( is_object( $data['created'] ) && is_a( $data['created'], 'Datetime' ) ){
        $this->created = $data['created'];
      }elseif( is_string( $this->created ) ){
        $this->created = new Datetime( $data['created'] );
      }
    }
    
    if( isset( $data['template_id'] ) ){
      $this->template_id = $data['template_id'];
    }
    
    if( isset( $data['to_address'] ) ){
      $this->to_address = $data['to_address'];
    }
    
    if( isset( $data['send_count'] ) ){
      $this->send_count = $data['send_count'];
    }
    
    if( isset( $data['to_send'] ) ){
      $this->to_send = $data['to_send'];
    }
    
    return $this;
  }
  
  protected function _fetch_from_db( $id ){
    global $wpdb;
    $fields = $wpdb->get_row(
      $wpdb->prepare(
        "SELECT t.notification_id AS id,
          t.template_id AS template_id,
          t.created AS created,
          t.to_address AS to_address,
          t.send_count AS send_count,
          t.to_send AS to_send
        FROM {$wpdb->bop_mail_notifications} AS t
        WHERE t.notification_id = %d
        LIMIT 1",
        $id
      ),
      ARRAY_A
    );
    return $fields;
  }
  
  public function insert(){
    global $wpdb;
    
    if( ! $this->template_id || ! $this->to_address )
      return false;
    
    $insert_fields = ['template_id'=>$this->template_id, 'to_address'=>$this->to_address, 'send_count'=>$this->send_count, 'to_send'=>$this->to_send];
    
    $formats = ['%d', '%s', '%d', '%d'];
    
    $this->id = $wpdb->insert( $wpdb->bop_mail_notifications, $insert_fields, $formats );
    return $this;
  }
  
  public function update(){
    global $wpdb;
    
    if( ! $this->id )
      return false;
      
    $update_fields = ['template_id'=>$this->template_id, 'to_address'=>$this->to_address, 'send_count'=>$this->send_count, 'to_send'=>$this->to_send];
    
    $formats = ['%d', '%s', '%d', '%d'];
    
    return $wpdb->update( $wpdb->bop_mail_notifications, $update_fields, ['notification_id'=>$this->id], $formats, ['%d'] );
  }
  
  public function get_meta( $k = '', $single = false ){
    if( ! $this->id ) return false;
    return get_metadata( 'bop_mail_notification', $this->id, $k, $single );
  }
  
  public function update_meta( $k, $v, $prev = '' ){
    if( ! $this->id ) return false;
    return update_metadata( 'bop_mail_notification', $this->id, $k, $v, $prev );
  }
  
  public function add_meta( $k, $v, $unique = false ){
    if( ! $this->id ) return false;
    return add_metadata( 'bop_mail_notification', $this->id, $k, $v, $unique );
  }
  
  public function delete_meta( $k, $v = '' ){
    if( ! $this->id ) return false;
    return delete_metadata( 'bop_mail_notification', $this->id, $k, $v );
  }
  
  public function update_multi_meta( $k, $new_items ){
    $old_items = $this->get_meta( $k );
    
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
          $this->update_meta( $k, $to_add[$i], $old_item );
          ++$i;
        }else{
          $this->delete_meta( $k, $old_item );
        }
      }
    }
    
    //add any remaining new
    while( $i < count( $to_add ) ){
      $this->add_meta( $k, $to_add[$i] );
      ++$i;
    }
  }
  
  public function set_headers( $headers ){
    if( ! $this->id )
      return false;
    
    if( isset( $headers['From'] ) ){
      $this->update_meta( 'From_address', $headers['From'] );
    }
    if( isset( $headers['Reply-To'] ) ){
      $this->update_meta( 'Reply-To_address', $headers['Reply-To'] );
    }
    
    $multi_adds=['Ccs'=>'Cc_address', 'Bccs'=>'Bcc_address'];
    foreach( $multi_adds as $key => $meta_key ){
      if( isset( $headers[$key] ) ){
        $this->update_multi_meta( $meta_key, $headers[$key] );
      }
    }
    
    return $this;
  }
  
  public function set_attachments( $atts ){
    $this->update_multi_meta( $meta_key, $atts );
    return $this;
  }
  
  public function get_template(){
    if( ! $this->template && $this->template_id ){
      $this->template = get_post( $this->template_id );
    }
    return $this->template;
  }
  
  public function set_template_vars( $vars ){
    foreach( $vars as $key => $var ){
      $this->update_meta( '_var_' . $key, $var );
    }
    return $this;
  }
  
  public function get_template_vars(){
    $meta = $this->get_meta();
    
    $vars = [];
    foreach( $meta as $key => $val ){
      if( strpos( $key, '_var_' ) === 0 )
        $vars[substr( $key, 5 )] = $val[0];
    }
    
    return $vars;
  }
  
  public static function fill_template_vars( $content, $vars ){
    require_once bop_mail_plugin_path( 'templating-engines/Mustache/Autoloader.php' );
    if( ! class_exists( 'Mustache_Engine' ) )
      Mustache_Autoloader::register();
    
    $m = new Mustache_Engine();
    return $m->render( $content, $vars );
  }
  
  public function send(){
    
    $tmpl = $this->get_template();
    
    $subject = self::fill_template_vars( $tmpl->post_title, $this->get_template_vars() );
    $content = self::fill_template_vars( $tmpl->post_content, $this->get_template_vars() );
    $headers = [];
    
    $from = $this->get_meta( 'From_address', true );
    $from = $from ? $from : get_post_meta( $tmpl->ID, 'From_address', true );
    if( $from ){
      $headers[] = 'From: ' . $from;
    }
    
    $reply_to = $this->get_meta( 'Reply-To_address', true );
    $reply_to = $reply_to ? $reply_to : get_post_meta( $tmpl->ID, 'Reply-To_address', true );
    if( $reply_to ){
      $headers[] = 'Reply-To: ' . $reply_to;
    }
    
    $ccs = (array)$this->get_meta( 'Cc_address' );
    $ccs = array_merge( $ccs, (array)get_post_meta( $tmpl->ID, 'Cc_address' ) );
    if( $ccs ){
      $headers[] = 'Cc: ' . implode( ', ', (array)$ccs );
    }
    
    $bccs = (array)$this->get_meta( 'Bcc_address' );
    $bccs = array_merge( $bccs, (array)get_post_meta( $tmpl->ID, 'Bcc_address' ) );
    if( $bccs ){
      $headers[] = 'Bcc: ' . implode( ', ', (array)$bccs );
    }
    
    $attachments = [];
    $attachment_ids = (array)$this->get_meta( 'bop_mail_attachment_id' );
    $attachment_ids = array_merge( $attachment_ids, (array)get_post_meta( $tmpl->ID, 'bop_mail_attachment_id' ) );
    if( $attachment_ids ){
      foreach( $attachment_ids as $aid ){
        $attachments[] = get_attached_file( $aid );
      }
    }
    
    echo $success = wp_mail( $this->to_address, $subject, $content, $headers, $attachments );
    
    if( $success ){
      ++$this->send_count;
      $this->to_send = false;
      $this->update();
    }
    
    return $success;
  }
  
}
