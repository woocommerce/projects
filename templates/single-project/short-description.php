<?php
/**
 * Single project short description
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

if ( ! $post->post_excerpt ) return;
?>
<div itemprop="description">
	<?php echo apply_filters( 'woothemes_projects_short_description', $post->post_excerpt ) ?>
</div>