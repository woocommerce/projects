<?php
/**
 * Content wrappers
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$template = get_option('template');

if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
?>
<!-- #content Starts -->
<?php if ( function_exists( 'woo_content_before' ) ) woo_content_before(); ?>
<div id="content" class="col-full">

    <!-- #main Starts -->
    <?php if ( function_exists( 'woo_main_before' ) ) woo_main_before(); ?>
    <div id="main" class="col-left">

<?php
} else {
	switch( $template ) {
		case 'twentyeleven' :
			echo '<div id="primary"><div id="content" role="main">';
			break;
		case 'twentytwelve' :
			echo '<div id="primary" class="site-content"><div id="content" role="main">';
			break;
		case 'twentythirteen' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
			break;
		default :
			echo '<div id="container"><div id="content" role="main">';
			break;
	}
}