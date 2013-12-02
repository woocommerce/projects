<?php
/**
 * The template for displaying project category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woothemes_projects/content-project_cat.php
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woothemes_projects_loop;

// Store loop count we're currently on
if ( empty( $woothemes_projects_loop['loop'] ) )
	$woothemes_projects_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woothemes_projects_loop['columns'] ) )
	$woothemes_projects_loop['columns'] = apply_filters( 'loop_showcase_columns', 2 );

// Increase loop count
$woothemes_projects_loop['loop']++;
?>
<li class="project-category project<?php
    if ( ( $woothemes_projects_loop['loop'] - 1 ) % $woothemes_projects_loop['columns'] == 0 || $woothemes_projects_loop['columns'] == 1)
        echo ' first';
	if ( $woothemes_projects_loop['loop'] % $woothemes_projects_loop['columns'] == 0 )
		echo ' last';
	?>">

	<?php do_action( 'woothemes_projects_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'project_cat' ); ?>">

		<?php
			/**
			 * woothemes_projects_before_subcategory_title hook
			 *
			 * @hooked woothemes_projects_subcategory_thumbnail - 10
			 */
			do_action( 'woothemes_projects_before_subcategory_title', $category );
		?>

		<h3>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'woothemes_projects_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			?>
		</h3>

		<?php
			/**
			 * woothemes_projects_after_subcategory_title hook
			 */
			do_action( 'woothemes_projects_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'woothemes_projects_after_subcategory', $category ); ?>

</li>