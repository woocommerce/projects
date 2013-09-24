<?php
/**
 * The template for displaying project content in the single-project.php template
 *
 * Override this template by copying it to yourtheme/woothemes-projects/content-single-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * woothemes_projects_before_single_project hook
	 *
	 * @hooked wc_print_messages - 10
	 */
	 do_action( 'woothemes_projects_before_single_project' );
?>

<div id="project-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woothemes_projects_show_project_images hook
		 *
		 * @hooked woothemes_projects_show_project_images - 20
		 */
		do_action( 'woothemes_projects_before_single_project_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * woothemes_projects_single_project_summary hook
			 *
			 * @hooked woothemes_projects_template_single_title - 5
			 * @hooked woothemes_projects_template_single_excerpt - 20
			 * @hooked woothemes_projects_template_single_meta - 40
			 * @hooked woothemes_projects_template_single_sharing - 50
			 */
			do_action( 'woothemes_projects_single_project_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * woothemes_projects_after_single_project_summary hook
		 *
		 */
		do_action( 'woothemes_projects_after_single_project_summary' );
	?>

</div><!-- #project-<?php the_ID(); ?> -->

<?php do_action( 'woothemes_projects_after_single_project' ); ?>