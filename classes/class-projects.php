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
	private $file;
	public $singular_name;
	public $plural_name;
	public $taxonomy_category;

	public $template_url;

	public $admin;
	public $frontend;

	/**
	 * @var string
	 */
	public $version = '1.0.0';

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
		$this->assets_url 	= esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token 		= 'projects';
		$this->post_type 	= 'project';

		// Variables
		$this->template_url	= apply_filters( 'projects_template_url', 'projects/' );

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Define constants
		$this->define_constants();

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );

		// Run this on deactivation.
		register_deactivation_hook( $this->file, array( $this, 'deactivation' ) );

		add_action( 'init', array( $this, 'post_type_names' ) );
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

		// Testimonials Integration
		add_action( 'init', array( $this, 'projects_testimonials_init' ) );

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
	 * Define Projects Constants
	 */
	private function define_constants() {
		define( 'PROJECTS_PLUGIN_FILE', __FILE__ );
		define( 'PROJECTS_VERSION', $this->version );
	}

	/**
	 * Change the UI names in the admin
	 *
	 * @access public
	 * @since  1.1
	 * @return void
	 */
	public function post_type_names () {
		$this->singular_name 	= apply_filters( 'projects_post_type_singular_name', _x( 'Project', 'post type singular name', 'projects' ) );
		$this->plural_name 		= apply_filters( 'projects_post_type_plural_name', _x( 'Projects', 'post type general name', 'projects' ) );
	}

	/**
	 * Register the post type.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name' 					=> $this->plural_name,
			'singular_name' 		=> $this->singular_name,
			'add_new' 				=> _x( 'Add New', $this->post_type, 'projects' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'projects' ), $this->singular_name ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'projects' ), $this->singular_name ),
			'new_item' 				=> sprintf( __( 'New %s', 'projects' ), $this->singular_name ),
			'all_items' 			=> sprintf( _x( 'All %s', $this->post_type, 'projects' ), $this->plural_name ),
			'view_item' 			=> sprintf( __( 'View %s', 'projects' ), $this->singular_name ),
			'search_items' 			=> sprintf( __( 'Search %a', 'projects' ), $this->plural_name ),
			'not_found' 			=>  sprintf( __( 'No %s Found', 'projects' ), $this->plural_name ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'projects' ), $this->plural_name ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $this->plural_name

		);
		$args = array(
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> array(
										'slug' 			=> trailingslashit ( strtolower( $this->singular_name ) ) . '%project_category%',
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

		$args = apply_filters( 'projects_register_post_type', $args );

		register_post_type( $this->post_type, (array) $args );
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

			$options = get_option( 'projects' );

			$defaults = apply_filters( 'projects_default_image_size', array(
				'project-archive' 	=> array(
											'width' 	=> 300,
											'height'	=> 300
										),
				'project-single' 	=> array(
											'width' 	=> 1024,
											'height'	=> 1024
										),
				'project-thumbnail' => array(
											'width' 	=> 100,
											'height'	=> 100
										)
			) );

			// Parse incomming $options into an array and merge it with $defaults
			$options = wp_parse_args( $options, $defaults );

			// Register each image size
			foreach ( $options as $image_size => $size ) {
				add_image_size( $image_size, $size['width'], $size['height'] );
			}

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

		// Flush rewrite rules
		flush_rewrite_rules();
	} // End activation()

	/**
	 * Run on deactivation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function deactivation () {
		// Flush rewrite rules
		flush_rewrite_rules();
	} // End deactivation()

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
		if ( ! in_array( $image_size, array( 'project-thumbnail', 'project-archive', 'project-single' ) ) )
			return apply_filters( 'projects_get_image_size_' . $image_size, '' );

		// Get image size from options
		$options = get_option( 'projects', array() );
		$size = $options[ $image_size ];

		$size['width'] 	= isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop'] 	= isset( $size['crop'] ) ? $size['crop'] : 1;

		return apply_filters( 'projects_get_image_size_' . $image_size, $size );
	} // End get_image_size()


	/**
	 * Init function for the Testimonials plugin integration.
	 * @since  1.1.0
	 * @return  void
	 */
	public function projects_testimonials_init() {

		if ( class_exists( 'Woothemes_Testimonials' ) ) {

			// Add custom fields
			add_filter( 'projects_custom_fields', array( $this, 'testimonials_custom_fields' ) );

			// Enqueue admin JavaScript
			add_action( 'admin_enqueue_scripts', array( $this, 'testimonials_admin_scripts' ) );
			add_action( 'wp_ajax_get_testimonials', array( $this, 'get_testimonials_callback' ) );
			add_action( 'admin_footer', array( $this, 'testimonials_javascript' ) );

		}

	} // End projects_testimonials_init()

	public function testimonials_admin_scripts () {
		wp_enqueue_script( 'jquery-ui-autocomplete', null, array( 'jquery' ), null, false);
	} // End projects_testimonials_admin_scripts()

	/**
	 * Ajax callback to search for testimonials.
	 * @param  string $query Search Query.
	 * @since  1.1.0
	 * @return json       	Search Results.
	 */
	public function get_testimonials_callback() {

		check_ajax_referer( 'projects_ajax_get_testimonials', 'security' );

		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( !empty( $term ) ) {

			header( 'Content-Type: application/json; charset=utf-8' );

			$query_args = array(
				'post_type' 		=> 'testimonial',
				'orderby' 			=> 'title',
				's' 				=> $term,
				'suppress_filters' 	=> false
			);

			$testimonials = get_posts( $query_args );

			$found_testimonials = array();

			if ( $testimonials ) {
				foreach ( $testimonials as $testimonial ) {
					$found_testimonials[] = array( 'id' => $testimonial->ID, 'title' => $testimonial->post_title );
				}
			}

			echo json_encode( $found_testimonials );

		}

		die();

	} // End get_testimonials_callback()

	/**
	 * Output Testimonials admin javascript
	 * @since  1.1.0
	 * @return  void
	 */
	public function testimonials_javascript() {

		global $pagenow, $post_type;

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && isset( $post_type ) && esc_attr( $post_type ) == $this->post_type ) {

			$ajax_nonce = wp_create_nonce( 'projects_ajax_get_testimonials' );

	?>
			<script type="text/javascript" >
				jQuery(function() {
					jQuery( "#testimonials_search" ).autocomplete({
						minLength: 2,
						source: function ( request, response ) {
							jQuery.ajax({
								url: ajaxurl,
								dataType: 'json',
								data: {
									action: 'get_testimonials',
									security: '<?php echo $ajax_nonce; ?>',
									term: request.term
								},
								success: function( data ) {
									response( jQuery.map( data, function( item ) {
										return {
											label: item.title,
											value: item.id
										}
									}));
								}
							});
						},
						select: function ( event, ui ) {
							event.preventDefault();
							jQuery( "#testimonials_search" ).val( ui.item.label );
							jQuery( "#testimonials_id" ).val( ui.item.value );
						}
					});
				});
			</script>
	<?php
		}
	} // End testimonials_javascript()

	public function testimonials_custom_fields( $fields ) {

		$fields['testimonials_search'] = array(
			'name' 			=> __( 'Testimonial', 'projects' ),
			'description' 	=> __( 'Select a Testimonial to link to this Project.', 'projects' ),
			'type' 			=> 'text',
			'default' 		=> '',
			'section' 		=> 'info',
		);

		$fields['testimonials_id'] = array(
			'name' 			=> __( 'Testimonial ID', 'projects' ),
			'description' 	=> __( 'Holds the id of the selected testimonial.', 'projects' ),
			'type' 			=> 'hidden',
			'default' 		=> 0,
			'section' 		=> 'info',
		);

		return $fields;

	} // End testimonials_custom_fields()

} // End Class