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
<div class="project_meta">

	<?php
		$terms_as_text = get_the_term_list( $post->ID, 'project-category', '', ', ', '' );
		echo '<div class="categories">' . __( 'Posted in: ', 'projects' ) . $terms_as_text . '</div>';
	?>

</div>