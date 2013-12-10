<?php
class Projects_Template_Loader {
	public $template_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->template_url = apply_filters( 'projects_template_url', 'projects/' );
	} // End __construct()

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. projects looks for theme
	 * overrides in /theme/projects/ by default
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader ( $template ) {
		global $projects, $post;

		$find = array( 'projects.php' );
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

		} elseif ( is_post_type_archive( 'project' ) || is_page( projects_get_page_id( 'projects' ) ) ) {

			$file 	= 'archive-project.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = $projects->plugin_path() . '/templates/' . $file;
		}

		return $template;
	} // End template_loader()
} // End Class