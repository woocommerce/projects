<?php
/**
 * The template for displaying project category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/projects/content-project_cat.php
 *
 * @author 		WooThemes
 * @package 	Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $projects_loop;

// Store loop count we're currently on
if ( empty( $projects_loop['loop'] ) )
	$projects_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $projects_loop['columns'] ) )
	$projects_loop['columns'] = apply_filters( 'projects_loop_columns', 2 );

// Increase loop count
$projects_loop['loop']++;
?>
<li class="project-category project<?php
    if ( ( $projects_loop['loop'] - 1 ) % $projects_loop['columns'] == 0 || $projects_loop['columns'] == 1)
        echo ' first';
	if ( $projects_loop['loop'] % $projects_loop['columns'] == 0 )
		echo ' last';
	?>">

	<?php do_action( 'projects_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'project_cat' ); ?>">

		<?php
			/**
			 * projects_before_subcategory_title hook
			 *
			 * @hooked projects_subcategory_thumbnail - 10
			 */
			do_action( 'projects_before_subcategory_title', $category );
		?>

		<h3>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'projects_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			?>
		</h3>

		<?php
			/**
			 * projects_after_subcategory_title hook
			 */
			do_action( 'projects_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'projects_after_subcategory', $category ); ?>

</li>