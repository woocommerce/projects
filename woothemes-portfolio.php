<?php
/**
 * Plugin Name: Portfolio
 * Plugin URI: http://woothemes.com/
 * Description: Hi, I'm your portfolio showcase plugin for WordPress. Show off your recent work using our shortcode, widget or template tag.
 * Author: WooThemes
 * Version: 1.0.0
 * Author URI: http://woothemes.com/
 *
 * @package WordPress
 * @subpackage Woothemes_Portfolio
 * @author Matty
 * @since 1.0.0
 */

require_once( 'classes/class-woothemes-portfolio.php' );
require_once( 'classes/class-woothemes-portfolio-taxonomy.php' );
require_once( 'woothemes-portfolio-template.php' );
require_once( 'classes/class-woothemes-widget-portfolio.php' );
global $woothemes_portfolio;
$woothemes_portfolio = new Woothemes_Portfolio( __FILE__ );
$woothemes_portfolio->version = '1.0.0';