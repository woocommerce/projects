<?php
/**
 * Single project description
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

if ( ! $post->post_excerpt ) return;
?>
<div class="single-project-short-description" itemprop="description">
	<?php echo apply_filters( 'projects_short_description', wpautop( $post->post_excerpt ) ) ?>
</div>