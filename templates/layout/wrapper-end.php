<?php
/**
 * Content wrappers
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

$template = get_option( 'template' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div></div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	default :
		echo '</div></div>';
		break;
}