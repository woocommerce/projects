<?php
/**
 * The Template for displaying project archives, including the main showcase page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woothemes_projects/archive-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
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

		<?php if ( apply_filters( 'woothemes_projects_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php woothemes_projects_page_title(); ?></h1>

		<?php endif; ?>

		<?php do_action( 'woothemes_projects_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woothemes_projects_before_showcase_loop hook
				 *
				 * @hooked woothemes_projects_template_categories - 10
				 */
				do_action( 'woothemes_projects_before_showcase_loop' );
			?>

			<?php woothemes_projects_project_loop_start(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php woothemes_projects_get_template_part( 'content', 'project' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woothemes_projects_project_loop_end(); ?>

			<?php
				/**
				 * woothemes_projects_after_showcase_loop hook
				 *
				 * @hooked woothemes_projects_pagination - 10
				 */
				do_action( 'woothemes_projects_after_showcase_loop' );
			?>

		<?php else : ?>

			<?php woothemes_projects_get_template( 'loop/no-projects-found.php' ); ?>

		<?php endif; ?>

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