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
$excerpt_raw = '';
if ( isset( $post->post_excerpt ) ) {
	$excerpt_raw = $post->post_excerpt;
} // End If Statement
if ( '' === trim( $excerpt_raw ) ) {
	$excerpt_raw = get_the_excerpt();
} // End If Statement
?>
<div itemprop="description" class="short-description">
	<?php echo apply_filters( 'post_excerpt', wpautop( $excerpt_raw ) ) ?>
</div>