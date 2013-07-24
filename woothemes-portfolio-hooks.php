<?php
/**
 * WooThemes Portfolio Hooks
 *
 * Action/filter hooks used for WooThemes Portfolio functions/templates
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	Woothemes_Portfolio/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Template Hooks ********************************************************/

if ( ! is_admin() || defined('DOING_AJAX') ) {

	/**
	 * Content Wrappers
	 *
	 * @see woothemes_portfolio_output_content_wrapper()
	 * @see woothemes_portfolio_output_content_wrapper_end()
	 */
	add_action( 'woothemes_portfolio_before_main_content', 'woothemes_portfolio_output_content_wrapper', 10 );
	add_action( 'woothemes_portfolio_after_main_content', 'woothemes_portfolio_output_content_wrapper_end', 10 );

	/**
	 * Sidebar
	 *
	 * @see woothemes_portfolio_get_sidebar()
	 */
	if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
		add_action( 'woo_main_after', 'woothemes_portfolio_get_sidebar', 10 );
	} else {
		add_action( 'woothemes_portfolio_sidebar', 'woothemes_portfolio_get_sidebar', 10 );
	}

	/**
	 * Archive descriptions
	 *
	 * @see woothemes_portfolio_taxonomy_archive_description()
	 * @see woothemes_portfolio_project_archive_description()
	 */
	add_action( 'woothemes_portfolio_archive_description', 'woothemes_portfolio_taxonomy_archive_description', 10 );
	add_action( 'woothemes_portfolio_archive_description', 'woothemes_portfolio_project_archive_description', 10 );

	/**
	 * Product Loop Items
	 *
	 * @see woothemes_portfolio_template_loop_add_to_cart()
	 * @see woothemes_portfolio_template_loop_project_thumbnail()
	 * @see woothemes_portfolio_template_loop_price()
	 * @see woothemes_portfolio_template_loop_rating()
	 */
	add_action( 'woothemes_portfolio_after_showcase_loop_item', 'woothemes_portfolio_template_loop_add_to_cart', 10 );
	add_action( 'woothemes_portfolio_before_showcase_loop_item_title', 'woothemes_portfolio_template_loop_project_thumbnail', 10 );

	/**
	 * Before Single Products Summary Div
	 *
	 * @see woothemes_portfolio_show_project_images()
	 * @see woothemes_portfolio_show_project_thumbnails()
	 */
	add_action( 'woothemes_portfolio_before_single_project_summary', 'woothemes_portfolio_show_project_images', 20 );
	add_action( 'woothemes_portfolio_project_thumbnails', 'woothemes_portfolio_show_project_thumbnails', 20 );

	/**
	 * Product Summary Box
	 *
	 * @see woothemes_portfolio_template_single_title()
	 * @see woothemes_portfolio_template_single_price()
	 * @see woothemes_portfolio_template_single_excerpt()
	 * @see woothemes_portfolio_template_single_meta()
	 * @see woothemes_portfolio_template_single_sharing()
	 */
	add_action( 'woothemes_portfolio_single_project_summary', 'woothemes_portfolio_template_single_title', 5 );
	add_action( 'woothemes_portfolio_single_project_summary', 'woothemes_portfolio_template_single_excerpt', 20 );
	add_action( 'woothemes_portfolio_single_project_summary', 'woothemes_portfolio_template_single_meta', 40 );
	add_action( 'woothemes_portfolio_single_project_summary', 'woothemes_portfolio_template_single_sharing', 50 );

	/**
	 * Pagination after shop loops
	 *
	 * @see woothemes_portfolio_pagination()
	 */
	add_action( 'woothemes_portfolio_after_showcase_loop', 'woothemes_portfolio_pagination', 10 );
}

/** Store Event Hooks *****************************************************/

/**
 * Showcase Page Handling and Support
 *
 * @see woothemes_portfolio_nav_menu_item_classes()
 */
add_filter( 'wp_nav_menu_objects',  'woothemes_portfolio_nav_menu_item_classes', 2, 20 );

/**
 * Filters
 */
add_filter( 'woothemes_portfolio_short_description', 'wptexturize'        );
add_filter( 'woothemes_portfolio_short_description', 'convert_smilies'    );
add_filter( 'woothemes_portfolio_short_description', 'convert_chars'      );
add_filter( 'woothemes_portfolio_short_description', 'wpautop'            );
add_filter( 'woothemes_portfolio_short_description', 'shortcode_unautop'  );
add_filter( 'woothemes_portfolio_short_description', 'prepend_attachment' );
add_filter( 'woothemes_portfolio_short_description', 'do_shortcode', 11 ); // AFTER wpautop()