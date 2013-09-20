<?php
/*-----------------------------------------------------------------------------------*/
/* Get Post image attachments */
/*-----------------------------------------------------------------------------------*/
/*
Description:

This function will get all the attached post images that have been uploaded via the
WP post image upload and return them in an array.

*/
if ( ! function_exists( 'woo_get_post_images' ) ) {
function woo_get_post_images($offset = 1) {

// Arguments
$repeat = 100; 				// Number of maximum attachments to get
$photo_size = 'large';		// The WP "size" to use for the large image

global $post;

$output = array();

$id = get_the_id();
$attachments = get_children( array(
'post_parent' => $id,
'numberposts' => $repeat,
'post_type' => 'attachment',
'post_mime_type' => 'image',
'order' => 'ASC',
'orderby' => 'menu_order date' )
);
if ( !empty($attachments) ) :
	$output = array();
	$count = 0;
	foreach ( $attachments as $att_id => $attachment ) {
		$count++;
		if ($count <= $offset) continue;
		$url = wp_get_attachment_image_src($att_id, $photo_size, true);
			$output[] = array( 'url' => $url[0], 'caption' => $attachment->post_excerpt, 'id' => $att_id, 'alt' => get_post_meta( $att_id, '_wp_attachment_image_alt', true ) );
	}
endif;
return $output;
} // End woo_get_post_images()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio Navigation */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_portfolio_navigation' ) ) {
	function woo_portfolio_navigation ( $galleries ) {

		// Sanity check.
		if ( ! is_array( $galleries ) || ( count( $galleries ) <= 0 ) ) { return; }

		global $woo_options;

		$settings = array(
						'id' => 'port-tags',
						'label' => __( 'Select a category:', 'woothemes' ),
						'display_all' => true
						 );

		$settings = apply_filters( 'woo_portfolio_navigation_args', $settings );

		// Prepare the anchor tags of the various gallery items.
		$gallery_anchors = '';

		foreach ( $galleries as $g ) {
			$gallery_anchors .= '<a href="#' . $g->slug . '" rel="' . $g->slug . '" class="navigation-slug-' . $g->slug . ' navigation-id-' . $g->term_id . '">' . $g->name . '</a>' . "\n";
		}

		$html = '<div id="' . $settings['id'] . '" class="port-tags">' . "\n";
			$html .= '<div class="fl">' . "\n";
				$html .= '<span class="port-cat">' . "\n";

				// Display label, if one is set.
				if ( $settings['label'] != '' ) { $html .= $settings['label'] . ' '; }

				// Display "All", if set to "true".
				if ( $settings['display_all'] == 'all' ) { $html .= '<a href="#" rel="all" class="current">' . __( 'All', 'woothemes' ) . '</a> '; }

				// Add the gallery anchors in.
				$html .= $gallery_anchors;

				$html .= '</span>' . "\n";
			$html .= '</div><!--/.fl-->' . "\n";
			$html .= '<div class="fix"></div>' . "\n";
		$html .= '</div><!--/#' . $settings['id'] . ' .port-tags-->' . "\n";


		$html = apply_filters( 'woo_portfolio_navigation', $html );

		echo $html;

	} // End woo_portfolio_navigation()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio Item Extras (Testimonial and Link) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_portfolio_item_extras' ) ) {
	function woo_portfolio_item_extras ( $data ) {

		$settings = array(
							'id' => 'extras',
							'display_button' => true
						 );

		// Allow child themes/plugins to filter these settings.
		$settings = apply_filters( 'woo_portfolio_item_extras_settings', $settings, $data );

		$html = '';

		$html .= '<div id="' . $settings['id'] . '">' . "\n";

		if ( $data['display_url'] != '' ) { $html .= '<a class="button" href="' . $data['display_url'] . '">' . __( 'Visit Website', 'woothemes' ) . '</a>' . "\n"; }

		if ( $data['testimonial'] != '' ) { $html .= '<blockquote>' . $data['testimonial'] . '</blockquote>' . "\n"; } // End IF Statement

		if ( $data['testimonial_author'] != '' ) {
			$html .= '<cite>&ndash; ' . $data['testimonial_author'] . "\n";
				if ( $data['display_url'] != '' ) { $html .= ' - <a href="' . $data['display_url'] . '" target="_blank">' . $data['display_url'] . '</a>' . "\n"; }
			$html .= '</cite>' . "\n";
		} // End IF Statement

			$html .= '</div><!--/#extras-->' . "\n";

			// Allow child themes/plugins to filter this HTML.
			$html = apply_filters( 'woo_portfolio_item_extras_html', $html, $data );

			echo $html;

	} // End woo_portfolio_item_extras()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio Item Settings */
/* @uses woo_portfolio_image_dimensions() */
/*-----------------------------------------------------------------------------------*/

	if ( ! function_exists( 'woo_portfolio_item_settings' ) ) {
	function woo_portfolio_item_settings ( $id ) {

		global $woo_options;

		// Sanity check.
		if ( ! is_numeric( $id ) ) { return; }

		$website_layout = 'two-col-left';
		$website_width = '960px';

		if ( isset( $woo_options['woo_layout'] ) ) { $website_layout = $woo_options['woo_layout']; }
		if ( isset( $woo_options['woo_layout_width'] ) ) { $website_width = $woo_options['woo_layout_width']; }

		$dimensions = woo_portfolio_image_dimensions( $website_layout, $website_width );

		$width = $dimensions['width'];
		$height = $dimensions['height'];

		$enable_gallery = false;
		if ( isset( $woo_options['woo_portfolio_gallery'] ) ) { $enable_gallery = $woo_options['woo_portfolio_gallery']; }

		$settings = array(
							'large' => '',
							'caption' => '',
							'rel' => '',
							'gallery' => array(),
							'css_classes' => 'group post portfolio-img',
							'embed' => '',
							'enable_gallery' => $enable_gallery,
							'testimonial' => '',
							'testimonial_author' => '',
							'display_url' => '',
							'width' => $width,
							'height' => $height
						 );

		$meta = get_post_custom( $id );

		// Check if there is a gallery in post.
		// woo_get_post_images is offset by 1 by default. Setting to offset by 0 to show all images.

    	$large = '';
    	if ( isset( $meta['portfolio-image'][0] ) ) {
    		$large = $meta['portfolio-image'][0];
    	}

    	$caption = '';

    	if ( $settings['enable_gallery'] == 'true' ) {

        	$gallery = woo_get_post_images( '0' );
        	if ( $gallery ) {
        		// Get first uploaded image in gallery
        		$large = $gallery[0]['url'];
        		$caption = $gallery[0]['caption'];
            }

        } // End IF Statement

        // If we only have one image, disable the gallery functionality.
        if ( is_array( $gallery ) && ( count( $gallery ) <= 1 ) ) {
       		$settings['enable_gallery'] = 'false';
        }

        // Check for a post thumbnail, if support for it is enabled.
        if ( ( $woo_options['woo_post_image_support'] == 'true' ) && current_theme_supports( 'post-thumbnails' ) ) {
        	$image_id = get_post_thumbnail_id( $id );
        	if ( intval( $image_id ) > 0 ) {
        		$large_data = wp_get_attachment_image_src( $image_id, 'large' );
        		if ( is_array( $large_data ) ) {
        			$large = $large_data[0];
        		}
        	}
        }

        // See if lightbox-url custom field has a value
        if ( isset( $meta['lightbox-url'] ) && ( $meta['lightbox-url'][0] != '' ) ) {
        	$large = $meta['lightbox-url'][0];
        }

        // Set rel on anchor to show lightbox
        if ( is_array( $gallery ) && ( count( $gallery ) <= 1 ) ) {
      		$rel = 'rel="lightbox"';
		} else {
	  		$rel = 'rel="lightbox['. $id .']"';
		}

		// Create CSS classes string.
		$css = '';
		$galleries = array();
		$terms = get_the_terms( $id, 'portfolio-gallery' );
		if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) { foreach ( $terms as $t ) { $galleries[] = $t->slug; } }
		$css = join( ' ', $galleries );

		// If on the single item screen, check for a video.
		if ( is_singular() ) { $settings['embed'] = woo_embed( 'width=540' ); }

		// Add testimonial information.
		if ( isset( $meta['testimonial'] ) && ( $meta['testimonial'][0] != '' ) ) {
			$settings['testimonial'] = $meta['testimonial'][0];
		}

		if ( isset( $meta['testimonial_author'] ) && ( $meta['testimonial_author'][0] != '' ) ) {
			$settings['testimonial_author'] = $meta['testimonial_author'][0];
		}

		// Look for a custom display URL of the portfolio item (used if it's a website, for example)
		if ( isset( $meta['url'] ) && ( $meta['url'][0] != '' ) ) {
			$settings['display_url'] = $meta['url'][0];
		}

		// Assign the values we have to our array.
		$settings['large'] = $large;
		$settings['caption'] = $caption;
		$settings['rel'] = $rel;
		$settings['gallery'] = $gallery;
		$settings['css_classes'] .= ' ' . $css;

		// Disable "enable_gallery" option is gallery is empty.
		if ( ! is_array( $settings['gallery'] ) || ( $settings['gallery'] == '' ) || ( count( $settings['gallery'] ) <= 0 ) ) {
			$settings['enable_gallery'] = 'false';
		}

		// Allow child themes/plugins to filter these settings.
		$settings = apply_filters( 'woo_portfolio_item_settings', $settings, $id );

		return $settings;

	} // End woo_portfolio_item_settings()
}
?>