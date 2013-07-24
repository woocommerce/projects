<?php
/**
 * The template for displaying project content in the single-project.php template
 *
 * Override this template by copying it to yourtheme/woothemes-portfolio/content-single-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Portfolio/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * woothemes_portfolio_before_single_product hook
	 *
	 * @hooked wc_print_messages - 10
	 */
	 do_action( 'woothemes_portfolio_before_single_product' );
?>

<div itemscope itemtype="http://schema.org/Product" id="project-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woothemes_portfolio_show_product_images hook
		 *
		 * @hooked woothemes_portfolio_show_product_images - 20
		 */
		do_action( 'woothemes_portfolio_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * woocommerce_single_product_summary hook
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
			do_action( 'woothemes_portfolio_single_project_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * woothemes_portfolio_after_single_product_summary hook
		 *
		 */
		do_action( 'woothemes_portfolio_after_single_project_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woothemes_portfolio_after_single_project' ); ?>