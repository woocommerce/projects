<?php
/**
 * WooThemes Projects Hooks
 *
 * Action/filter hooks used for WooThemes Projects functions/templates
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Template Hooks ********************************************************/

if ( ! is_admin() || defined('DOING_AJAX') ) {

	add_filter( 'body_class', 'woo_projects_body_class' );

	/**
	 * Content Wrappers
	 *
	 * @see woothemes_projects_output_content_wrapper()
	 * @see woothemes_projects_output_content_wrapper_end()
	 */
	add_action( 'woothemes_projects_before_main_content', 'woothemes_projects_output_content_wrapper', 10 );
	add_action( 'woothemes_projects_after_main_content', 'woothemes_projects_output_content_wrapper_end', 10 );

	/**
	 * Sidebar
	 *
	 * @see woothemes_projects_get_sidebar()
	 */
	add_action( 'woothemes_projects_sidebar', 'woothemes_projects_get_sidebar', 10 );

	/**
	 * Archive descriptions
	 *
	 * @see woothemes_projects_taxonomy_archive_description()
	 * @see woothemes_projects_project_archive_description()
	 */
	add_action( 'woothemes_projects_archive_description', 'woothemes_projects_taxonomy_archive_description', 10 );
	add_action( 'woothemes_projects_archive_description', 'woothemes_projects_project_archive_description', 10 );

	/**
	 * Project Loop Items
	 *
	 * @see woothemes_projects_template_loop_project_thumbnail()
	 */
	add_action( 'woothemes_projects_before_showcase_loop_item_title', 'woothemes_projects_template_loop_project_thumbnail', 10 );

	/**
	 * Before Single Projects Summary Div
	 *
	 * @see woothemes_projects_show_project_images()
	 * @see woothemes_projects_show_project_thumbnails()
	 */
	add_action( 'woothemes_projects_before_single_project_summary', 'woothemes_projects_show_project_images', 20 );
	add_action( 'woothemes_projects_project_thumbnails', 'woothemes_projects_show_project_thumbnails', 20 );

	/**
	 * Project Summary Box
	 *
	 * @see woothemes_projects_template_single_title()
	 * @see woothemes_projects_template_single_excerpt()
	 * @see woothemes_projects_template_single_meta()
	 */
	add_action( 'woothemes_projects_single_project_summary', 'woothemes_projects_template_single_title', 5 );
	add_action( 'woothemes_projects_single_project_summary', 'woothemes_projects_template_single_excerpt', 20 );
	add_action( 'woothemes_projects_single_project_summary', 'woothemes_projects_template_single_meta', 40 );

	/**
	 * Pagination after showcase loops
	 *
	 * @see woothemes_projects_pagination()
	 */
	add_action( 'woothemes_projects_after_showcase_loop', 'woothemes_projects_pagination', 10 );
}

/** Store Event Hooks *****************************************************/

/**
 * Showcase Page Handling and Support
 *
 * @see woothemes_projects_nav_menu_item_classes()
 */
add_filter( 'wp_nav_menu_objects',  'woothemes_projects_nav_menu_item_classes', 2, 20 );

/**
 * Filters
 */
add_filter( 'woothemes_projects_short_description', 'wptexturize'        );
add_filter( 'woothemes_projects_short_description', 'convert_smilies'    );
add_filter( 'woothemes_projects_short_description', 'convert_chars'      );
add_filter( 'woothemes_projects_short_description', 'wpautop'            );
add_filter( 'woothemes_projects_short_description', 'shortcode_unautop'  );
add_filter( 'woothemes_projects_short_description', 'prepend_attachment' );
add_filter( 'woothemes_projects_short_description', 'do_shortcode', 11 ); // AFTER wpautop()