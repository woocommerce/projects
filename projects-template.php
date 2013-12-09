<?php
/**
 * WooThemes Projects Template Functions
 *
 * Functions used in the template files to output content - in most cases hooked in via the template actions. All functions are pluggable.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Global ****************************************************************/

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 *
 * @return void
 */
function projects_template_redirect() {
	global $wp_query, $wp;

	// Redirect project base page to post type archive url
	if ( is_page( projects_get_page_id( 'showcase' ) ) ) {
		wp_safe_redirect( get_post_type_archive_link( 'project' ) );
		exit;
	}
}

/**
 * Fix active class in nav for shop page.
 *
 * @param array $menu_items
 * @param array $args
 * @return array
 */
function projects_nav_menu_item_classes( $menu_items, $args ) {

	if ( ! is_projects() ) return $menu_items;

	$projects_page 	= (int) projects_get_page_id( 'showcase' );
	$page_for_posts = (int) get_option( 'page_for_posts' );

	foreach ( (array) $menu_items as $key => $menu_item ) {

		$classes = (array) $menu_item->classes;

		// Unset active class for blog page
		if ( $page_for_posts == $menu_item->object_id ) {
			$menu_items[$key]->current = false;

			if ( in_array( 'current_page_parent', $classes ) )
				unset( $classes[ array_search('current_page_parent', $classes) ] );

			if ( in_array( 'current-menu-item', $classes ) )
				unset( $classes[ array_search('current-menu-item', $classes) ] );

		// Set active state if this is the shop page link
		} elseif ( is_showcase() && $projects_page == $menu_item->object_id ) {
			$menu_items[$key]->current = true;
			$classes[] = 'current-menu-item';
			$classes[] = 'current_page_item';

		// Set parent state if this is a product page
		} elseif ( is_singular( 'project' ) && $projects_page == $menu_item->object_id ) {
			$classes[] = 'current_page_parent';
		}

		$menu_items[$key]->classes = array_unique( $classes );

	}

	return $menu_items;
}

if ( ! function_exists( 'projects_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function projects_output_content_wrapper() {
		projects_get_template( 'showcase/wrapper-start.php' );
	}
}
if ( ! function_exists( 'projects_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function projects_output_content_wrapper_end() {
		projects_get_template( 'showcase/wrapper-end.php' );
	}
}

if ( ! function_exists( 'projects_get_sidebar' ) ) {

	/**
	 * Get the showcase sidebar template.
	 *
	 * @access public
	 * @return void
	 */
	function projects_get_sidebar() {
		projects_get_template( 'showcase/sidebar.php' );
	}
}

/** Loop ******************************************************************/

if ( ! function_exists( 'projects_page_title' ) ) {

	/**
	 * projects_page_title function.
	 *
	 * @access public
	 * @return void
	 */
	function projects_page_title() {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'projects' ), get_search_query() );

			if ( get_query_var( 'paged' ) )
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'projects' ), get_query_var( 'paged' ) );

		} elseif ( is_tax() ) {

			$page_title = single_term_title( '', false );

		} else {

			$showcase_page_id 	= projects_get_page_id( 'showcase' );
			$page_title   		= get_the_title( $showcase_page_id );

		}

	    echo apply_filters( 'projects_page_title', $page_title );
	}
}

if ( ! function_exists( 'projects_project_loop_start' ) ) {

	/**
	 * Output the start of a project loop. By default this is a UL
	 *
	 * @access public
	 * @return void
	 */
	function projects_project_loop_start( $echo = true ) {
		ob_start();
		projects_get_template( 'loop/loop-start.php' );
		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}
if ( ! function_exists( 'projects_project_loop_end' ) ) {

	/**
	 * Output the end of a project loop. By default this is a UL
	 *
	 * @access public
	 * @return void
	 */
	function projects_project_loop_end( $echo = true ) {
		ob_start();

		projects_get_template( 'loop/loop-end.php' );

		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}
if ( ! function_exists( 'projects_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives
	 *
	 * @access public
	 * @subpackage	Archives
	 * @return void
	 */
	function projects_taxonomy_archive_description() {
		if ( is_tax( array( 'project-category', 'project-tag' ) ) && get_query_var( 'paged' ) == 0 ) {
			$description = apply_filters( 'the_content', term_description() );
			if ( $description ) {
				echo '<div class="term-description">' . $description . '</div>';
			}
		}
	}
}
if ( ! function_exists( 'projects_project_archive_description' ) ) {

	/**
	 * Show a showcase page description on project archives
	 *
	 * @access public
	 * @subpackage	Archives
	 * @return void
	 */
	function projects_project_archive_description() {
		if ( is_post_type_archive( 'project' ) && get_query_var( 'paged' ) == 0 || is_page( projects_get_page_id( 'showcase' ) ) ) {
			$showcase_page   	= get_post( projects_get_page_id( 'showcase' ) );
			$description 		= apply_filters( 'the_content', $showcase_page->post_content );
			if ( $description ) {
				echo '<div class="page-description">' . $description . '</div>';
			}
		}
	}
}

if ( ! function_exists( 'projects_template_loop_project_thumbnail' ) ) {

	/**
	 * Get the project thumbnail for the loop.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function projects_template_loop_project_thumbnail() {
		echo '<figure class="project-thumbnail">' . projects_get_project_thumbnail() . '</figure>';
	}
}
if ( ! function_exists( 'projects_reset_loop' ) ) {

	/**
	 * Reset the loop's index and columns when we're done outputting a project loop.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function projects_reset_loop() {
		global $projects_loop;
		// Reset loop/columns globals when starting a new loop
		$projects_loop['loop'] = $projects_loop['column'] = '';
	}
}

add_filter( 'loop_end', 'projects_reset_loop' );


if ( ! function_exists( 'projects_get_project_thumbnail' ) ) {

	/**
	 * Get the project thumbnail, or the placeholder if not set.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @param string $size (default: 'showcase_catalog')
	 * @param int $placeholder_width (default: 0)
	 * @param int $placeholder_height (default: 0)
	 * @return string
	 */
	function projects_get_project_thumbnail( $size = 'project-archive' ) {
		global $post;

		if ( has_post_thumbnail() )
			return get_the_post_thumbnail( $post->ID, $size );
	}
}

if ( ! function_exists( 'projects_result_count' ) ) {

	/**
	 * Output the result count text (Showing x - x of x results).
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function projects_result_count() {
		projects_get_template( 'loop/result-count.php' );
	}
}

if ( ! function_exists( 'projects_pagination' ) ) {

	/**
	 * Output the pagination.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function projects_pagination() {
		projects_get_template( 'loop/pagination.php' );
	}
}

if ( ! function_exists( 'projects_template_categories' ) ) {

	/**
	 * Output the project categories.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_categories() {
		projects_get_template( 'loop/categories.php' );
	}
}

if ( ! function_exists( 'projects_template_short_description' ) ) {

	/**
	 * Output the project short description.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_short_description() {
		projects_get_template( 'loop/short-description.php' );
	}
}

/** Single Project ********************************************************/

if ( ! function_exists( 'projects_template_single_feature' ) ) {

	/**
	 * Output the project feature before the single project summary.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_single_feature() {
		projects_get_template( 'single-project/project-feature.php' );
	}
}

if ( ! function_exists( 'projects_template_single_gallery' ) ) {

	/**
	 * Output the project gallery before the single project summary.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_single_gallery() {
		projects_get_template( 'single-project/project-gallery.php' );
	}
}

if ( ! function_exists( 'projects_template_single_title' ) ) {

	/**
	 * Output the project title.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_single_title() {
		projects_get_template( 'single-project/title.php' );
	}
}

if ( ! function_exists( 'projects_template_single_description' ) ) {

	/**
	 * Output the project short description (excerpt).
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_single_description() {
		projects_get_template( 'single-project/description.php' );
	}
}

if ( ! function_exists( 'projects_template_single_meta' ) ) {

	/**
	 * Output the project meta.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_template_single_meta() {
		projects_get_template( 'single-project/meta.php' );
	}
}

if ( ! function_exists( 'projects_single_pagination' ) ) {

	/**
	 * Output the project pagination.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function projects_single_pagination() {
		projects_get_template( 'single-project/pagination.php' );
	}
}