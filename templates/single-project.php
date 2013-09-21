<?php
/**
 * The Template for displaying all single projects.
 *
 * Override this template by copying it to yourtheme/woothemes-projects/single-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'showcase' ); ?>

	<?php
		/**
		 * woothemes_projects_before_main_content hook
		 *
		 * @hooked woothemes_projects_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'woothemes_projects_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php woothemes_projects_get_template_part( 'content', 'single-project' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woothemes_projects_after_main_content hook
		 *
		 * @hooked woothemes_projects_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woothemes_projects_after_main_content' );
	?>

	<?php
		/**
		 * woothemes_projects_sidebar hook
		 *
		 * @hooked woothemes_projects_get_sidebar - 10
		 */
		do_action( 'woothemes_projects_sidebar' );
	?>

<?php get_footer( 'showcase' ); ?>