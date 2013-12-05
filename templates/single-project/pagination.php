<?php
/**
 * Single project pagination
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<nav class="projects-single-pagination">
	<div class="next">
		<?php next_post_link( '%link' ); ?>
	</div>
	<div class="previous">
		<?php previous_post_link( '%link' ); ?>
	</div>
</nav>
