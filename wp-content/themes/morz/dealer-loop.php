<?php

/**
 * Catch-all post loop
 */

// display full post/image or thumbs
if ( ! isset( $called_from_shortcode ) ) {
	$settings = new stdClass;

	$settings->show_title   = true;
	$settings->show_media   = true;
	$settings->pagination   = true;
	$settings->layout       = class_exists( 'Vamtam_Elements_B' ) ? vamtam_get_option( 'archive-layout' ) : 'normal';
	$settings->show_content = $settings->layout !== 'normal';
	$settings->columns      = $settings->layout === 'normal' ? 1 : 2;
	$settings->gap          = true;

	$news        = 'mosaic' === $settings->layout;
	$max_columns = 0;
}

if ( defined( 'VAMTAM_ARCHIVE_TEMPLATE' ) && ! $news ) {
	$settings->show_content = false;
}

global $vamtam_loop_vars;
$old_vamtam_loop_vars = $vamtam_loop_vars;

$vamtam_loop_vars = array(
	'show_content' => $settings->show_content,
	'show_title'   => $settings->show_title,
	'show_media'   => $settings->show_media,
	'news'         => $news,
	'columns'      => $settings->columns,
	'layout'       => $settings->layout,
);

$is_cube = in_array( $settings->layout, array( 'mosaic', 'small' ), true );

$wrapper_class = array();

$wrapper_class[] = $news ? 'news' : 'regular';
$wrapper_class[] = $settings->layout;

if ( $news && ! $is_cube ) {
	$wrapper_class[] = 'row';
}

$cube_options         = array();
$data_options_escaped = '';

if ( $is_cube ) {
	$cube_options = array(
		'layoutMode'        => $settings->layout,
		'sortToPreventGaps' => true,
		'defaultFilter'     => '*',
		'animationType'     => 'quicksand',
		'gapHorizontal'     => $settings->gap ? 30 : 0,
		'gapVertical'       => $settings->gap ? 30 : 0,
		'gridAdjustment'    => 'responsive',
		'mediaQueries'      => VamtamTemplates::scrollable_columns( $max_columns ),
		'displayType'       => 'bottomToTop',
		'displayTypeSpeed'  => 100,
	);

	$wrapper_class[] = 'vamtam-cubeportfolio cbp';

	$data_options_escaped = 'data-options="' . esc_attr( json_encode( $cube_options ) ) . '"';

	wp_enqueue_style( 'cubeportfolio' );

	if ( VamtamTemplates::early_cube_load() ) {
		wp_enqueue_script( 'cubeportfolio' );
	}

	$GLOBALS['vamtam_inside_cube'] = true;

	// print late styles, otherwise Beaver will skip over some of them
	if ( ! doing_filter( 'get_the_excerpt' ) ) {
		print_late_styles();
	}
}

?>
<div class="pp-dealers-wrapper">
<?php

	$i = 0;

	if ( ! isset( $blog_query ) ) {
		$blog_query = $GLOBALS['wp_query'];
	} else {
		// ideally, this shouldn't be necessary, but for some reason Beaver Builder
		// uses the post ID from the global $wp_query instead of the global $post
		$GLOBALS['wp_query'] = $blog_query;
	}

	if ( $blog_query->have_posts() ) :
		while ( $blog_query->have_posts() ) : $blog_query->the_post();
			$post_class   = array();
			$post_class[] = 'pp-content-post pp-content-grid-post pp-grid-style-3';

			if ( $settings->columns === 1 && ! $news ) {
				$post_class[] = 'clearfix';
			}

			if ( $news && 0 === $i % $settings->columns ) {
				$post_class[] = 'clearboth';
			}
?>
			<div <?php post_class( implode( ' ', $post_class ) ) ?>>
				<div>
					<?php include locate_template( 'templates/dealer.php' );	?>
				</div>
			</div>
<?php
			$i++;
		endwhile;
	endif;

	wp_reset_query();

?>
</div>

<?php

if ( vamtam_sanitize_bool( $settings->pagination ) ) {
	$pagination_type = vamtam_get_option( 'pagination-type' );

	if ( 'mosaic' !== $settings->layout || defined( 'VAMTAM_ARCHIVE_TEMPLATE' ) ) {
		$pagination_type = 'paged';
	}

	VamtamTemplates::pagination( $pagination_type, true, $vamtam_loop_vars, $blog_query );
}

if ( $settings->layout === 'mosaic' || $settings->layout === 'grid' ) {
	$GLOBALS['vamtam_inside_cube'] = false;
}

$vamtam_loop_vars = $old_vamtam_loop_vars;
