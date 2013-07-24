<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Portfolio Frontend Class
 *
 * All functionality pertaining to the portfolio frontend.
 *
 * @package WordPress
 * @subpackage Woothemes_Portfolio_Frontend
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */

class Woothemes_Portfolio_Frontend {
	/**
	 * Instance of Woothemes_Portfolio_Template_Loader.
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
		require_once( 'class-woothemes-portfolio-template-loader.php' );
		$this->template_loader = new Woothemes_Portfolio_Template_Loader();
		add_filter( 'template_include', array( $this->template_loader, 'template_loader' ) );
	} // End __construct()
} // End Class
?>