jQuery(document).ready(function($){

	// Uploading files
	var project_gallery_frame;
	var $image_gallery_ids 	= $( '#project_image_gallery' );
	var $project_images 	= $( '#project_images_container ul.project_images' );

	jQuery( '.add_project_images' ).on( 'click', 'a', function( event ) {

		var $el 			= $(this);
		var attachment_ids 	= $image_gallery_ids.val();

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( project_gallery_frame ) {
			project_gallery_frame.open();
			return;
		}

		// Create the media frame.
		project_gallery_frame = wp.media.frames.downloadable_file = wp.media({
			// Set the title of the modal.
			title: woo_projects_admin.gallery_title,
			button: {
				text: woo_projects_admin.gallery_button,
			},
			multiple: true
		});

		// When an image is selected, run a callback.
		project_gallery_frame.on( 'select', function() {

			var selection = project_gallery_frame.state().get( 'selection' );

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();

				if ( attachment.id ) {
					attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

					$project_images.append('\
						<li class="image" data-attachment_id="' + attachment.id + '">\
							<img src="' + attachment.sizes.thumbnail.url + '" />\
								<ul class="actions">\
									<li><a href="#" class="delete" title="'+ woo_projects_admin.delete_image +'">&times;</a></li>\
								</ul>\
							</li>');
					}

			} );

			$image_gallery_ids.val( attachment_ids );
		});

		// Finally, open the modal.
		project_gallery_frame.open();
	});

	// Image ordering
	$project_images.sortable({
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'projects-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css( 'background-color','#f6f6f6' );
		},
		stop:function(event,ui){
			ui.item.removeAttr( 'style' );
		},
		update: function(event, ui) {
			var attachment_ids = '';
				$( '#project_images_container ul li.image' ).css( 'cursor','default' ).each(function() {
				var attachment_id = jQuery(this).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});
				$image_gallery_ids.val( attachment_ids );
		}
	});
	// Remove images
	$( '#project_images_container' ).on( 'click', 'a.delete', function() {
		$(this).closest( 'li.image' ).remove();

		var attachment_ids = '';

		$( '#project_images_container ul li.image' ).css( 'cursor','default' ).each(function() {
			var attachment_id = jQuery(this).attr( 'data-attachment_id' );
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$image_gallery_ids.val( attachment_ids );

		return false;
	} );



	// Instantiates the variable that holds the media library frame.
	var projects_data_frame;

	// Runs when the image button is clicked.
	jQuery( '#project-data' ).on( 'click', '.projects-upload', function( event ) {

		// Prevents the default action from occuring.
		event.preventDefault();

		// store button object
		$button = $(this);

		// If the frame already exists, re-open it.
		if ( projects_data_frame ) {
			projects_data_frame.open();
			return;
		}

		title = $button.data( 'title' ) ? $button.data( 'title' ) : woo_projects_admin.default_title;
		button = $button.data( 'button' ) ? $button.data( 'button' ) : woo_projects_admin.default_button;
		library = $button.data( 'library' ) ? $button.data( 'library' ) : '';

		// Sets up the media library frame
		projects_data_frame = wp.media.frames.projects_data_frame = wp.media({
			title: title,
			button: { text: button },
			library: { type: library }
		});

		// Runs when an image is selected.
		projects_data_frame.on( 'select', function(){

			// Grabs the attachment selection and creates a JSON representation of the model.
			var media_attachment = projects_data_frame.state().get( 'selection' ).first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$button.prev( 'input.projects-upload-field' ).val( media_attachment.url );

		});

		// Opens the media library frame.
		projects_data_frame.open();
	});

});