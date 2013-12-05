<?php
/**
 * The template for displaying project content in the single-project.php template
 *
 * Override this template by copying it to yourtheme/projects/content-single-project.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * projects_before_single_project hook
	 *
	 */
	 do_action( 'projects_before_single_project' );
?>

<div id="project-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * projects_show_project_images hook
		 *
		 * @hooked projects_template_single_gallery - 20
		 */
		do_action( 'projects_before_single_project_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * projects_single_project_summary hook
			 *
			 * @hooked projects_template_single_title - 10
			 * @hooked projects_template_single_excerpt - 20
			 * @hooked projects_template_single_meta - 40
			 */
			do_action( 'projects_single_project_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * projects_after_single_project_summary hook
		 *
		 */
		do_action( 'projects_after_single_project_summary' );
	?>

</div><!-- #project-<?php the_ID(); ?> -->

<?php
	/**
	 * projects_after_single_project hook
	 *
	 * @hooked projects_single_pagination - 10
	 */
	do_action( 'projects_after_single_project' );