<?php
/**
 * Plugin Name: Projects
 * Plugin URI: http://woothemes.com/
 * Description: Hi, I'm your project showcase plugin for WordPress. Show off your recent work using our shortcode, widget or template tag.
 * Author: WooThemes
 * Version: 1.2.0
 * Author URI: http://woothemes.com/
 *
 * @package WordPress
 * @subpackage Projects
 * @author Matty
 * @since 1.0.0
 */

require_once( 'classes/class-projects.php' );
require_once( 'classes/class-projects-taxonomy.php' );
require_once( 'classes/class-projects-shortcodes.php' );
require_once( 'classes/class-projects-settings.php' );
require_once( 'classes/class-widget-projects.php' );
require_once( 'classes/class-widget-project-categories.php' );

require_once( 'projects-template.php' );
require_once( 'projects-core-functions.php' );
require_once( 'projects-hooks.php' );

global $projects;
$projects = new Projects( __FILE__ );