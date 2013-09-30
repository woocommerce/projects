<?php
/**
 * Single project description
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

if ( ! $post->post_excerpt ) return;
?>
<div itemprop="description" class="short-description">
	<?php echo apply_filters( 'woothemes_projects_description', $post->post_excerpt ) ?>
</div>