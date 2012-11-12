<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'woothemes_get_portfolios' ) ) {
/**
 * Wrapper function to get the testimonials from the WooDojo_Testimonials class.
 * @param  string/array $args  Arguments.
 * @since  1.0.0
 * @return array/boolean       Array if true, boolean if false.
 */
function woothemes_get_portfolios ( $args = '' ) {
	global $woothemes_portfolios;
	return $woothemes_portfolios->get_portfolios( $args );
} // End woothemes_get_portfolios()
}

/**
 * Enable the usage of do_action( 'woothemes_portfolios' ) to display portfolios within a theme/plugin.
 *
 * @since  1.0.0
 */
add_action( 'woothemes_portfolios', 'woothemes_portfolios' );

if ( ! function_exists( 'woothemes_portfolios' ) ) {
/**
 * Display or return HTML-formatted testimonials.
 * @param  string/array $args  Arguments.
 * @since  1.0.0
 * @return string
 */
function woothemes_portfolios ( $args = '' ) {
	global $post;

	$defaults = array(
		'limit' => 5, 
		'orderby' => 'menu_order', 
		'order' => 'DESC', 
		'id' => 0, 
		'echo' => true, 
		'size' => 50, 
		'per_row' => 3, 
		'link_title' => true, 
		'title' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	// Allow child themes/plugins to filter here.
	$args = apply_filters( 'woothemes_portfolios_args', $args );
	$html = '';

	do_action( 'woothemes_portfolios_before', $args );
		
		// The Query.
		$query = woothemes_get_portfolios( $args );

		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			
			$html .= '<div class="widget widget_woothemes_portfolios">' . "\n";
			$html .= '<div class="portfolios">' . "\n";

			if ( '' != $args['title'] ) {
				$html .= '<h2>' . esc_html( $args['title'] ) . '</h2>' . "\n";
			}
			
			// Begin templating logic.
			
			$tpl = '<div class="%%CLASS%%">%%IMAGE%%<h3 class="portfolio-title">%%TITLE%%</h3><div class="portfolio-content">%%CONTENT%%</div></div>';
			$tpl = apply_filters( 'woothemes_portfolios_item_template', $tpl, $args );

			$i = 0;
			foreach ( $query as $post ) {
				$template = $tpl;
				$i++;

				setup_postdata( $post );
				
				$term_list = wp_get_post_terms($post->ID, 'portfolio_cat', array("fields" => "slugs"));

				$class = 'portfolio ';
				$class .= implode(" ", $term_list);
				
				if( ( 0 == $i % $args['per_row'] ) ) {
					$class .= ' last';
				} elseif ( 0 == ( $i - 1 ) % ( $args['per_row'] ) ) {
					$class .= ' first';
				}


				$title = get_the_title();
				if ( true == $args['link_title'] ) {
					$title = '<a href="' . esc_url( $post->url ) . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
				}

				// Optionally display the image, if it is available.
				if ( isset( $post->image ) && ( '' != $post->image ) ) {
					$template = str_replace( '%%IMAGE%%', $post->image, $template );
				} else {
					$template = str_replace( '%%IMAGE%%', '', $template );
				}

				$template = str_replace( '%%CLASS%%', $class, $template );
				$template = str_replace( '%%TITLE%%', $title, $template );
				$template = str_replace( '%%CONTENT%%', get_the_content(), $template );

				$html .= $template;

				if( ( 0 == $i % $args['per_row'] ) ) {
					$html .= '<div class="fix"></div>' . "\n";
				}
			}

			$html .= '</div><!--/.portfolios-->' . "\n";
			$html .= '</div><!--/.widget widget_woothemes_portfolios-->' . "\n";

			wp_reset_postdata();
		}
		
		// Allow child themes/plugins to filter here.
		$html = apply_filters( 'woothemes_portfolios_html', $html, $query, $args );
		
		if ( $args['echo'] != true ) { return $html; }
		
		// Should only run is "echo" is set to true.
		echo $html;
		
		do_action( 'woothemes_portfolios_after', $args ); // Only if "echo" is set to true.
} // End woothemes_portfolios()
}

if ( ! function_exists( 'woothemes_portfolios_shortcode' ) ) {
function woothemes_portfolios_shortcode () {
	woothemes_portfolios();
} // End woothemes_portfolios_shortcode()
}

add_shortcode( 'woothemes_portfolios', 'woothemes_portfolios_shortcode' );