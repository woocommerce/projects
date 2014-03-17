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

?>
<div itemprop="description" class="short-description">
	<?php echo apply_filters( 'post_excerpt', wpautop( get_the_excerpt() ) ) ?>
</div>