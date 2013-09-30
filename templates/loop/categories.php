<?php
/**
 * Project Categories
 *
 * @author 		WooThemes
 * @package 	Woothemes_Projects/Templates
 * @version     1.0.0
 */
$term_args 		= array(
					'taxonomy' => 'project-category'
				);
$terms 			= get_terms( 'project-category', $term_args );
$term_list 		= '';
$count 			= count( $terms );
$i 				= 0;
$display_count 	= '';

if ( $count > 0 ) {
    ?>
    	<nav>
    		<ul class="project-categories">
    			<?php foreach ( $terms as $term ) :
    				if ( apply_filters( 'woothemes_projects_category_display_count', true ) )
						$display_count 	= '<span class="count"> ' . $term->count . '</span>';
    			?>
	    			<li class="project-category-link">
	    				<a href="<?php echo get_term_link( $term ); ?>" title="<?php sprintf( __( 'View all projects in %s', 'woothemes-projects' ), $term->name ); ?>"> <?php echo $term->name; ?></a><?php echo $display_count; ?>
	    			</li>
    			<?php endforeach; ?>
    		</ul>
    	</nav>
    <?php
}