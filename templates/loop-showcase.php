<?php
/**
 * Loop-shop (deprecated)
 *
 * Outputs a project loop
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.6.4
 * @deprecated 	1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

_deprecated_file( basename(__FILE__), '1.6', '', 'Use your own loop code, as well as the content-project.php template. loop-shop.php will be removed in WC 2.1.' );
?>

<?php if ( have_posts() ) : ?>

	<?php do_action('woocommerce_before_shop_loop'); ?>

	<?php woocommerce_project_loop_start(); ?>

		<?php woocommerce_project_subcategories(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php woocommerce_get_template_part( 'content', 'project' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_project_loop_end(); ?>

	<?php do_action('woocommerce_after_shop_loop'); ?>

<?php else : ?>

	<?php if ( ! woocommerce_project_subcategories( array( 'before' => woocommerce_project_loop_start( false ), 'after' => woocommerce_project_loop_end( false ) ) ) ) : ?>

		<p><?php _e( 'No projects found which match your selection.', 'woocommerce' ); ?></p>

	<?php endif; ?>

<?php endif; ?>

<div class="clear"></div>