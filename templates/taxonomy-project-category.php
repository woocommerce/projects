<?php
/**
 * The Template for displaying projects in a project category. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/woothemes-projects/taxonomy-project-category.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

woothemes_projects_get_template( 'archive-project.php' );