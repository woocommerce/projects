<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Projects Settings Class
 *
 * All functionality pertaining to the projects settings.
 *
 * @package WordPress
 * @subpackage Woothemes_Projects
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Woothemes_Projects_Settings {
	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'woothemes_projects_add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'woothemes_projects_options_init' ) );

	} // End __construct()

	public function woothemes_projects_add_settings_page() {
		add_submenu_page( 'edit.php?post_type=project', 'Settings', 'Settings', 'publish_posts', 'woothemes-projects-settings-page', array( $this, 'woothemes_projects_settings_page' ) );
	}

	public function woothemes_projects_settings_page() {
		?>
		<div class="wrap">

			<h2><?php _e( 'Projects Settings', 'woothemes-projects' ); ?></h2>

			<form action="options.php" method="post">

				<?php settings_fields( 'woothemes_projects_main_settings' ); ?>

				<?php do_settings_sections( 'woothemes-projects' ); ?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>

			</form>

		</div>
		<?php
	}

	public function woothemes_projects_options_init(){
		register_setting( 'woothemes_projects_main_settings', 'woothemes_projects', array( $this, 'woothemes_projects_main_settings_validate' ) );
		add_settings_section( 'woothemes_projects_page_settings_description', 'Pages', array( $this, 'woothemes_projects_page_settings' ), 'woothemes-projects' );
		add_settings_section( 'woothemes_projects_image_settings_description', 'Images', array( $this, 'woothemes_projects_images_settings' ), 'woothemes-projects' );
	}

	public function woothemes_projects_page_settings() {
		?>
		<p><?php _e( 'Setup core projects pages.', 'woothemes-projects' ); ?></p>
		<?php
			$options = get_option( 'woothemes_projects' );
			$args = array(
				'name'					=> 'woothemes_projects[woothemes_projects_showcase_page_id]',
				'selected'				=> absint( $options['woothemes_projects_showcase_page_id'] ),
				'sort_column' 			=> 'menu_order',
	            'sort_order'			=> 'ASC',
	            'show_option_none' 		=> ' ',
				);
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Projects Page', 'woothemes-projects' ); ?></th>
				<td>
					<?php wp_dropdown_pages( $args ); ?>
					<p class="description">
						<?php _e( 'This sets the base page of your projects. This is where your projects archive will be.', 'woothemes-projects' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function woothemes_projects_images_settings() {
		?>
		<p><?php _e ( 'These settings affect the actual dimensions of images in your catalog – the display on the front-end will still be affected by CSS styles. After changing these settings you may need to' , 'woothemes-projects' ); ?> <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/"><?php _e( 'regenerate your thumbnails', 'woothemes-projects' ); ?></a>.</p>
		<?php
			$options = get_option( 'woothemes_projects' );

			$defaults 	= array(
			    'archive_image_width' 	=> 300,
			    'archive_image_height' 	=> 300,
			    'single_image_width' 	=> 1024,
			    'single_image_height' 	=> 1024,
			    'thumb_width' 			=> 100,
			    'thumb_height' 			=> 100,
			);

			// Parse incomming $options into an array and merge it with $defaults
			// @todo: make this work
			$newoptions = wp_parse_args( $options, $defaults );

		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Archive Images', 'woothemes-projects' ); ?>
				</th>
				<td>
					<?php _e( 'Width:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[archive_image_width]" value="<?php echo $options['archive_image_width']; ?>" /> <?php _e( 'Height:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[archive_image_height]" value="<?php echo $options['archive_image_height']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Single Images', 'woothemes-projects' ); ?>
				</th>
				<td>
					<?php _e( 'Width:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[single_image_width]" value="<?php echo $options['single_image_width']; ?>" /> <?php _e( 'Height:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[single_image_height]" value="<?php echo $options['single_image_width']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Thumbnails', 'woothemes-projects' ); ?>
				</th>
				<td>
					<?php _e( 'Width:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[thumb_width]" value="<?php echo $options['thumb_width']; ?>" /> <?php _e( 'Height:', 'woothemes-projects' ); ?> <input type="text" size="3" name="woothemes_projects[thumb_height]" value="<?php echo $options['thumb_height']; ?>" />
				</td>
			</tr>
		</table>
		<?php
	}

	public function woothemes_projects_main_settings_validate( $input ) {

		$input['woothemes_projects_showcase_page_id'] 		= absint( $input['woothemes_projects_showcase_page_id'] );

		$input['archive_image_width'] 	= absint( $input['archive_image_width'] );
		$input['archive_image_height'] 	= absint( $input['archive_image_height'] );

		$input['single_image_width'] 	= absint( $input['single_image_width'] );
		$input['single_image_height'] 	= absint( $input['single_image_height'] );

		$input['thumb_width'] 			= absint( $input['thumb_width'] );
		$input['thumb_height'] 			= absint( $input['thumb_height'] );

		return $input;
	}


} // End Class

new Woothemes_Projects_Settings();