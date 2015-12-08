<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Query Class
 *
 * All functionality pertaining to the projects WP query modifications.
 *
 * @package WordPress
 * @subpackage Projects_Query
 * @category Plugin
 * @author Jeffikus
 * @since 1.5.0
 */

class Projects_Query {
	/**
	 * NOTE - Ported from WooCommerce in order to
	 * correctly modify the query object.
	 * @since   1.5.0
	 */

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	/** @public array Unfiltered project ids - refactor 1.5.1 */
	public $unfiltered_project_ids 	= array();

	/** @public array Filtered project ids - refactor 1.5.1 */
	public $filtered_project_ids 	= array();

	/** @public array Filtered project ids - refactor 1.5.1 */
	public $filtered_project_ids_for_taxonomy 	= array();

	/** @public array project IDs that match filters */
	public $post__in 		= array();

	/** @public array|string The meta query for the page */
	public $meta_query 		= '';

	/** @public array Post IDs - refactor 1.5.1 */
	public $layered_nav_post__in 	= array();

	/** @public array Stores post IDs - refactor 1.5.1 */
	public $layered_nav_project_ids = array();

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.5.0
	 * @return void
	 */
	public function __construct () {
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_filter( 'the_posts', array( $this, 'the_posts' ), 11, 2 );
			add_action( 'wp', array( $this, 'remove_project_query' ) );
		}
		$this->init_query_vars();
	} // End __construct()

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP
		$this->query_vars = array();
	}

	/**
	 * add_query_vars function.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Get query vars
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Hook into pre_get_posts to do the main project query
	 *
	 * @param mixed $q query object
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		// Fix for verbose page rules
		if ( $GLOBALS['wp_rewrite']->use_verbose_page_rules && isset( $q->queried_object_id ) && $q->queried_object_id === projects_get_page_id( 'projects' ) ) {
			$q->set( 'post_type', 'project' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );

			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;
		}

		// Fix for endpoints on the homepage
		if ( $q->is_home() && 'page' == get_option('show_on_front') && get_option('page_on_front') != $q->get('page_id') ) {
			$_query = wp_parse_args( $q->query );
			if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;

				$q->set( 'page_id', get_option('page_on_front') );
			}
		}

		// When orderby is set, WordPress shows posts. Get around that here.
		if ( $q->is_home() && 'page' == get_option('show_on_front') && get_option('page_on_front') == projects_get_page_id( 'projects' ) ) {
			$_query = wp_parse_args( $q->query );
			if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
				$q->is_page = true;
				$q->is_home = false;
				$q->set( 'page_id', get_option('page_on_front') );
				$q->set( 'post_type', 'project' );
			}
		}

		// Special check for the project archive on front
		if ( $q->is_page() && 'page' == get_option( 'show_on_front' ) && $q->get('page_id') == projects_get_page_id( 'projects' ) ) {

			// This is a front-page projects
			$q->set( 'post_type', 'project' );
			$q->set( 'page_id', '' );
			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page projects later on
			define( 'PROJECT_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting http://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$project_page 	= get_post( projects_get_page_id( 'projects' ) );

			$wp_post_types['project']->ID 			= $project_page->ID;
			$wp_post_types['project']->post_title 	= $project_page->post_title;
			$wp_post_types['project']->post_name 	= $project_page->post_name;
			$wp_post_types['project']->post_type    = $project_page->post_type;
			$wp_post_types['project']->ancestors    = get_ancestors( $project_page->ID, $project_page->post_type );

			// Fix conditional Functions like is_front_page
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;

			// Fix WP SEO
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
				add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
			}

		// Only apply to project categories, the project post archive, the projects page, project tags, and project attribute taxonomies
		} elseif ( ! $q->is_post_type_archive( 'project' ) && ! $q->is_tax( get_object_taxonomies( 'project' ) ) ) {
			return;
		}

		$this->project_query( $q );

		if ( is_search() ) {
			add_filter( 'posts_where', array( $this, 'search_post_excerpt' ) );
			add_filter( 'wp', array( $this, 'remove_posts_where' ) );
		}

		// We're on a projects page so queue the get_projects_in_view function
		add_action( 'wp', array( $this, 'get_projects_in_view' ), 2);

		// And remove the pre_get_posts hook
		$this->remove_project_query();
	}

	/**
	 * search_post_excerpt function.
	 *
	 * @access public
	 * @param string $where (default: '')
	 * @return string (modified where clause)
	 */
	public function search_post_excerpt( $where = '' ) {
		global $wp_the_query;

		// If this is not a WC Query, do not modify the query
		if ( empty( $wp_the_query->query_vars['projects_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
			return $where;

		$where = preg_replace(
			"/post_title\s+LIKE\s*(\'\%[^\%]+\%\')/",
			"post_title LIKE $1) OR (post_excerpt LIKE $1", $where );

		return $where;
	}

	/**
	 * wpseo_metadesc function.
	 * Hooked into wpseo_ hook already, so no need for function_exist
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metadesc() {
		return WPSEO_Meta::get_value( 'metadesc', projects_get_page_id('projects') );
	}

	/**
	 * wpseo_metakey function.
	 * Hooked into wpseo_ hook already, so no need for function_exist
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metakey() {
		return WPSEO_Meta::get_value( 'metakey', projects_get_page_id('projects') );
	}

	/**
	 * Hook into the_posts to do the main project query if needed - relevanssi compatibility
	 *
	 * @access public
	 * @param array $posts
	 * @param WP_Query|bool $query (default: false)
	 * @return array
	 */
	public function the_posts( $posts, $query = false ) {
		// Abort if there's no query
		if ( ! $query )
			return $posts;

		// Abort if we're not filtering posts
		if ( empty( $this->post__in ) )
			return $posts;

		// Abort if this query has already been done
		if ( ! empty( $query->projects_query ) )
			return $posts;

		// Abort if this isn't a search query
		if ( empty( $query->query_vars["s"] ) )
			return $posts;

		// Abort if we're not on a post type archive/project taxonomy
		if 	( ! $query->is_post_type_archive( 'project' ) && ! $query->is_tax( get_object_taxonomies( 'project' ) ) )
			return $posts;

		$filtered_posts   = array();
		$queried_post_ids = array();

		foreach ( $posts as $post ) {
			if ( in_array( $post->ID, $this->post__in ) ) {
				$filtered_posts[] = $post;
				$queried_post_ids[] = $post->ID;
			}
		}

		$query->posts = $filtered_posts;
		$query->post_count = count( $filtered_posts );

		// Ensure filters are set
		$this->unfiltered_project_ids = $queried_post_ids;
		$this->filtered_project_ids   = $queried_post_ids;

		if ( sizeof( $this->layered_nav_post__in ) > 0 ) {
			$this->layered_nav_project_ids = array_intersect( $this->unfiltered_project_ids, $this->layered_nav_post__in );
		} else {
			$this->layered_nav_project_ids = $this->unfiltered_project_ids;
		}

		return $filtered_posts;
	}

	/**
	 * Query the projects, applying sorting/ordering etc. This applies to the main wordpress loop
	 *
	 * @param mixed $q
	 */
	public function project_query( $q ) {

		// Meta query
		$meta_query = $this->get_meta_query( $q->get( 'meta_query' ) );

		// Get a list of post id's which match the current filters set (in the layered nav and price filter)
		$post__in   = array_unique( apply_filters( 'loop_projects_post_in', array() ) );

		// Query vars that affect posts shown
		$q->set( 'meta_query', $meta_query );
		$q->set( 'post__in', $post__in );
		$q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'loop_projects_per_page', get_option( 'posts_per_page' ) ) );

		// Set a special variable
		$q->set( 'projects_query', 'project_query' );

		// Store variables
		$this->post__in   = $post__in;
		$this->meta_query = $meta_query;

		do_action( 'projects_project_query', $q, $this );
	}

	/**
	 * Remove the query
	 */
	public function remove_project_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Remove the posts_where filter
	 */
	public function remove_posts_where() {
		remove_filter( 'posts_where', array( $this, 'search_post_excerpt' ) );
	}

	/**
	 * Get an unpaginated list all project ID's (both filtered and unfiltered).
	 */
	public function get_projects_in_view() {
		global $wp_the_query;

		// Get main query
		$current_wp_query = $wp_the_query->query;

		// Get WP Query for current page (without 'paged')
		unset( $current_wp_query['paged'] );

		// Get all visible posts, regardless of filters
		$unfiltered_project_ids = get_posts(
			array_merge(
				$current_wp_query,
				array(
					'post_type'              => 'project',
					'numberposts'            => -1,
					'post_status'            => 'publish',
					'meta_query'             => $this->meta_query,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'pagename'               => '',
					'projects_query'         => 'get_projects_in_view'
				)
			)
		);

		// Store the variable
		$this->unfiltered_project_ids = $unfiltered_project_ids;

		// Also store filtered posts ids...
		if ( sizeof( $this->post__in ) > 0 ) {
			$this->filtered_project_ids = array_intersect( $this->unfiltered_project_ids, $this->post__in );
		} else {
			$this->filtered_project_ids = $this->unfiltered_project_ids;
		}

		// And filtered post ids which just take layered nav into consideration
		if ( sizeof( $this->layered_nav_post__in ) > 0 ) {
			$this->layered_nav_project_ids = array_intersect( $this->unfiltered_project_ids, $this->layered_nav_post__in );
		} else {
			$this->layered_nav_project_ids = $this->unfiltered_project_ids;
		}
	}

	/**
	 * Appends meta queries to an array.
	 * @access public
	 * @param array $meta_query
	 * @return array
	 */
	public function get_meta_query( $meta_query = array() ) {
		if ( ! is_array( $meta_query ) )
			$meta_query = array();

		return array_filter( $meta_query );
	}

} // End Class