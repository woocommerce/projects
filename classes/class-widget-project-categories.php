<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Project Categories Widget
 *
 * A WooThemes standardized project categories widget.
 *
 * @package WordPress
 * @subpackage Projects
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * protected $projects_widget_cssclass
 * protected $projects_widget_description
 * protected $projects_widget_idbase
 * protected $projects_widget_title
 *
 * - __construct()
 * - widget()
 * - update()
 * - form()
 * - get_orderby_options()
 */
class Woothemes_Widget_Project_Categories extends WP_Widget {
	protected $projects_widget_cssclass;
	protected $projects_widget_description;
	protected $projects_widget_idbase;
	protected $projects_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->projects_widget_cssclass 	= 'widget_projects_categories';
		$this->projects_widget_description 	= __( 'Project Categories', 'projects-by-woothemes' );
		$this->projects_widget_idbase 		= 'woothemes-project-categories';
		$this->projects_widget_title 		= __( 'Project Categories', 'projects-by-woothemes' );

		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> $this->projects_widget_cssclass,
			'description' 	=> $this->projects_widget_description
			);

		/* Widget control settings. */
		$control_ops = array(
			'id_base' 	=> $this->projects_widget_idbase
			);

		/* Create the widget. */
		$this->WP_Widget( $this->projects_widget_idbase, $this->projects_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$args = array();

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { $args['title'] = $title; }

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->projects_widget_cssclass . '_top' );

		// Checkbox values.
		if ( isset( $instance['count'] ) ) { $args['count'] = $instance['count']; }
		if ( isset( $instance['hierarchical'] ) ) { $args['hierarchical'] = $instance['hierarchical']; }

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Display the project categories
		$project_taxonomies = get_object_taxonomies( 'project' );

		if ( count( $project_taxonomies ) > 0) {
		     foreach ( $project_taxonomies as $project_tax ) {
			     $args = array(
		         	  'orderby' 		=> 'name',
			          'show_count' 		=> $instance['count'],
		        	  'pad_counts' 		=> 0,
			          'hierarchical' 	=> $instance['hierarchical'],
		        	  'taxonomy' 		=> $project_tax,
		        	  'title_li' 		=> '',
		        	);

			     echo '<ul class="projects_categories_list_widget">';

			     wp_list_categories( $args );

			     echo '</ul>';
		     }
		}

		echo $after_widget;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->projects_widget_cssclass . '_bottom' );

	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 			= strip_tags( $new_instance['title'] );

		/* Escape checkbox values */
		$instance['count']  		= esc_attr( $new_instance['count'] );
		$instance['hierarchical']  	= esc_attr( $new_instance['hierarchical'] );

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
			'title' 		=> __( 'Project Categories', 'projects-by-woothemes' ),
			'count'			=> 1,
			'hierarchical'	=> 1,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'projects-by-woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<!-- Widget Show Count: Checkbox Input -->
		<p>
			<input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['count'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show Count:', 'projects-by-woothemes' ); ?></label>
		</p>

		<!-- Widget Hierarchical: Checkbox Input -->
		<p>
			<input id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['hierarchical'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show Hierarchy:', 'projects-by-woothemes' ); ?></label>
		</p>
<?php
	} // End form()

} // End Class

/* Register the widget. */
register_widget( 'Woothemes_Widget_Project_Categories' );