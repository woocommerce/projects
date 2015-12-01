<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Admin Class
 *
 * All functionality pertaining to the projects admin.
 *
 * @package WordPress
 * @subpackage Projects_Admin
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */

class Projects_Admin {
	private $dir;
	private $assets_dir;
	private $assets_url;
	private $token;
	private $post_type;
	private $file;
	private $singular_name;
	private $plural_name;

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct ( $file ) {
		$this->dir 			= dirname( $file );
		$this->file 		= $file;
		$this->assets_dir 	= trailingslashit( $this->dir ) . 'assets';
		$this->assets_url 	= esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token 		= 'projects';
		$this->post_type 	= 'project';

		global $pagenow;

		add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_action( 'admin_notices', array( $this, 'configuration_admin_notice' ) );
		add_action( 'do_meta_boxes', array( $this, 'featured_image_label' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'featured_image_set_link' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'featured_image_remove_link' ) );
		add_filter( 'media_view_strings', array( $this, 'featured_image_popup_set_link' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
		add_action( 'manage_' . $this->post_type .'_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );

		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
			add_action( 'restrict_manage_posts', array( $this, 'projects_restrict_manage_posts' ) );
			add_filter( 'parse_query', array( $this, 'projects_post_type_request' ) );
		}
	} // End __construct()

	/**
	 * Filter the request to just give posts for the given taxonomy, if applicable.
	 *
	 * @access public
	 * @param array $post_types - post types to add taxonomy filtering to
	 * @uses wp_dropdown_categories()
	 * @since  1.1.0
	 * @return void
	 */
	function projects_restrict_manage_posts() {
	    global $typenow;

	    $post_types = array( 'project' );

	    if ( in_array( $typenow, $post_types ) ) {
	    	$filters = get_object_taxonomies( $typenow );

	        foreach ( $filters as $tax_slug ) {

	        	$tax_obj = get_taxonomy( $tax_slug );

	        	if ( isset( $_GET[$tax_slug] ) ) {
	        		$selected = esc_attr( $_GET[$tax_slug] );
		        } else {
		        	$selected = null;
		        }

	            wp_dropdown_categories( array(
	                'show_option_all' 	=> __( 'Show All ' . $tax_obj->label, 'projects-by-woothemes' ),
	                'taxonomy' 	  		=> $tax_slug,
	                'name' 		  		=> $tax_obj->name,
	                'orderby' 	  		=> 'name',
	                'selected' 	  		=> $selected,
	                'hierarchical' 	  	=> $tax_obj->hierarchical,
	                'show_count' 	  	=> true,
	                'hide_empty' 	  	=> true,
	            ) );
	        }
	    }
	} // projects_restrict_manage_posts()

	/**
	 * Adjust the query string to use taxonomy slug instead of ID.
	 *
	 * @access public
	 * @param array $filters - all taxonomies for the current post type
	 * @uses get_object_taxonomies()
	 * @uses  get_term_by()
	 * @since  1.1.0
	 * @return void
	 */
	function projects_post_type_request( $query ) {
	  	global $pagenow, $typenow;

	    $filters = get_object_taxonomies( $typenow );

	    foreach ( $filters as $tax_slug ) {
			$var = &$query->query_vars[$tax_slug];

			if ( isset( $var ) ) {
				$term = get_term_by( 'id', $var, $tax_slug );

				if ( false != $term ) {
					$var = $term->slug;
				}
			}
	    }

	    return $query;
	} // End projects_post_type_request()

	/**
	 * Add custom columns for the "manage" screen of this post type.
	 *
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $wpdb, $post;

		$meta = get_post_custom( $id );

		switch ( $column_name ) {

			case 'image':
				$value = '';

				$value = projects_get_image( $id, 120 );

				echo $value;
			break;

			default:
			break;

		}
	} // End register_custom_columns()

	/**
	 * Add custom column headings for the "manage" screen of this post type.
	 *
	 * @access public
	 * @param array $defaults
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {

		$new_columns          = array();
		$new_columns['cb']    = $defaults['cb'];
		$new_columns['image'] = __( 'Cover Image', 'projects-by-woothemes' );

		$last_item = '';

		if ( isset( $defaults['date'] ) ) { unset( $defaults['date'] ); }

		if ( count( $defaults ) > 2 ) {
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $new_columns, $defaults );

		if ( $last_item != '' ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[$k] = $v;
				break;
			}
		}

		return $defaults;
	} // End register_custom_column_headings()

	/**
	 * Update messages for the post type admin.
	 * @since  1.0.0
	 * @param  array $messages Array of messages for all post types.
	 * @return array           Modified array.
	 */
	public function updated_messages ( $messages ) {
	  global $post, $post_ID, $projects;

	  $messages[$this->post_type] = array(
	    0 	=> '', // Unused. Messages start at index 1.
	    1 	=> sprintf( __( '%s updated. View %s%s%s', 'projects-by-woothemes' ), $projects->singular_name, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    2 	=> __( 'Custom field updated.', 'projects-by-woothemes' ),
	    3 	=> __( 'Custom field deleted.', 'projects-by-woothemes' ),
	    4 	=> sprintf( __( '%s updated.', 'projects-by-woothemes' ), $projects->singular_name ),
	    /* translators: %s: date and time of the revision */
	    5 	=> isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s', 'projects-by-woothemes' ), $projects->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 	=> sprintf( __( '%s published. View %s%s%s', 'projects-by-woothemes' ), $projects->singular_name, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    7 	=> sprintf( __( '%s saved.' ), $projects->singular_name ),
	    8 	=> sprintf( __( '%s submitted. Preview %s%s%s', 'projects-by-woothemes' ), $projects->singular_name, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    9 	=> sprintf( __( '%s scheduled for: %s. Preview %s', 'projects-by-woothemes' ), $projects->singular_name,
	      // translators: Publish box date format, see http://php.net/date
	      '<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink($post_ID) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    10 	=> sprintf( __( '%s draft updated. Preview %s%s%s', 'projects-by-woothemes' ), $projects->singular_name, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	  );

	  return $messages;
	} // End updated_messages()

	/**
	 * Setup the meta box.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function meta_box_setup () {
		global $projects;

		// Add short description meta box (replaces default excerpt)
		add_meta_box( 'postexcerpt', sprintf( __( '%s Short Description', 'projects-by-woothemes' ), $projects->singular_name ), array( $this, 'meta_box_short_description' ), $this->post_type, 'normal' );

		// Project Details Meta Box Load
		add_meta_box( 'project-data', sprintf( __( '%s Details', 'projects-by-woothemes' ), $projects->singular_name ), array( $this, 'meta_box_content' ), $this->post_type, 'normal', 'high' );

		// Project Images Meta Bog Load
		add_meta_box( 'project-images', sprintf( __( '%s Gallery', 'projects-by-woothemes' ), $projects->singular_name ), array( $this, 'meta_box_content_project_images' ), $this->post_type, 'side' );

	} // End meta_box_setup()


	/**
	 * The project short description meta box.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function meta_box_short_description( $post ) {
		$settings = array(
			'textarea_name'	=> 'excerpt',
			'quicktags' 	=> array( 'buttons' => 'em,strong,link' ),
			'tinymce' 		=> array(
								'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
								'theme_advanced_buttons2' => '',
								),
			'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', apply_filters( 'projects_product_short_description_editor_settings', $settings ) );
	} // End meta_box_short_description()

	/**
	 * The contents of our meta box.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;
		$fields = get_post_custom( $post_id );
		$field_data = $this->get_custom_fields_settings();

		$html = '';

		$html .= '<input type="hidden" name="woo_' . $this->token . '_nonce" id="woo_' . $this->token . '_nonce" value="' . wp_create_nonce( plugin_basename( $this->dir ) ) . '" />';

		if ( 0 < count( $field_data ) ) {
			$html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";

			foreach ( $field_data as $k => $v ) {
				$data = isset( $v['default'] ) ? $v['default'] : '';
				if ( isset( $fields['_' . $k] ) && isset( $fields['_' . $k][0] ) ) {
					$data = maybe_unserialize( $fields['_' . $k][0] );
				}

				switch ( $v['type'] ) {
					case 'hidden':
						$field = '<input name="' . esc_attr( $k ) . '" type="hidden" id="' . esc_attr( $k ) . '" value="' . esc_attr( $data ) . '" />';
						$html .= '<tr valign="top">' . $field . "\n";
						$html .= '<tr/>' . "\n";
						break;
					case 'text':
					case 'url':
						$field = '<input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />';
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'textarea':
						$field = '<textarea name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="large-text">' . esc_attr( $data ) . '</textarea>';
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'editor':
						$editor_arguments = array( 'media_buttons' => false, 'textarea_rows' => 10 );
						ob_start();
						wp_editor( $data, $k, apply_filters( 'projects_editor_arguments' , $editor_arguments ) );
						$field = ob_get_contents();
						ob_end_clean();
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'upload':
						$data_atts = '';
						if ( isset( $v['media-frame']['title'] ) ){
							$data_atts .= sprintf( 'data-title="%s" ', esc_attr( $v['media-frame']['title'] ) );
						}
						if ( isset( $v['media-frame']['button'] ) ){
							$data_atts .= sprintf( 'data-button="%s" ', esc_attr( $v['media-frame']['button'] ) );
						}
						if ( isset( $v['media-frame']['library'] ) ){
							$data_atts .= sprintf( 'data-library="%s" ', esc_attr( $v['media-frame']['library'] ) );
						}

						$field = '<input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text projects-upload-field" value="' . esc_attr( $data ) . '" />';
						$field .= '<button id="' . esc_attr( $k ) . '" class="projects-upload button"' . $data_atts . '>' . $v['label'] . '</button>';
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'radio':
						$field = '';
						if ( isset( $v['options'] ) && is_array( $v['options'] ) ) {
							foreach ( $v['options'] as $val => $option ){
								$field .= '<p><label for="' . esc_attr( $v['name'] . '-' . $val ) . '"><input id="' . esc_attr( $v['name'] . '-' . $val ) . '" type="radio" name="' . esc_attr( $k ) . '" value="' . esc_attr( $val ) . '" ' . checked( $val, $data, false ) . ' / >'. $option . '</label></p>' . "\n";
							}
						}
						$html .= '<tr valign="top"><th scope="row"><label>' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'checkbox':
						$field = '<p><input id="' . esc_attr( $v['name'] ) . '" type="checkbox" name="' . esc_attr( $k ) . '" value="1" ' . checked( 'yes', $data, false ) . ' / ></p>' . "\n";
						if( isset( $v['description'] ) ) $field .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $v['name'] ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'multicheck':
						$field = '';
						if( isset( $v['options'] ) && is_array( $v['options'] ) ){
							foreach ( $v['options'] as $val => $option ){
								$field .= '<p><label for="' . esc_attr( $v['name'] . '-' . $val ) . '"><input id="' . esc_attr( $v['name'] . '-' . $val ) . '" type="checkbox" name="' . esc_attr( $k ) . '[]" value="' . esc_attr( $val ) . '" ' . checked( 1, in_array( $val, (array) $data ), false ) . ' / >'. $option . '</label></p>' . "\n";
							}
						}
						$html .= '<tr valign="top"><th scope="row"><label>' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					case 'select':
						$field = '<select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" >'. "\n";
						if ( isset( $v['options'] ) && is_array( $v['options'] ) ) {
							foreach ( $v['options'] as $val => $option ){
								$field .= '<option value="' . esc_attr( $val ) . '" ' . selected( $val, $data, false ) . '>'. $option .'</option>' . "\n";
							}
						}
						$field .= '</select>'. "\n";
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					default:
						$field = apply_filters( 'projects_data_field_type_' . $v['type'], null, $k, $data, $v );
						if ( $field ) {
							$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
							if( isset( $v['description'] ) ) $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
							$html .= '</td><tr/>' . "\n";
						}
						break;
				}

			}

			$html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}

		echo $html;
	} // End meta_box_content()

	/**
	 * Display the project images meta box.
	 *
	 * @access public
	 * @return void
	 */
	public function meta_box_content_project_images () {
		global $post, $projects;
		?>
		<div id="project_images_container">
			<ul class="project_images">
				<?php
					if ( metadata_exists( 'post', $post->ID, '_project_image_gallery' ) ) {
						$project_image_gallery = get_post_meta( $post->ID, '_project_image_gallery', true );
					} else {
						// Backwards compat
						$attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids' );
						$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
						$project_image_gallery = implode( ',', $attachment_ids );
					}

					$attachments = array_filter( explode( ',', $project_image_gallery ) );

					if ( $attachments )
						foreach ( $attachments as $attachment_id ) {
							echo '<li class="image" data-attachment_id="' . $attachment_id . '">
								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
								<ul class="actions">
									<li><a href="#" class="delete" title="' . __( 'Delete image', 'projects-by-woothemes' ) . '">&times;</a></li>
									<li><a href="' . get_edit_post_link( $attachment_id ) . '" class="edit">' . __( 'Edit image', 'projects-by-woothemes' ) . '</a></li>
								</ul>
							</li>';
						}
				?>
			</ul>

			<input type="hidden" id="project_image_gallery" name="project_image_gallery" value="<?php echo esc_attr( $project_image_gallery ); ?>" />

		</div>
		<p class="add_project_images hide-if-no-js">
			<a href="#"><?php printf( __( 'Add %s gallery images', 'projects-by-woothemes' ), strtolower( $projects->singular_name ) ); ?></a>
		</p>
		<?php
	} // End meta_box_content_project_images()

	/**
	 * Save meta box fields.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param int $post_id
	 * @return void
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ( get_post_type() != $this->post_type ) || ! isset( $_POST['woo_' . $this->token . '_nonce'] ) || ! wp_verify_nonce( $_POST['woo_' . $this->token . '_nonce'], plugin_basename( $this->dir ) ) ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$field_data 	= $this->get_custom_fields_settings();
		$fields 		= array_keys( $field_data );

		foreach ( $fields as $f ) {

			switch ( $field_data[$f]['type'] ) {
				case 'url':
					${$f} = isset( $_POST[$f] ) ? esc_url( $_POST[$f] ) : '';
					break;
				case 'textarea':
				case 'editor':
					${$f} = isset( $_POST[$f] ) ? wp_kses_post( trim( $_POST[$f] ) ) : '';
					break;
				case 'checkbox':
					${$f} = isset( $_POST[$f] ) ? 'yes' : 'no';
					break;
				case 'multicheck':
					// ensure checkbox is array and whitelist accepted values against options
					${$f} = isset( $_POST[$f] ) && is_array( $field_data[$f]['options'] ) ? (array) array_intersect( (array) $_POST[$f], array_flip( $field_data[$f]['options'] ) ) : '';
					break;
				case 'radio':
				case 'select':
					// whitelist accepted value against options
					$values = array();
					if ( is_array( $field_data[$f]['options'] ) )
					    $values = array_keys( $field_data[$f]['options'] );
					${$f} = isset( $_POST[$f] ) && in_array( $_POST[$f], $values ) ? $_POST[$f] : '';
					break;
				default :
					${$f} = isset( $_POST[$f] ) ? strip_tags( trim( $_POST[$f] ) ) : '';
					break;
			}

			// save it
			update_post_meta( $post_id, '_' . $f, ${$f} );

		}

		// Save the project gallery image IDs.
		$attachment_ids = array_filter( explode( ',', sanitize_text_field( $_POST['project_image_gallery'] ) ) );
		update_post_meta( $post_id, '_project_image_gallery', implode( ',', $attachment_ids ) );

		do_action( 'projects_process_meta', $post_id, $field_data, $fields );

	} // End meta_box_save()

	/**
	 * Get the settings for the custom fields.
	 * @since  1.1.0
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();

		$fields['client'] = array(
		    'name' 			=> __( 'Client', 'projects-by-woothemes' ),
		    'description' 	=> __( 'Enter the client name. (Optional)', 'projects-by-woothemes' ),
		    'type' 			=> 'text',
		    'default' 		=> '',
		    'section' 		=> 'info'
		);

		$fields['url'] = array(
		    'name' 			=> __( 'URL', 'projects-by-woothemes' ),
		    'description' 	=> __( 'Enter the project URL. (Optional)', 'projects-by-woothemes' ),
		    'type' 			=> 'url',
		    'default' 		=> '',
		    'section' 		=> 'info'
		);

		return apply_filters( 'projects_custom_fields', $fields );
	} // End get_custom_fields_settings()

	/**
	 * Customise the "Enter title here" text.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param string $title
	 * @return void
	 */
	public function enter_title_here ( $title ) {
		global $projects;
		if ( get_post_type() == $this->post_type ) {
			$title = sprintf( __( 'Enter the %s title here', 'projects-by-woothemes' ), strtolower( $projects->singular_name ) );
		}
		return $title;
	} // End enter_title_here()

	/**
	 * Enqueue post type admin CSS.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function enqueue_admin_styles () {
		global $pagenow;

		wp_enqueue_style( 'projects-admin', $this->assets_url . '/css/admin.css', array(), '1.0.0' );

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && get_post_type() == $this->post_type ) {
			wp_enqueue_script( 'projects-admin', $this->assets_url . '/js/admin.js', array( 'jquery' ), '1.0.0', true );
		

			wp_localize_script( 'projects-admin', 'woo_projects_admin',
					array(
						'gallery_title' 	=> __( 'Add Images to Project Gallery', 'projects-by-woothemes' ),
						'gallery_button' 	=> __( 'Add to gallery', 'projects-by-woothemes' ),
						'delete_image'		=> __( 'Delete image', 'projects-by-woothemes' ),
						'default_title' 	=> __( 'Upload', 'projects-by-woothemes' ),
						'default_button' 	=> __( 'Select this', 'projects-by-woothemes' ),
					)
				);
			
		}

	} // End enqueue_admin_styles()

	/**
	 * Display an admin notice, if not on the settings screen and if projects page isn't set.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function configuration_admin_notice () {
		if ( ( isset( $_GET['page'] ) && 'projects-settings-page' == $_GET['page'] ) ) return;

		$projects_page = projects_get_page_id( 'projects' );

		if ( -1 == $projects_page ) {
			$url = add_query_arg( 'post_type', 'project', admin_url( 'edit.php' ) );
			$url = add_query_arg( 'page', 'projects-settings-page', $url );
			echo '<div class="updated fade"><p>' . sprintf( __( '%sProjects by WooThemes is almost ready.%s To get started, %sconfigure your projects page%s.', 'projects-by-woothemes' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' ) . '</p></div>' . "\n";
		}
	} // End configuration_admin_notice()

	/**
	 * Replace the featured image meta box
	 * Functionality is identical, this is purely to change the label.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_label() {
		global $projects;
	    remove_meta_box( 'postimagediv', 'project', 'side' );
	    add_meta_box( 'postimagediv', sprintf( __( '%s Cover Image', 'projects-by-woothemes' ), $projects->singular_name ), 'post_thumbnail_meta_box', 'project', 'side' );
	} // End featured_image_label()

	/**
	 * Tweak the 'Set featured image' string to say 'Set cover image'.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_set_link( $content ) {
		$post_type = $this->get_current_post_type();

		if ( 'project' == $post_type ) {
	    	$content = str_replace( __( 'Set featured image' ), __( 'Set cover image', 'projects-by-woothemes' ), $content );
		}

		return $content;
	} // End featured_image_set_link()

	/**
	 * Tweak the 'Remove featured image' string to say 'Remove cover image'.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_remove_link( $content ) {
		$post_type = $this->get_current_post_type();

	    if ( 'project' == $post_type ) {
	    	$content = str_replace( __( 'Remove featured image' ), __( 'Remove cover image', 'projects-by-woothemes' ), $content );
		}

		return $content;
	} // End featured_image_remove_link()

	/**
	 * Tweak the featured image strings in the media popup
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_popup_set_link( $strings ) {
		$post_type = $this->get_current_post_type();
		if ( 'project' == $post_type ) {
			$strings['setFeaturedImageTitle'] 	= __( 'Set Cover Image', 'projects-by-woothemes' );
			$strings['setFeaturedImage']		= __( 'Set cover image', 'projects-by-woothemes' );
		}
		return $strings;
	} // End featured_image_popup_set_link()

	/**
	 * Determine what post type the current admin page is related to
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_current_post_type() {
        global $post, $typenow, $current_screen;

        if ( $post && $post->post_type )
            return $post->post_type;

        elseif ( $typenow )
            return $typenow;

        elseif ( $current_screen && $current_screen->post_type )
            return $current_screen->post_type;

        elseif ( isset( $_REQUEST['post_type'] ) )
            return sanitize_key( $_REQUEST['post_type'] );

        return null;
    } // End get_current_post_type()

} // End Class
