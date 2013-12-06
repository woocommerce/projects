<?php
/**
 * Single Project Meta
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
?>
<div class="project-meta">
	<ul class="single-project-categories">
		<?php
			$terms_as_text = get_the_term_list( $post->ID, 'project-category', '<li>', '</li><li>', '</li>' );
			echo $terms_as_text;
		?>
	</ul>
</div>