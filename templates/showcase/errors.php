<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $errors ) return;
?>
<ul class="projects-error">
	<?php foreach ( $errors as $error ) : ?>
		<li><?php echo wp_kses_post( $error ); ?></li>
	<?php endforeach; ?>
</ul>