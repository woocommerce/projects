<?php
class Woothemes_Portfolio_Template_Loader {
	public $template_url;

	public function __construct() {
		$this->template_url = apply_filters( 'woothemes_portfolio_template_url', 'woothemes-portfolio/' );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. woothemes-portfolio looks for theme
	 * overrides in /theme/woothemes-portfolio/ by default
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		global $woothemes_portfolio;

		$find = array();
		$file = '';

		if ( is_single() && 'project' == get_post_type() ) {

			$file 	= 'single-project.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;

		} elseif ( is_tax( 'project-category' ) ) {

			$term = get_queried_object();

			$file 		= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= $this->template_url . $file;

		} elseif ( is_post_type_archive( 'project' ) || is_page( woothemes_portfolio_get_page_id( 'showcase' ) ) ) {

			$file 	= 'archive-project.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = $woothemes_portfolio->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}
} // End Class