<?php
/**
 * Content wrappers
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
?>
</div><!-- /#main -->
    <?php if ( function_exists( 'woo_main_after' ) ) woo_main_after(); woothemes_projects_get_sidebar(); /* TODO: Make this work correctly on the woo_main_after hook. */ ?>
</div><!-- /#content -->
<?php if ( function_exists( 'woo_content_after' ) ) woo_content_after(); ?>
<?php
} else {
?>
	</div>
</div>
<?php } ?>