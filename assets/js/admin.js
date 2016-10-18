(function($, local){
  $(document).ready( function(){
      
      var $thumbs = $('.attachment-thumbs');
      var $attachmentsInput = $('[name="bop-mail-attachments"]');
      
      if( wp.media.frames.bopMailAttachmentsFrame !== void(0) ){
        wp.media.frames.bopMailAttachmentsFrame.open();
        return;
      }
      
      var frame = wp.media.frames.bopMailAttachmentsFrame = wp.media(
        {
          title: local.attachments_media_modal_title,
            button: {
              text: local.attachments_media_modal_select_button
            },
            multiple: true
        }
      );
      
      function updateAttachmentThumbs(attachments){
        $thumbs.empty();
        
        $.each(attachments, function(i,attachment){
          var $imgContainer = $('<div class="attachment-thumb"></div>');
          var $img = $('<img>').attr('alt', attachment.attributes.title);
          if(attachment.attributes.type == 'image'){
            $img.attr('src', attachment.attributes.url);
          }else{
            $img.attr('src', local.attachments_file_placeholder_img);
          }
          $thumbs.append($imgContainer);
          $imgContainer.data('id', attachment.attributes.id).append($img).append('<span>'+attachment.attributes.title+'('+attachment.attributes.subtype+')</span>');
        });
      }
      
      function updateAttachmentIds(attachments){
        ids = [];
        $.each(attachments, function(i,attachment){
          ids.push(attachment.attributes.id);
        });
        $attachmentsInput.data('ids', ids).val(ids.join(','));
      }
      
      //initial setup
      var attachments = [];
      $.each($.parseJSON($('#bop-mail-attachments-initial-ids').text()), function(i,att){
        var attachment = wp.media.attachment(att.id);
        attachment.set(att);
        attachments.push(attachment);
      });
      updateAttachmentThumbs(attachments);
      updateAttachmentIds(attachments);
      
      //when changing
      $('#bop-mail-attachments-upload-btn').click(function() {
        frame.open();
        
        var selection = frame.state().get('selection');
        $.each($attachmentsInput.data('ids'), function(i,id){
          selection.add(wp.media.attachment(id));
        });
        
        frame.on('select', function() {
          var attachments = selection.toArray();
          updateAttachmentThumbs(attachments);
          updateAttachmentIds(attachments);
          frame.close();
        });
      });

  });
})(jQuery, bop_mail_admin_local);
