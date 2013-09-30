<?php
/**
 * Project Categories
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

$term_args 	= array(
				'taxonomy' => 'project-category'
			);
$terms 		= get_terms( 'project-category', $term_args );
$term_list 	= '';
$count 		= count( $terms );
$i 			= 0;

if ( $count > 0 ) {
    foreach ( $terms as $term ) {
        $i++;

        if ( apply_filters( 'woothemes_projects_category_display_count', true ) ) {
			$display_count = '<span class="count"> ' . $term->count . '</span>';
		} else {
			$display_count = '';
		}

    	$term_list .= '<li class="project-category-link"><a href="' . get_term_link( $term ) . '" title="' . sprintf( __( 'View all projects in %s', 'woothemes-projects' ), $term->name ) . '">' . $term->name . '</a>' . $display_count . '</li>';
    }
    echo '<nav><ul class="project-categories">' . $term_list . '</ul></nav>';
}