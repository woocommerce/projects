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

	<?php
		// Categories
		$terms_as_text 	= get_the_term_list( $post->ID, 'project-category', '<li>', '</li><li>', '</li>' );

		// Meta
		$client 		= esc_attr( get_post_meta( $post->ID, '_client', true ) );
		$url 			= esc_url( get_post_meta( $post->ID, '_url', true ) );

		do_action( 'projects_before_meta' );

		/**
		 * Display categories if they're set
		 */
		if ( $terms_as_text ) {
			echo '<div class="categories">';
			echo '<h3>' . __( 'Categories', 'projects-by-woothemes' ) . '</h3>';
			echo '<ul class="single-project-categories">';
			echo $terms_as_text;
			echo '</ul>';
			echo '</div>';
		}

		/**
		 * Display client if set
		 */
		if ( $client ) {
			echo '<div class="client">';
			echo '<h3>' . __( 'Client', 'projects-by-woothemes' ) . '</h3>';
			echo '<span class="client-name">' . $client . '</span>';
			echo '</div>';
		}

		/**
		 * Display link if set
		 */
		if ( $url ) {
			echo '<div class="url">';
			echo '<h3>' . __( 'Link', 'projects-by-woothemes' ) . '</h3>';
			echo '<span class="project-url"><a href="' . $url . '">' . apply_filters( 'projects_visit_project_link', __( 'Visit project', 'projects-by-woothemes' ) ) . '</a></span>';
			echo '</div>';
		}

		do_action( 'projects_after_meta' );
	?>
</div>