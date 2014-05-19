<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Widget
 *
 * A WooThemes standardized projects widget.
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
 */
class Woothemes_Widget_Projects extends WP_Widget {
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
		$this->projects_widget_cssclass 	= 'widget_projects_items';
		$this->projects_widget_description 	= __( 'Recent projects listed on your site.', 'projects-by-woothemes' );
		$this->projects_widget_idbase 		= 'projects';
		$this->projects_widget_title 		= __( 'Recent Projects', 'projects-by-woothemes' );

		// Cache
		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );

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

		$cache = wp_cache_get( 'widget_projects_items', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$args = array();

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { $args['title'] = $title; }

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->projects_widget_cssclass . '_top' );

		// Integer values.
		if ( isset( $instance['limit'] ) && ( 0 < count( $instance['limit'] ) ) ) { $args['limit'] = intval( $instance['limit'] ); }

		// Display the projects.

		$args = array(
			'post_type'				=> 'project',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $args['limit'],
			'orderby' 				=> 'date',
			'order' 				=> 'DESC'
		);

		$r = new WP_Query( $args );

		if ( $r->have_posts() ) {

			echo $before_widget;

			if ( $title )
				echo $before_title . $title . $after_title;

			echo '<ul class="projects_list_widget">';

			while ( $r->have_posts()) {
				$r->the_post();
				projects_get_template_part( 'content', 'project-widget' );
			}

			echo '</ul>';

			echo $after_widget;

			wp_reset_postdata();
		}

		// Add actions for plugins/themes to hook onto.
		do_action( $this->projects_widget_cssclass . '_bottom' );

		$cache[ $widget_id ] = ob_get_flush();

		wp_cache_set('widget_projects_items', $cache, 'widget');

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

		/* Make sure the integer values are definitely integers. */
		$instance['limit'] 			= intval( $new_instance['limit'] );

		/* Flush cache. */
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_projects_items']) )
			delete_option('widget_projects_items');

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
			'title' 		=> __( 'Recent Projects', 'projects-by-woothemes' ),
			'limit' 		=> 5,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'projects-by-woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<!-- Widget Limit: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'projects-by-woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>
<?php
	} // End form()

	/**
	 * Flush widget cache
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete('widget_projects_items', 'widget');
	}

} // End Class

/* Register the widget. */
register_widget( 'Woothemes_Widget_Projects' );