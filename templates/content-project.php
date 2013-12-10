<?php
/**
 * The template for displaying project content within loops.
 *
 * Override this template by copying it to yourtheme/projects/content-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $project, $projects_loop;

// Store loop count we're currently on
if ( empty( $projects_loop['loop'] ) )
	$projects_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $projects_loop['columns'] ) )
	$projects_loop['columns'] = apply_filters( 'projects_loop_columns', 2 );

// Increase loop count
$projects_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $projects_loop['loop'] - 1 ) % $projects_loop['columns'] || 1 == $projects_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $projects_loop['loop'] % $projects_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'projects_before_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>" class="project-permalink">

		<?php
			/**
			 * projects_loop_item hook
			 *
			 * @hooked projects_template_loop_project_thumbnail - 10
			 * @hooked projects_template_loop_project_title - 20
			 */
			do_action( 'projects_loop_item' );
		?>

	</a>

	<?php
		/**
		 * projects_after_loop_item hook
		 *
		 * @hooked projects_template_short_description - 10
		 */
		do_action( 'projects_after_loop_item' );
	?>

</li>