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

		// WooCommerce Integration
		add_action( 'init', array( $this, 'projects_woocommerce_init' ) );

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
					$found_testimonials[] = array(
												'id' 	=> $testimonial->ID,
												'title' => $testimonial->post_title
												);
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

	/**
	 * Init function for the WooCommerce plugin integration.
	 * @since  1.2.0
	 * @return  void
	 */
	public function projects_woocommerce_init() {

		if ( class_exists( 'WooCommerce' ) ) {

			// Add custom fields
			add_filter( 'projects_custom_fields', array( $this, 'woocommerce_custom_fields' ) );

			// Enqueue admin JavaScript
			add_action( 'admin_enqueue_scripts', array( $this, 'woocommerce_admin_scripts' ) );
			add_action( 'wp_ajax_get_products', array( $this, 'get_products_callback' ) );
			add_action( 'admin_footer', array( $this, 'woocommerce_javascript' ) );

		}

	} // End projects_woocommerce_init()

	public function woocommerce_admin_scripts () {
		wp_enqueue_script( 'jquery-ui-autocomplete', null, array( 'jquery' ), null, false );
	} // End projects_woocommerce_admin_scripts()

	/**
	 * Ajax callback to search for products.
	 * @param  string $query Search Query.
	 * @since  1.2.0
	 * @return json       	Search Results.
	 */
	public function get_products_callback() {
		check_ajax_referer( 'projects_ajax_get_products', 'security' );

		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( ! empty( $term ) ) {

			header( 'Content-Type: application/json; charset=utf-8' );

			$query_args = array(
				'post_type' 		=> 'product',
				'orderby' 			=> 'title',
				's' 				=> $term,
				'suppress_filters' 	=> false
			);

			$products = get_posts( $query_args );

			$found_products = array();

			if ( $products ) {

				foreach ( $products as $product ) {

					$_product = new WC_Product( $product->ID );

					if ( $_product->get_sku() ) {
						$identifier = $_product->get_sku();
					} else {
						$identifier = '#' . $_product->id;
					}

					$found_products[] = array(
											'id' 			=> $product->ID,
											'title' 		=> $product->post_title,
											'identifier'	=> $identifier,
											);
				}
			}

			echo json_encode( $found_products );
		}

		die();

	} // End get_products_callback()

	/**
	 * Output Products admin javascript
	 * @since  1.2.0
	 * @return  void
	 */
	public function woocommerce_javascript() {

		global $pagenow, $post_type;

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && isset( $post_type ) && esc_attr( $post_type ) == $this->post_type ) {

			$ajax_nonce = wp_create_nonce( 'projects_ajax_get_products' );

	?>
			<script type="text/javascript" >
				jQuery(function() {
					jQuery( "#products_search" ).autocomplete({
						minLength: 2,
						source: function ( request, response ) {
							jQuery.ajax({
								url: ajaxurl,
								dataType: 'json',
								data: {
									action: 'get_products',
									security: '<?php echo $ajax_nonce; ?>',
									term: request.term
								},
								success: function( data ) {
									response( jQuery.map( data, function( item ) {
										return {
											label: item.identifier + ' – ' + item.title,
											value: item.id
										}
									}));
								}
							});
						},
						select: function ( event, ui ) {
							event.preventDefault();
							jQuery( "#products_search" ).val( ui.item.label );
							jQuery( "#products_id" ).val( ui.item.value );
						},
						change: function ( event, ui ) {
							event.preventDefault();
							if ( 0 == jQuery( "#products_search" ).val().length ) {
								jQuery( "#products_id" ).val( '' );
							}
						}
					});
				});
			</script>
	<?php
		}
	} // End woocommerce_javascript()

	public function woocommerce_custom_fields( $fields ) {

		$fields['products_search'] = array(
			'name' 			=> __( 'Product', 'projects-by-woothemes' ),
			'description' 	=> __( 'Search for Product to link to this Project. (Optional)', 'projects-by-woothemes' ),
			'type' 			=> 'text',
			'default' 		=> '',
			'section' 		=> 'info',
		);

		$fields['products_id'] = array(
			'name' 			=> __( 'Product ID', 'projects-by-woothemes' ),
			'description' 	=> __( 'Holds the id of the selected product.', 'projects-by-woothemes' ),
			'type' 			=> 'hidden',
			'default' 		=> 0,
			'section' 		=> 'info',
		);

		return $fields;

	} // End woocommerce_custom_fields()

} // End Class

new Projects_Integrations();