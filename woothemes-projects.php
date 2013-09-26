<?php
/**
 * Plugin Name: Projects
 * Plugin URI: http://woothemes.com/
 * Description: Hi, I'm your project showcase plugin for WordPress. Show off your recent work using our shortcode, widget or template tag.
 * Author: WooThemes
 * Version: 1.0.0
 * Author URI: http://woothemes.com/
 *
 * @package WordPress
 * @subpackage Woothemes_Projects
 * @author Matty
 * @since 1.0.0
 */

require_once( 'classes/class-woothemes-projects.php' );
require_once( 'classes/class-woothemes-projects-taxonomy.php' );
require_once( 'classes/class-woothemes-projects-shortcodes.php' );
// require_once( 'classes/class-woothemes-widget-projects.php' );

require_once( 'woothemes-projects-template.php' );
require_once( 'woothemes-projects-core-functions.php' );
require_once( 'woothemes-projects-hooks.php' );

global $woothemes_projects;
$woothemes_projects = new Woothemes_Projects( __FILE__ );
$woothemes_projects->version = '1.0.0';