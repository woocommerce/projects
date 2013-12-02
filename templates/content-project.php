<?php
/**
 * The template for displaying project content within loops.
 *
 * Override this template by copying it to yourtheme/woothemes_projects/content-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $project, $woothemes_projects_loop;

// Store loop count we're currently on
if ( empty( $woothemes_projects_loop['loop'] ) )
	$woothemes_projects_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woothemes_projects_loop['columns'] ) )
	$woothemes_projects_loop['columns'] = apply_filters( 'loop_showcase_columns', 2 );

// Increase loop count
$woothemes_projects_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woothemes_projects_loop['loop'] - 1 ) % $woothemes_projects_loop['columns'] || 1 == $woothemes_projects_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woothemes_projects_loop['loop'] % $woothemes_projects_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woothemes_projects_before_showcase_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>" class="project-permalink">

		<?php
			/**
			 * woothemes_projects_before_showcase_loop_item_title hook
			 *
			 * @hooked woothemes_projects_show_project_loop_sale_flash - 10
			 * @hooked woothemes_projects_template_loop_project_thumbnail - 10
			 */
			do_action( 'woothemes_projects_before_showcase_loop_item_title' );
		?>

		<h3><?php the_title(); ?></h3>

		<?php
			/**
			 * woothemes_projects_after_showcase_loop_item_title hook
			 *
			 * @hooked woothemes_projects_template_loop_price - 10
			 */
			do_action( 'woothemes_projects_after_showcase_loop_item_title' );
		?>

	</a>

	<?php do_action( 'woothemes_projects_after_showcase_loop_item' ); ?>

</li>