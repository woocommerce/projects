<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Frontend Class
 *
 * All functionality pertaining to the projects frontend.
 *
 * @package WordPress
 * @subpackage Projects_Frontend
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */

class Projects_Frontend {
	/**
	 * Instance of Projects_Template_Loader.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $template_loader;

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
		require_once( 'class-projects-template-loader.php' );
		$this->template_loader = new Projects_Template_Loader();
		add_filter( 'template_include', array( $this->template_loader, 'template_loader' ) );
	} // End __construct()
} // End Class
?>