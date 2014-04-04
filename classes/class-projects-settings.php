<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Settings Class
 *
 * All functionality pertaining to the projects settings.
 *
 * @package WordPress
 * @subpackage Projects
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Projects_Settings {
	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'projects_add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'projects_options_init' ) );

	} // End __construct()

	public function projects_add_settings_page() {
		add_submenu_page( 'edit.php?post_type=project', __( 'Settings', 'projects-by-woothemes' ), __( 'Settings', 'projects-by-woothemes' ), 'publish_posts', 'projects-settings-page', array( $this, 'projects_settings_page' ) );
	}

	public function projects_settings_page() {
		?>
		<div class="wrap">

			<h2><?php _e( 'Projects Settings', 'projects-by-woothemes' ); ?></h2>

			<form action="options.php" method="post">

				<?php settings_fields( 'projects_main_settings' ); ?>

				<?php do_settings_sections( 'projects' ); ?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>

			</form>

		</div>
		<?php
	}

	public function projects_options_init(){
		register_setting( 'projects_main_settings', 'projects', array( $this, 'projects_main_settings_validate' ) );
		add_settings_section( 'projects_page_settings_description', __( 'Pages', 'projects-by-woothemes' ), array( $this, 'projects_page_settings' ), 'projects' );
		add_settings_section( 'projects_image_settings_description', __( 'Images', 'projects-by-woothemes' ), array( $this, 'projects_images_settings' ), 'projects' );
	}

	public function projects_page_settings() {
		?>
		<p><?php _e( 'Configure projects pages.', 'projects-by-woothemes' ); ?></p>
		<?php
			$options = get_option( 'projects' );
			$args = array(
				'name'					=> 'projects[projects_page_id]',
				'selected'				=> absint( $options['projects_page_id'] ),
				'sort_column' 			=> 'menu_order',
	            'sort_order'			=> 'ASC',
	            'show_option_none' 		=> ' ',
				);
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Projects Page', 'projects-by-woothemes' ); ?></th>
				<td>
					<?php wp_dropdown_pages( $args ); ?>
					<p class="description">
						<?php _e( 'The base page of your projects. This is your projects archive.', 'projects-by-woothemes' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function projects_images_settings() {
		?>
		<p><?php _e ( 'These settings control the dimensions of thumbnails in your projects. After updating these settings you may need to' , 'projects-by-woothemes' ); ?> <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/"><?php _e( 'regenerate your thumbnails', 'projects-by-woothemes' ); ?></a>.</p>
		<?php

			$options = get_option( 'projects' );

			$defaults = apply_filters( 'projects_default_image_size', array(
				'project-archive' 	=> array(
											'width' 	=> 300,
											'height'	=> 300,
											'crop'		=> 'no'
										),
				'project-single' 	=> array(
											'width' 	=> 1024,
											'height'	=> 1024,
											'crop'		=> 'no'
										),
				'project-thumbnail' => array(
											'width' 	=> 100,
											'height'	=> 100,
											'crop'		=> 'yes'
										)
			) );

			// Parse incomming $options into an array and merge it with $defaults
			$options = wp_parse_args( $options, $defaults );

		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Archive Images', 'projects-by-woothemes' ); ?>
				</th>
				<td>
					<?php $crop = isset( $options['project-archive']['crop'] ) ? $options['project-archive']['crop'] : 'no'; ?>
					<?php _e( 'Width:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-archive][width]" value="<?php echo $options['project-archive']['width']; ?>" /> <?php _e( 'Height:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-archive][height]" value="<?php echo $options['project-archive']['height']; ?>" /> <?php _e( 'Crop:', 'projects-by-woothemes' ); ?> <input type="checkbox" name="projects[project-archive][crop]" value="1" <?php checked( $crop, 'yes' );?> /> 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Single Images', 'projects-by-woothemes' ); ?>
				</th>
				<td>
					<?php $crop = isset( $options['project-single']['crop'] ) ? $options['project-single']['crop'] : 'no'; ?>
					<?php _e( 'Width:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-single][width]" value="<?php echo $options['project-single']['width']; ?>" /> <?php _e( 'Height:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-single][height]" value="<?php echo $options['project-single']['height']; ?>" /> <?php _e( 'Crop:', 'projects-by-woothemes' ); ?> <input type="checkbox" name="projects[project-single][crop]" value="1" <?php checked( $crop, 'yes' );?> /> 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Thumbnails', 'projects-by-woothemes' ); ?>
				</th>
				<td>
					<?php $crop = isset( $options['project-thumbnail']['crop'] ) ? $options['project-thumbnail']['crop'] : 'no'; ?>
					<?php _e( 'Width:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-thumbnail][width]" value="<?php echo $options['project-thumbnail']['width']; ?>" /> <?php _e( 'Height:', 'projects-by-woothemes' ); ?> <input type="text" size="3" name="projects[project-thumbnail][height]" value="<?php echo $options['project-thumbnail']['height']; ?>" /> <?php _e( 'Crop:', 'projects-by-woothemes' ); ?> <input type="checkbox" name="projects[project-thumbnail][crop]" value="1" <?php checked( $crop, 'yes' );?> /> 
				</td>
			</tr>
		</table>
		<?php
	}

	public function projects_main_settings_validate( $input ) {

		$input['projects_page_id']				= absint( $input['projects_page_id'] );

		$input['project-archive']['width'] 		= absint( $input['project-archive']['width'] );
		$input['project-archive']['height'] 	= absint( $input['project-archive']['height'] );
		$input['project-archive']['crop'] 		= isset( $input['project-archive']['crop'] ) ? 'yes': 'no';

		$input['project-single']['width'] 		= absint( $input['project-single']['width'] );
		$input['project-single']['height'] 		= absint( $input['project-single']['height'] );
		$input['project-single']['crop'] 		= isset( $input['project-single']['crop'] ) ? 'yes': 'no';

		$input['project-thumbnail']['width'] 	= absint( $input['project-thumbnail']['width'] );
		$input['project-thumbnail']['height'] 	= absint( $input['project-thumbnail']['height'] );
		$input['project-thumbnail']['crop'] 	= isset( $input['project-thumbnail']['crop'] ) ? 'yes': 'no';

		return $input;
	}


} // End Class

new Projects_Settings();