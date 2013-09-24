<?php
/**
 * Loop-showcase (deprecated)
 *
 * Outputs a project loop
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 * @deprecated 	1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

_deprecated_file( basename(__FILE__), '1.6', '', 'Use your own loop code, as well as the content-project.php template. loop-showcase.php will be removed in WC 2.1.' );
?>

<?php if ( have_posts() ) : ?>

	<?php do_action('woothemes_projects_before_showcase_loop'); ?>

	<?php woothemes_projects_project_loop_start(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php woothemes_projects_get_template_part( 'content', 'project' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woothemes_projects_project_loop_end(); ?>

	<?php do_action('woothemes_projects_after_showcase_loop'); ?>

<?php else : ?>

	<p><?php _e( 'No projects found which match your selection.', 'woothemes-projects' ); ?></p>

<?php endif; ?>

<div class="clear"></div>