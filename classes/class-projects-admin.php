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
		$this->assets_url 	= $this->assets_url 	= esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token 		= 'projects';
		$this->post_type 	= 'project';

		global $pagenow;

		add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
		add_action( 'admin_print_styles', array( $this, 'enqueue_admin_styles' ), 10 );
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_action( 'admin_notices', array( $this, 'configuration_admin_notice' ) );
		add_action( 'do_meta_boxes', array( $this, 'featured_image_label' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'featured_image_set_link' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'featured_image_remove_link' ) );
		add_filter( 'media_view_strings', array( $this, 'featured_image_popup_set_link' ) );

		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
			add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
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
	                'show_option_all' 	=> __( 'Show All ' . $tax_obj->label, 'projects' ),
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
	}

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
	}

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
		$new_columns['image'] = __( 'Cover Image', 'projects' );

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
	    1 	=> sprintf( __( '%1$s updated. %2$sView %3$s%4$s', 'projects' ), $projects->singular_name, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    2 	=> __( 'Custom field updated.', 'projects' ),
	    3 	=> __( 'Custom field deleted.', 'projects' ),
	    4 	=> sprintf( __( '%s updated.', 'projects' ), $projects->singular_name ),
	    /* translators: %s: date and time of the revision */
	    5 	=> isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from 2$%s', 'projects' ), $projects->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 	=> sprintf( __( '$1%s published. $2%sView $3%s$4%s', 'projects' ), $projects->singular_name, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    7 	=> sprintf( __( '%s saved.' ), $projects->singular_name ),
	    8 	=> sprintf( __( '%1$s submitted. %2$sPreview %3$s%4$s', 'projects' ), $projects->singular_name, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    9 	=> sprintf( __( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s', 'projects' ), $projects->singular_name,
	      // translators: Publish box date format, see http://php.net/date
	      '<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink($post_ID) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
	    10 	=> sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s', 'projects' ), $projects->singular_name, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', strtolower( $projects->singular_name ), '</a>' ),
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
		add_meta_box( 'postexcerpt', sprintf( __( '%s Short Description', 'projects' ), $projects->singular_name ), array( $this, 'meta_box_short_description' ), 'project', 'normal' );

		// Project Details Meta Box Load
		add_meta_box( 'project-data', sprintf( __( '%s Details', 'projects' ), array( $this, 'meta_box_content' ), $projects->singular_name ), $this->post_type, 'normal', 'high' );

		// Project Images Meta Bog Load
		add_meta_box( 'project-images', sprintf( __( '%s Gallery', 'projects' ), $projects->singular_name ), array( $this, 'meta_box_content_project_images' ), 'project', 'side' );

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
	}

	/**
	 * The contents of the Project info meta box.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;
		$fields 	= get_post_custom( $post_id );
		$field_data = $this->get_custom_fields_settings();

		$html = '';

		$html .= '<input type="hidden" name="woo_' . $this->token . '_noonce" id="woo_' . $this->token . '_noonce" value="' . wp_create_nonce( plugin_basename( $this->dir ) ) . '" />';

		if ( 0 < count( $field_data ) ) {
			$html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";

			foreach ( $field_data as $k => $v ) {
				$data = $v['default'];
				if ( isset( $fields['_' . $k] ) && isset( $fields['_' . $k][0] ) ) {
					$data = $fields['_' . $k][0];
				}

				$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
				$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
				$html .= '</td><tr/>' . "\n";
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
									<li><a href="#" class="delete" title="' . __( 'Delete image', 'projects' ) . '">&times;</a></li>
								</ul>
							</li>';
						}
				?>
			</ul>

			<input type="hidden" id="project_image_gallery" name="project_image_gallery" value="<?php echo esc_attr( $project_image_gallery ); ?>" />

		</div>
		<p class="add_project_images hide-if-no-js">
			<a href="#"><?php printf( __( 'Add %s gallery images', 'projects' ), strtolower( $projects->singular_name ) ); ?></a>
		</p>
		<script type="text/javascript">
			jQuery(document).ready(function($){

				// Uploading files
				var project_gallery_frame;
				var $image_gallery_ids 	= $( '#project_image_gallery' );
				var $project_images 	= $( '#project_images_container ul.project_images' );

				jQuery( '.add_project_images' ).on( 'click', 'a', function( event ) {

					var $el 			= $(this);
					var attachment_ids 	= $image_gallery_ids.val();

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( project_gallery_frame ) {
						project_gallery_frame.open();
						return;
					}

					// Create the media frame.
					project_gallery_frame = wp.media.frames.downloadable_file = wp.media({
						// Set the title of the modal.
						title: '<?php _e( 'Add Images to Project Gallery', 'projects' ); ?>',
						button: {
							text: '<?php _e( 'Add to gallery', 'projects' ); ?>',
						},
						multiple: true
					});

					// When an image is selected, run a callback.
					project_gallery_frame.on( 'select', function() {

						var selection = project_gallery_frame.state().get( 'selection' );

						selection.map( function( attachment ) {

							attachment = attachment.toJSON();

							if ( attachment.id ) {
								attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

								$project_images.append('\
									<li class="image" data-attachment_id="' + attachment.id + '">\
										<img src="' + attachment.url + '" />\
										<ul class="actions">\
											<li><a href="#" class="delete" title="<?php _e( 'Delete image', 'projects' ); ?>">&times;</a></li>\
										</ul>\
									</li>');
							}

						} );

						$image_gallery_ids.val( attachment_ids );
					});

					// Finally, open the modal.
					project_gallery_frame.open();
				});

				// Image ordering
				$project_images.sortable({
					items: 'li.image',
					cursor: 'move',
					scrollSensitivity:40,
					forcePlaceholderSize: true,
					forceHelperSize: false,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'projects-metabox-sortable-placeholder',
					start:function(event,ui){
						ui.item.css( 'background-color','#f6f6f6' );
					},
					stop:function(event,ui){
						ui.item.removeAttr( 'style' );
					},
					update: function(event, ui) {
						var attachment_ids = '';

						$( '#project_images_container ul li.image' ).css( 'cursor','default' ).each(function() {
							var attachment_id = jQuery(this).attr( 'data-attachment_id' );
							attachment_ids = attachment_ids + attachment_id + ',';
						});

						$image_gallery_ids.val( attachment_ids );
					}
				});

				// Remove images
				$( '#project_images_container' ).on( 'click', 'a.delete', function() {

					$(this).closest( 'li.image' ).remove();

					var attachment_ids = '';

					$( '#project_images_container ul li.image' ).css( 'cursor','default' ).each(function() {
						var attachment_id = jQuery(this).attr( 'data-attachment_id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});

					$image_gallery_ids.val( attachment_ids );

					return false;
				} );

			});
		</script>
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
		if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['woo_' . $this->token . '_noonce'], plugin_basename( $this->dir ) ) ) {
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

			${$f} = strip_tags(trim($_POST[$f]));

			// Escape the URLs.
			if ( 'url' == $field_data[$f]['type'] ) {
				${$f} = esc_url( ${$f} );
			}

			if ( get_post_meta( $post_id, '_' . $f ) == '' ) {
				add_post_meta( $post_id, '_' . $f, ${$f}, true );
			} elseif( ${$f} != get_post_meta( $post_id, '_' . $f, true ) ) {
				update_post_meta( $post_id, '_' . $f, ${$f} );
			} elseif ( ${$f} == '' ) {
				delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
			}
		}

		// Save the project gallery image IDs.
		$attachment_ids = array_filter( explode( ',', sanitize_text_field( $_POST['project_image_gallery'] ) ) );
		update_post_meta( $post_id, '_project_image_gallery', implode( ',', $attachment_ids ) );
	} // End meta_box_save()

	/**
	 * Get the settings for the custom fields.
	 * @since  1.1.0
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();

		$fields['client'] = array(
		    'name' 			=> __( 'Client', 'projects' ),
		    'description' 	=> __( 'Enter the client name. (Optional)', 'projects' ),
		    'type' 			=> 'text',
		    'default' 		=> '',
		    'section' 		=> 'info'
		);

		$fields['url'] = array(
		    'name' 			=> __( 'URL', 'projects' ),
		    'description' 	=> __( 'Enter the project URL. (Optional)', 'projects' ),
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
			$title = sprintf( __( 'Enter the %s title here', 'projects' ), strtolower( $projects->singular_name ) );
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
		wp_register_style( 'projects-admin', $this->assets_url . '/css/admin.css', array(), '1.0.0' );
		wp_enqueue_style( 'projects-admin' );
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
			echo '<div class="updated fade"><p>' . sprintf( __( '%sProjects by WooThemes is almost ready.%s To get started, %sconfigure your projects page%s.', 'projects' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' ) . '</p></div>' . "\n";
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
	    add_meta_box( 'postimagediv', sprintf( __( '%s Cover Image', 'projects' ), $projects->singular_name ), 'post_thumbnail_meta_box', 'project', 'side' );
	}

	/**
	 * Tweak the 'Set featured image' string to say 'Set cover image'.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_set_link( $content ) {
		$post_type = $this->get_current_post_type();

		if ( 'project' == $post_type ) {
	    	$content = str_replace( __( 'Set featured image' ), __( 'Set cover image', 'projects' ), $content );
		}

		return $content;
	}

	/**
	 * Tweak the 'Remove featured image' string to say 'Remove cover image'.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_remove_link( $content ) {
		$post_type = $this->get_current_post_type();

	    if ( 'project' == $post_type ) {
	    	$content = str_replace( __( 'Remove featured image' ), __( 'Remove cover image', 'projects' ), $content );
		}

		return $content;
	}

	/**
	 * Tweak the featured image strings in the media popup
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function featured_image_popup_set_link( $strings ) {
		$post_type = $this->get_current_post_type();
		if ( 'project' == $post_type ) {
			$strings['setFeaturedImageTitle'] 	= __( 'Set Cover Image', 'projects' );
			$strings['setFeaturedImage']		= __( 'Set cover image', 'projects' );
		}
		return $strings;
	}

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
    }

} // End Class
?>