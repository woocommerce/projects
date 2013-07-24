<?php
/**
 * The Template for displaying all single projects.
 *
 * Override this template by copying it to yourtheme/woothemes-portfolio/single-project.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Portfolio/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header('showcase'); ?>

	<?php
		/**
		 * woothemes_portfolio_before_main_content hook
		 *
		 * @hooked woothemes_portfolio_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action('woothemes_portfolio_before_main_content');
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php woothemes_portfolio_get_template_part( 'content', 'single-project' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woothemes_portfolio_after_main_content hook
		 *
		 * @hooked woothemes_portfolio_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woothemes_portfolio_after_main_content');
	?>

	<?php
		/**
		 * woothemes_portfolio_sidebar hook
		 *
		 * @hooked woothemes_portfolio_get_sidebar - 10
		 */
		do_action('woothemes_portfolio_sidebar');
	?>

<?php get_footer('showcase'); ?>