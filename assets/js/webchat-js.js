jQuery(function ($) {
	/*
	 * Select/Upload image(s) event
	 */
    $('body').on('click', '.webchat_upload_image_button', function (e) {
        e.preventDefault();

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    // uncomment the next line if you want to attach image to the current post
                    // uploadedTo : wp.media.view.settings.post.id, 
                    type: 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function () { // it also has "open" and "close" events 
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(button).removeClass('webchat_upload_image_button button button-primary').html('<img class="true_pre_image" src="' + attachment.url + '" style="width:60px;border-radius:50px;display:block;" />').next().val(attachment.id).next().show();
                /* if you sen multiple to true, here is some code for getting the image IDs
                var attachments = frame.state().get('selection'),
                    attachment_ids = new Array(),
                    i = 0;
                attachments.each(function(attachment) {
                        attachment_ids[i] = attachment['id'];
                    console.log( attachment );
                    i++;
                });
                */
            })
                .open();
    });

	/*
	 * Remove image event
	 */
    $('body').on('click', '.webchat_remove_image_button', function () {
        $(this).hide().prev().val('').prev().addClass('webchat_upload_image_button button button-primary').html('Upload image');
        return false;
    });

    $('.bcpcolor').bcp();
    $('#chatResponseTextColor').on('pcb.refresh', function (e) {
        let color = $(this).bcp('color');
        if (color.value) {
            $('#chatResponseTextColorInput').val(color.value)
            $(this).css({
                backgroundColor: color.value,
                borderColor: color.value,
                color: color.dark ? '#fff' : '#000'
            });
        }
    });

    $('#chatResponseColor').on('pcb.refresh', function (e) {
        let color = $(this).bcp('color');
        if (color.value) {
            $('#chatResponseColorInput').val(color.value)
            $(this).css({
                backgroundColor: color.value,
                borderColor: color.value,
                color: color.dark ? '#fff' : '#000'
            });
        }
    });

    $('#headerTextColor').on('pcb.refresh', function (e) {
        let color = $(this).bcp('color');
        if (color.value) {
            $('#headerTextColorInput').val(color.value)
            $(this).css({
                backgroundColor: color.value,
                borderColor: color.value,
                color: color.dark ? '#fff' : '#000'
            });
        }
    });

    $('#headerBGColor').on('pcb.refresh', function (e) {
        let color = $(this).bcp('color');
        if (color.value) {
            $('#headerBGColorInput').val(color.value)
            $(this).css({
                backgroundColor: color.value,
                borderColor: color.value,
                color: color.dark ? '#fff' : '#000'
            });
        }
    });


});