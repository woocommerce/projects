<?php
/**
 * WooThemes Projects Template Functions
 *
 * Functions used in the template files to output content - in most cases hooked in via the template actions. All functions are pluggable.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Global ****************************************************************/

if ( ! function_exists( 'woothemes_projects_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_output_content_wrapper() {
		woothemes_projects_get_template( 'showcase/wrapper-start.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_output_content_wrapper_end() {
		woothemes_projects_get_template( 'showcase/wrapper-end.php' );
	}
}

if ( ! function_exists( 'woothemes_projects_get_sidebar' ) ) {

	/**
	 * Get the showcase sidebar template.
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_get_sidebar() {
		woothemes_projects_get_template( 'showcase/sidebar.php' );
	}
}

/** Loop ******************************************************************/

if ( ! function_exists( 'woothemes_projects_page_title' ) ) {

	/**
	 * woothemes_projects_page_title function.
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_page_title() {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'woothemes-projects' ), get_search_query() );

			if ( get_query_var( 'paged' ) )
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'woothemes-projects' ), get_query_var( 'paged' ) );

		} elseif ( is_tax() ) {

			$page_title = single_term_title( '', false );

		} else {

			$showcase_page_id = woothemes_projects_get_page_id( 'showcase' );
			$page_title   = get_the_title( $showcase_page_id );

		}

	    echo apply_filters( 'woothemes_projects_page_title', $page_title );
	}
}

if ( ! function_exists( 'woothemes_projects_project_loop_start' ) ) {

	/**
	 * Output the start of a project loop. By default this is a UL
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_project_loop_start( $echo = true ) {
		ob_start();
		woothemes_projects_get_template( 'loop/loop-start.php' );
		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}
if ( ! function_exists( 'woothemes_projects_project_loop_end' ) ) {

	/**
	 * Output the end of a project loop. By default this is a UL
	 *
	 * @access public
	 * @return void
	 */
	function woothemes_projects_project_loop_end( $echo = true ) {
		ob_start();

		woothemes_projects_get_template( 'loop/loop-end.php' );

		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}
if ( ! function_exists( 'woothemes_projects_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives
	 *
	 * @access public
	 * @subpackage	Archives
	 * @return void
	 */
	function woothemes_projects_taxonomy_archive_description() {
		if ( is_tax( array( 'project-category', 'project-tag' ) ) && get_query_var( 'paged' ) == 0 ) {
			$description = apply_filters( 'the_content', term_description() );
			if ( $description ) {
				echo '<div class="term-description">' . $description . '</div>';
			}
		}
	}
}
if ( ! function_exists( 'woothemes_projects_project_archive_description' ) ) {

	/**
	 * Show a showcase page description on project archives
	 *
	 * @access public
	 * @subpackage	Archives
	 * @return void
	 */
	function woothemes_projects_project_archive_description() {
		if ( is_post_type_archive( 'project' ) && get_query_var( 'paged' ) == 0 ) {
			$showcase_page   = get_post( woothemes_projects_get_page_id( 'showcase' ) );
			$description = apply_filters( 'the_content', $showcase_page->post_content );
			if ( $description ) {
				echo '<div class="page-description">' . $description . '</div>';
			}
		}
	}
}

if ( ! function_exists( 'woothemes_projects_template_loop_project_thumbnail' ) ) {

	/**
	 * Get the project thumbnail for the loop.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function woothemes_projects_template_loop_project_thumbnail() {
		echo '<figure class="project-thumbnail">' . woothemes_projects_get_project_thumbnail() . '</figure>';
	}
}
if ( ! function_exists( 'woothemes_projects_reset_loop' ) ) {

	/**
	 * Reset the loop's index and columns when we're done outputting a project loop.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function woothemes_projects_reset_loop() {
		global $woothemes_projects_loop;
		// Reset loop/columns globals when starting a new loop
		$woothemes_projects_loop['loop'] = $woothemes_projects_loop['column'] = '';
	}
}

add_filter( 'loop_end', 'woothemes_projects_reset_loop' );


if ( ! function_exists( 'woothemes_projects_get_project_thumbnail' ) ) {

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
	function woothemes_projects_get_project_thumbnail( $size = 'projects-catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $post;

		if ( has_post_thumbnail() )
			return get_the_post_thumbnail( $post->ID, $size );
		elseif ( woothemes_projects_placeholder_img_src() )
			return woothemes_projects_placeholder_img( $size );
	}
}

if ( ! function_exists( 'woothemes_projects_result_count' ) ) {

	/**
	 * Output the result count text (Showing x - x of x results).
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function woothemes_projects_result_count() {
		woothemes_projects_get_template( 'loop/result-count.php' );
	}
}

if ( ! function_exists( 'woothemes_projects_pagination' ) ) {

	/**
	 * Output the pagination.
	 *
	 * @access public
	 * @subpackage	Loop
	 * @return void
	 */
	function woothemes_projects_pagination() {
		woothemes_projects_get_template( 'loop/pagination.php' );
	}
}

/** Single Project ********************************************************/

if ( ! function_exists( 'woothemes_projects_show_project_images' ) ) {

	/**
	 * Output the project image before the single project summary.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_show_project_images() {
		woothemes_projects_get_template( 'single-project/project-image.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_show_project_thumbnails' ) ) {

	/**
	 * Output the project thumbnails.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_show_project_thumbnails() {
		woothemes_projects_get_template( 'single-project/project-thumbnails.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_template_single_title' ) ) {

	/**
	 * Output the project title.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_template_single_title() {
		woothemes_projects_get_template( 'single-project/title.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_template_single_description' ) ) {

	/**
	 * Output the project short description (excerpt).
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_template_single_description() {
		woothemes_projects_get_template( 'single-project/description.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_template_single_meta' ) ) {

	/**
	 * Output the project meta.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_template_single_meta() {
		woothemes_projects_get_template( 'single-project/meta.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_template_categories' ) ) {

	/**
	 * Output the project categories.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_template_categories() {
		woothemes_projects_get_template( 'loop/categories.php' );
	}
}
if ( ! function_exists( 'woothemes_projects_template_short_description' ) ) {

	/**
	 * Output the project short description.
	 *
	 * @access public
	 * @subpackage	Project
	 * @return void
	 */
	function woothemes_projects_template_short_description() {
		woothemes_projects_get_template( 'loop/short-description.php' );
	}
}