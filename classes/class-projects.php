<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Class
 *
 * All functionality pertaining to the projects.
 *
 * @package WordPress
 * @subpackage Projects
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Projects {
	private $dir;
	private $assets_dir;
	private $assets_url;
	private $token;
	private $post_type;
	public $version;
	private $file;
	public $taxonomy_category;

	public $template_url;

	public $admin;
	public $frontend;

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		$this->dir 			= dirname( $file );
		$this->file 		= $file;
		$this->assets_dir 	= trailingslashit( $this->dir ) . 'assets';
		$this->assets_url 	= esc_url( str_replace( WP_PLUGIN_DIR, WP_PLUGIN_URL, $this->assets_dir ) );
		$this->token 		= 'projects';
		$this->post_type 	= 'project';

		// Variables
		$this->template_url	= apply_filters( 'projects_template_url', 'projects/' );

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );

		add_action( 'init', array( $this, 'register_rewrite_tags' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );

		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );

		if ( is_admin() ) {
			require_once( 'class-projects-admin.php' );
			$this->admin 	= new Projects_Admin( $file );
		} else {
			require_once( 'class-projects-frontend.php' );
			$this->frontend = new Projects_Frontend( $file );
		}
	} // End __construct()

	/**
	 * Register custom rewrite tags.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_rewrite_tags () {
		add_rewrite_tag( '%project_category%','([^&]+)' );
	} // End register_rewrite_tags()

	/**
	 * Register the post type.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name' 					=> _x( 'Projects', 'post type general name', 'projects' ),
			'singular_name' 		=> _x( 'Project', 'post type singular name', 'projects' ),
			'add_new' 				=> _x( 'Add New', $this->post_type, 'projects' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'projects' ), __( 'Project', 'projects' ) ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'projects' ), __( 'Project', 'projects' ) ),
			'new_item' 				=> sprintf( __( 'New %s', 'projects' ), __( 'Project', 'projects' ) ),
			'all_items' 			=> sprintf( _x( 'All %s', $this->post_type, 'projects' ), __( 'Projects', 'projects' ) ),
			'view_item' 			=> sprintf( __( 'View %s', 'projects' ), __( 'Project', 'projects' ) ),
			'search_items' 			=> sprintf( __( 'Search %a', 'projects' ), __( 'Projects', 'projects' ) ),
			'not_found' 			=>  sprintf( __( 'No %s Found', 'projects' ), __( 'Projects', 'projects' ) ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'projects' ), __( 'Projects', 'projects' ) ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __( 'Projects', 'projects' )

		);
		$args = array(
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> array(
										'slug' 			=> 'projects/%project_category%',
										'with_front' 	=> false
										),
			'capability_type' 		=> 'post',
			'has_archive' 			=> 'projects',
			'hierarchical' 			=> false,
			'supports' 				=> array(
										'title',
										'editor',
										'thumbnail',
										'excerpt'
										),
			'menu_position' 		=> 5,
			'menu_icon' 			=> ''
		);
		register_post_type( $this->post_type, $args );
	} // End register_post_type()

	/**
	 * Register the "project-category" taxonomy.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function register_taxonomy () {
		$this->taxonomy_category = new Projects_Taxonomy(); // Leave arguments empty, to use the default arguments.
		$this->taxonomy_category->register();
	} // End register_taxonomy()

	/**
	 * Register image sizes.
	 * @since  1.0.0
	 * @return void
	 */
	public function register_image_sizes () {
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'project-thumbnail', 100, 9999 ); 	// Thumbs
			add_image_size( 'project-category', 300, 9999 ); 	// Archive
			add_image_size( 'project-single', 1024, 9999 ); 	// Single
		}
	} // End register_image_sizes()

	/**
	 * Get projects.
	 * @param  string/array $args Arguments to be passed to the query.
	 * @since  1.0.0
	 * @return array/boolean      Array if true, boolean if false.
	 */
	public function get_projects ( $args = '' ) {
		$defaults = array(
			'limit' 	=> 5,
			'orderby' 	=> 'menu_order',
			'order' 	=> 'DESC',
			'id' 		=> 0
		);

		$args = wp_parse_args( $args, $defaults );

		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'projects_get_projects_args', $args );

		// The Query Arguments.
		$query_args 				= array();
		$query_args['post_type'] 	= 'project';
		$query_args['numberposts'] 	= $args['limit'];
		$query_args['orderby'] 		= $args['orderby'];
		$query_args['order'] 		= $args['order'];

		if ( is_numeric( $args['id'] ) && ( intval( $args['id'] ) > 0 ) ) {
			$query_args['p'] = intval( $args['id'] );
		}

		// Whitelist checks.
		if ( ! in_array( $query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$query_args['orderby'] = 'date';
		}

		if ( ! in_array( $query_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = 'DESC';
		}

		if ( ! in_array( $query_args['post_type'], get_post_types() ) ) {
			$query_args['post_type'] = 'project';
		}

		// The Query.
		$query = get_posts( $query_args );

		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			foreach ( $query as $k => $v ) {
				$meta = get_post_custom( $v->ID );

				// Get the image.
				$query[$k]->image = projects_get_image( $v->ID, $args['size'] );

				// Get the URL.
				if ( isset( $meta['_url'][0] ) && '' != $meta['_url'][0] ) {
					$query[$k]->url = esc_url( $meta['_url'][0] );
				} else {
					$query[$k]->url = get_permalink( $v->ID );
				}
			}
		} else {
			$query = false;
		}

		return $query;
	} // End get_projects()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'projects', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'projects';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'projects' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @since  1.0.0
	 * @return  void
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()

	/**
	 * Get the plugin url.
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function plugin_url () {
		return untrailingslashit( plugins_url( '/', $this->file ) );
	} // End plugin_url()


	/**
	 * Get the plugin path.
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function plugin_path () {
		return untrailingslashit( plugin_dir_path( $this->file ) );
	} // End plugin_path()


	/**
	 * Get an image size.
	 *
	 * Variable is filtered by projects_get_image_size_{image_size}
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  mixed $image_size
	 * @return string
	 */
	public function get_image_size ( $image_size ) {
		// Only return sizes we define in settings
		if ( ! in_array( $image_size, array( 'project-thumbnail', 'project-category', 'project-single' ) ) )
			return apply_filters( 'projects_get_image_size_' . $image_size, '' );

		$size = get_option( $image_size . '_image_size', array() );

		$size['width'] 	= isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop'] 	= isset( $size['crop'] ) ? $size['crop'] : 1;

		return apply_filters( 'projects_get_image_size_' . $image_size, $size );
	} // End get_image_size()
} // End Class