<?php
/**
 * The Template for displaying projects in a project category. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/projects/taxonomy-project-category.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

projects_get_template( 'archive-project.php' );