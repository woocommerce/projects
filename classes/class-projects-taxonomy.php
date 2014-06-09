<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Taxonomy Class
 *
 * Re-usable class for registering project taxonomies.
 *
 * @package WordPress
 * @subpackage WooThemes_Projects
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Projects_Taxonomy {
	/**
	 * The post type to register the taxonomy for.
	 * @access  private
	 * @since   1.0.0
	 * @var     string
	 */
	private $post_type;

	/**
	 * The key of the taxonomy.
	 * @access  private
	 * @since   1.0.0
	 * @var     string
	 */
	private $token;

	/**
	 * The singular name for the taxonomy.
	 * @access  private
	 * @since   1.0.0
	 * @var     string
	 */
	private $singular;

	/**
	 * The plural name for the taxonomy.
	 * @access  private
	 * @since   1.0.0
	 * @var     string
	 */
	private $plural;

	/**
	 * The arguments to use when registering the taxonomy.
	 * @access  private
	 * @since   1.0.0
	 * @var     string
	 */
	private $args;

	/**
	 * Class constructor.
	 * @access  public
	 * @since   1.0.0
	 * @param   string $token    The taxonomy key.
	 * @param   string $singular Singular name.
	 * @param   string $plural   Plural  name.
	 * @param   array  $args     Array of argument overrides.
	 */
	public function __construct ( $token = 'project-category', $singular = '', $plural = '', $args = array() ) {
		$this->post_type 	= 'project';
		$this->token 		= esc_attr( $token );
		$this->singular 	= apply_filters( 'projects_taxonomy_' . $this->token . '_singular_name', esc_html($singular) );
		$this->plural           = apply_filters( 'projects_taxonomy_' . $this->token . '_plural_name', esc_html($plural) );

		if ( '' == $this->singular ) $this->singular = __( 'Category', 'projects-by-woothemes' );
		if ( '' == $this->plural ) $this->plural = __( 'Categories', 'projects-by-woothemes' );

		$this->args = wp_parse_args( $args, $this->_get_default_args() );
	} // End __construct()

	/**
	 * Return an array of default arguments.
	 * @access  private
	 * @since   1.0.0
	 * @return  array Default arguments.
	 */
	private function _get_default_args () {
		return array(
			'labels' 				=> $this->_get_default_labels(),
			'public' 				=> true,
			'hierarchical' 			=> true,
			'show_ui' 				=> true,
			'show_admin_column' 	=> true,
			'query_var' 			=> true,
			'show_in_nav_menus' 	=> true,
			'show_tagcloud' 		=> false,
			'rewrite'               => array( 'slug' => ( $projects_page_id = projects_get_page_id( 'projects' ) ) && get_page( $projects_page_id ) ? get_page_uri( $projects_page_id ) : 'projects', 'with_front' => false )
			);
	} // End _get_default_args()

	/**
	 * Return an array of default labels.
	 * @access  private
	 * @since   1.3.0
	 * @return  array Default labels.
	 */
	private function _get_default_labels () {
		return array(
			    'name'                => sprintf( _x( 'Project %s', 'taxonomy general name', 'projects-by-woothemes' ), $this->plural ),
			    'singular_name'       => sprintf( _x( '%s', 'taxonomy singular name', 'projects-by-woothemes' ), $this->singular ),
			    'search_items'        => sprintf( __( 'Search %s', 'projects-by-woothemes' ), $this->plural ),
			    'all_items'           => sprintf( __( 'All %s', 'projects-by-woothemes' ), $this->plural ),
			    'parent_item'         => sprintf( __( 'Parent %s', 'projects-by-woothemes' ), $this->singular ),
			    'parent_item_colon'   => sprintf( __( 'Parent %s:', 'projects-by-woothemes' ), $this->singular ),
			    'edit_item'           => sprintf( __( 'Edit %s', 'projects-by-woothemes' ), $this->singular ),
			    'update_item'         => sprintf( __( 'Update %s', 'projects-by-woothemes' ), $this->singular ),
			    'add_new_item'        => sprintf( __( 'Add New %s', 'projects-by-woothemes' ), $this->singular ),
			    'new_item_name'       => sprintf( __( 'New %s Name', 'projects-by-woothemes' ), $this->singular ),
			    'menu_name'           => sprintf( __( '%s', 'projects-by-woothemes' ), $this->plural )
			  );
	} // End _get_default_labels()

	/**
	 * Register the taxonomy.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register () {
		$args = apply_filters( 'projects_register_taxonomy', $this->args );
		register_taxonomy( esc_attr( $this->token ), esc_attr( $this->post_type ), (array) $args );
	} // End register()
} // End Class
