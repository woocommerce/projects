<?php
/**
 * Projects_Shortcodes class.
 *
 * @class 		Projects_Shortcodes
 * @version		1.0.0
 * @package 	WordPress
 * @subpackage 	Projects/Classes
 * @category	Class
 * @author 		WooThemes
 */
class Projects_Shortcodes {

	public function __construct() {
		// Regular shortcodes
		add_shortcode( 'projects', array( $this, 'projects' ) );
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class' 	=> 'projects',
			'before' 	=> null,
			'after' 	=> null
		)
	){
		ob_start();

		$before 		= empty( $wrapper['before'] ) ? '<div class="' . $wrapper['class'] . '">' : $wrapper['before'];
		$after 			= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

	/**
	 * Recent Products shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public function projects( $atts ) {

		global $projects_loop;

		extract( shortcode_atts( array(
			'limit' 				=> '12',
			'columns' 				=> '2',
			'orderby' 				=> 'date',
			'order' 				=> 'desc',
			'exclude_categories'	=> null,
		), $atts ) );

		$args = array(
			'post_type'				=> 'project',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $limit,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'tax_query' 			=> array(
										array(
											'taxonomy' 	=> 'project-category',
											'field' 	=> 'id',
											'terms' 	=> explode( ',', $exclude_categories ),
											'operator' 	=> 'NOT IN'
										)
									)
		);

		ob_start();

		$projects = new WP_Query( apply_filters( 'projects_query', $args, $atts ) );

		$projects_loop['columns'] = $columns;

		if ( $projects->have_posts() ) : ?>

			<?php projects_project_loop_start(); ?>

				<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>

					<?php projects_get_template_part( 'content', 'project' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php projects_project_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="projects columns-' . $columns . '">' . ob_get_clean() . '</div>';

	}

}


new Projects_Shortcodes();