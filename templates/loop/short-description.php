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

?>
<div itemprop="description" class="short-description">
	<?php the_excerpt(); ?>
</div>