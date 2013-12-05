<?php
/**
 * Single Project Image
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $projects, $project;

?>
<div class="gallery">

	<?php
		if ( has_post_thumbnail() ) {

			$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_project_large_thumbnail_size', 'showcase_single' ) );
			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
			$attachment_count   = count( projects_get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[project-gallery]';
			} else {
				$gallery = '';
			}

			echo $image;

		} else {

			echo apply_filters( 'projects_single_project_image_html', sprintf( '<img src="%s" alt="Placeholder" />', projects_placeholder_img_src() ), $post->ID );

		}

		$attachment_ids = projects_get_gallery_attachment_ids();

		if ( $attachment_ids ) { ?>

			<?php

				$loop = 0;
				$columns = apply_filters( 'projects_project_thumbnails_columns', 3 );

				foreach ( $attachment_ids as $attachment_id ) {

					$classes = array( 'zoom' );

					if ( $loop == 0 || $loop % $columns == 0 )
						$classes[] = 'first';

					if ( ( $loop + 1 ) % $columns == 0 )
						$classes[] = 'last';

					$image_link = wp_get_attachment_url( $attachment_id );

					if ( ! $image_link )
						continue;

					$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_project_small_thumbnail_size', 'project-single' ) );
					$image_class = esc_attr( implode( ' ', $classes ) );
					$image_title = esc_attr( get_the_title( $attachment_id ) );

					echo $image;

					$loop++;

				} // endforeach ?>

		<?php } // endif ?>

</div>
