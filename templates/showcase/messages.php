<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<?php foreach ( $messages as $message ) : ?>
	<div class="woothemes-projects-message"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>
