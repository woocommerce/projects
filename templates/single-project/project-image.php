<?php
/**
 * Single Project Image
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woothemes_projects, $project;

?>
<div class="images">

	<?php
		if ( has_post_thumbnail() ) {

			$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_project_large_thumbnail_size', 'showcase_single' ) );
			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
			$attachment_count   = count( $project->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[project-gallery]';
			} else {
				$gallery = '';
			}

			echo apply_filters( 'woothemes_projects_single_project_image_html', sprintf( '<a href="%s" itemprop="image" class="woothemes_projects-main-image zoom" title="%s"  rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_title, $image ), $post->ID );

		} else {

			echo apply_filters( 'woothemes_projects_single_project_image_html', sprintf( '<img src="%s" alt="Placeholder" />', woothemes_projects_placeholder_img_src() ), $post->ID );

		}
	?>

	<?php do_action( 'woothemes_projects_project_thumbnails' ); ?>

</div>
