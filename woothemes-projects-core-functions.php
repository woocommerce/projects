<?php
/**
 * WooThemes Projects Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	Woothemes_Projects/Functions
 * @version     1.0.0
 */

if ( ! function_exists( 'woothemes_projects_get_page_id' ) ) {

	/**
	 * WooThemes Projects page IDs
	 *
	 * retrieve page ids - used for showcase
	 *
	 * returns -1 if no page is found
	 *
	 * @access public
	 * @param string $page
	 * @return int
	 */
	function woothemes_projects_get_page_id ( $page ) {
		$page = apply_filters( 'woothemes_projects_get_' . $page . '_page_id', get_option( 'woothemes_projects_' . $page . '_page_id' ) );

		return $page ? $page : -1;
	} // End woothemes_projects_get_page_id()
}

if ( ! function_exists( 'woothemes_projects_get_image' ) ) {

	/**
	 * Get the image for the given ID.
	 * @param  int 				$id   Post ID.
	 * @param  string/array/int $size Image dimension. (default: "projects-thumbnail")
	 * @since  1.0.0
	 * @return string       	<img> tag.
	 */
	function woothemes_projects_get_image ( $id, $size = 'projects-thumbnail' ) {
		$response = '';

		if ( has_post_thumbnail( $id ) ) {
			// If not a string or an array, and not an integer, default to 150x9999.
			if ( is_int( $size ) || ( 0 < intval( $size ) ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 150, 9999 );
			}
			$response = get_the_post_thumbnail( intval( $id ), $size );
		}

		return $response;
	} // End woothemes_projects_get_image()

}

/**
 * is_woothemes_projects - Returns true if on a page which uses WooThemes Projects templates
 *
 * @access public
 * @return bool
 */
function is_woothemes_projects () {
	return ( is_showcase() || is_project_category() || is_project() ) ? true : false;
} // End is_woothemes_projects()

if ( ! function_exists( 'is_showcase' ) ) {

	/**
	 * is_showcase - Returns true when viewing the project type archive (showcase).
	 *
	 * @access public
	 * @return bool
	 */
	function is_showcase() {
		return ( is_post_type_archive( 'project' ) || is_page( woothemes_projects_get_page_id( 'showcase' ) ) ) ? true : false;
	} // End is_showcase()
}

if ( ! function_exists( 'is_project_taxonomy' ) ) {

	/**
	 * is_project_taxonomy - Returns true when viewing a project taxonomy archive.
	 *
	 * @access public
	 * @return bool
	 */
	function is_project_taxonomy() {
		return is_tax( get_object_taxonomies( 'project' ) );
	} // End is_project_taxonomy()
}

if ( ! function_exists( 'is_project_category' ) ) {

	/**
	 * is_project_category - Returns true when viewing a project category.
	 *
	 * @access public
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_project_category( $term = '' ) {
		return is_tax( 'project-category', $term );
	} // End is_project_category()
}

if ( ! function_exists( 'is_project' ) ) {

	/**
	 * is_project - Returns true when viewing a single project.
	 *
	 * @access public
	 * @return bool
	 */
	function is_project() {
		return is_singular( array( 'project' ) );
	} // End is_project()
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @access public
	 * @return bool
	 */
	function is_ajax() {
		if ( defined('DOING_AJAX') )
			return true;

		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
	}
}

/**
 * Get template part (for templates like the showcase-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function woothemes_projects_get_template_part( $slug, $name = '' ) {
	global $woothemes_projects;
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/woothemes-projects/slug-name.php
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php", "{$woothemes_projects->template_url}{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( !$template && $name && file_exists( $woothemes_projects->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
		$template = $woothemes_projects->plugin_path() . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woothemes_projects/slug.php
	if ( !$template )
		$template = locate_template( array ( "{$slug}.php", "{$woothemes_projects->template_url}{$slug}.php" ) );

	if ( $template )
		load_template( $template, false );
} // End woothemes_projects_get_template_part()


/**
 * Get other templates and including the file.
 *
 * @access public
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function woothemes_projects_get_template ( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	global $woothemes_projects;

	if ( $args && is_array($args) )
		extract( $args );

	$located = woothemes_projects_locate_template( $template_name, $template_path, $default_path );

	do_action( 'woothemes_projects_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'woothemes_projects_after_template_part', $template_name, $template_path, $located, $args );
} // End woothemes_projects_get_template()


/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function woothemes_projects_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	global $woothemes_projects;

	if ( ! $template_path ) $template_path = $woothemes_projects->template_url;
	if ( ! $default_path ) $default_path = $woothemes_projects->plugin_path() . '/templates/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters('woothemes_projects_locate_template', $template, $template_name, $template_path);
} // End woothemes_projects_locate_template()

/**
 * Filter to allow project-category in the permalinks for projects.
 *
 * @access public
 * @param string $permalink The existing permalink URL.
 * @param object $post
 * @return string
 */
function woothemes_projects_project_post_type_link( $permalink, $post ) {
    // Abort if post is not a project
    if ( $post->post_type !== 'project' )
    	return $permalink;

    // Abort early if the placeholder rewrite tag isn't in the generated URL
    if ( false === strpos( $permalink, '%' ) )
    	return $permalink;

    // Get the custom taxonomy terms in use by this post
    $terms = get_the_terms( $post->ID, 'project-category' );

    if ( empty( $terms ) ) {
    	// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
        $project_cat = _x( 'uncategorized', 'slug', 'woothemes-projects' );
    } else {
    	// Replace the placeholder rewrite tag with the first term's slug
        $first_term = array_shift( $terms );
        $project_cat = $first_term->slug;
    }

    $find = array(
    	'%year%',
    	'%monthnum%',
    	'%day%',
    	'%hour%',
    	'%minute%',
    	'%second%',
    	'%post_id%',
    	'%category%',
    	'%project_category%'
    );

    $replace = array(
    	date_i18n( 'Y', strtotime( $post->post_date ) ),
    	date_i18n( 'm', strtotime( $post->post_date ) ),
    	date_i18n( 'd', strtotime( $post->post_date ) ),
    	date_i18n( 'H', strtotime( $post->post_date ) ),
    	date_i18n( 'i', strtotime( $post->post_date ) ),
    	date_i18n( 's', strtotime( $post->post_date ) ),
    	$post->ID,
    	$project_cat,
    	$project_cat
    );

    $replace = array_map( 'sanitize_title', $replace );

    $permalink = str_replace( $find, $replace, $permalink );

    return $permalink;
} // End woothemes_projects_project_post_type_link()

add_filter( 'post_type_link', 'woothemes_projects_project_post_type_link', 10, 2 );

/**
 * Get the placeholder image URL for projects etc
 *
 * @access public
 * @since  1.0.0
 * @return string
 */
function woothemes_projects_placeholder_img_src () {
	global $woothemes_projects;

	return apply_filters('woothemes_projects_placeholder_img_src', $woothemes_projects->plugin_url() . '/assets/images/placeholder.png' );
} // End woothemes_projects_placeholder_img_src()

/**
 * Get the placeholder image
 *
 * @access public
 * @since  1.0.0
 * @return string
 */
function woothemes_projects_placeholder_img ( $size = 'project-thumbnail' ) {
	global $woothemes_projects;

	$dimensions = $woothemes_projects->get_image_size( $size );

	return apply_filters('woothemes_projects_placeholder_img', '<img src="' . woothemes_projects_placeholder_img_src() . '" alt="Placeholder" width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '" />' );
} // End woothemes_projects_placeholder_img()

/**
 * woothemes_projects_get_gallery_attachment_ids function.
 *
 * @access public
 * @return array
 */
function woothemes_projects_get_gallery_attachment_ids ( $post_id = 0 ) {
	global $post;
	if ( 0 == $post_id ) $post_id = get_the_ID();
	$project_image_gallery = get_post_meta( $post_id, '_project_image_gallery', true );
	if ( '' == $project_image_gallery ) {
		// Backwards compat
		$attachment_ids = get_posts( 'post_parent=' . intval( $post_id ) . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids' );
		$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
		$project_image_gallery = implode( ',', $attachment_ids );
	}
	return array_filter( (array) explode( ',', $project_image_gallery ) );
} // End woothemes_projects_get_gallery_attachment_ids()


/**
 * Add body classes for Projects pages
 *
 * @param  array $classes
 * @return array
 */
function woo_projects_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_woothemes_projects() ) {
		$classes[] = 'projects';
		$classes[] = 'projects-page';
	}

	return array_unique( $classes );
}


/**
 * Enqueue styles
 */
function woothemes_projects_script() {

	wp_register_style( 'woothemes-projects-styles', plugins_url( '/assets/css/woo-projects.css', __FILE__ ) );
	wp_register_style( 'woothemes-projects-handheld', plugins_url( '/assets/css/woo-projects-handheld.css', __FILE__ ) );
	wp_register_style( 'woothemes-projects-general', plugins_url( '/assets/css/woo-projects-general.css', __FILE__ ) );

	if ( is_woothemes_projects() ) {
		wp_enqueue_style( 'woothemes-projects-styles' );
		wp_enqueue_style( 'woothemes-projects-handheld' );
		wp_enqueue_style( 'woothemes-projects-general' );
	}

}