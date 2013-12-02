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
/* Woo Projects Navigation */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_projects_navigation' ) ) {
	function woo_projects_navigation ( $galleries ) {

		// Sanity check.
		if ( ! is_array( $galleries ) || ( count( $galleries ) <= 0 ) ) { return; }

		global $woo_options;

		$settings = array(
						'id' => 'port-tags',
						'label' => __( 'Select a category:', 'woothemes' ),
						'display_all' => true
						 );

		$settings = apply_filters( 'woo_project_navigation_args', $settings );

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


		$html = apply_filters( 'woo_project_navigation', $html );

		echo $html;

	} // End woo_project_navigation()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Project Item Extras (Testimonial and Link) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_project_item_extras' ) ) {
	function woo_project_item_extras ( $data ) {

		$settings = array(
							'id' => 'extras',
							'display_button' => true
						 );

		// Allow child themes/plugins to filter these settings.
		$settings = apply_filters( 'woo_project_item_extras_settings', $settings, $data );

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
			$html = apply_filters( 'woo_project_item_extras_html', $html, $data );

			echo $html;

	} // End woo_project_item_extras()
}