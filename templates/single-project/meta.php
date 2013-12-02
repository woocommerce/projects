<?php
/**
 * Single Project Meta
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
?>
<div class="project_meta">

	<?php
		$terms_as_text = get_the_term_list( $post->ID, 'project_cat', '', ', ', '' );
		echo '<div class="categories">' . $terms_as_text . '</div>';
	?>

</div>