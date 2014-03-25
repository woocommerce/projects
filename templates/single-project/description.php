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

if ( ! $post->post_content ) return;
?>
<div class="single-project-description" itemprop="description">
	<?php echo apply_filters( 'projects_description', the_content() ); ?>
</div>