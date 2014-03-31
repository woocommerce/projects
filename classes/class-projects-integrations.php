<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Integrations Class
 *
 * All functionality pertaining to the integration between projects and other plugins.
 *
 * @package WordPress
 * @subpackage Projects
 * @category Plugin
 * @since 1.2.0
 */
class Projects_Integrations {
	private $token;
	private $post_type;

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.2.0
	 * @return void
	 */
	public function __construct() {
		$this->token 		= 'projects';
		$this->post_type 	= 'project';

		// Testimonials Integration
		add_action( 'init', array( $this, 'projects_testimonials_init' ) );

	} // End __construct()


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
						},
						change: function ( event, ui ) {
							event.preventDefault();
							if ( 0 == jQuery( "#testimonials_search" ).val().length ) {
								jQuery( "#testimonials_id" ).val( '' );
							}
						}
					});
				});
			</script>
	<?php
		}
	} // End testimonials_javascript()

	public function testimonials_custom_fields( $fields ) {

		$fields['testimonials_search'] = array(
			'name' 			=> __( 'Testimonial', 'projects-by-woothemes' ),
			'description' 	=> __( 'Search for Testimonial to link to this Project. (Optional)', 'projects-by-woothemes' ),
			'type' 			=> 'text',
			'default' 		=> '',
			'section' 		=> 'info',
		);

		$fields['testimonials_id'] = array(
			'name' 			=> __( 'Testimonial ID', 'projects-by-woothemes' ),
			'description' 	=> __( 'Holds the id of the selected testimonial.', 'projects-by-woothemes' ),
			'type' 			=> 'hidden',
			'default' 		=> 0,
			'section' 		=> 'info',
		);

		return $fields;

	} // End testimonials_custom_fields()

} // End Class

new Projects_Integrations();