<?php

/**
 * The common code for the single and looped post template
 *
 * @package vamtam/morz
 */

	global $post, $wp_query;

	if ( ! isset( $blog_query ) ) {
		$blog_query = $wp_query;
	}

	extract( VamtamPostFormats::post_layout_info() );
	$format = get_post_format();
	$format = empty( $format ) ? 'standard' : $format;

	$post_data = array_merge( array(
		'p'       => $post,
		'format'  => $format,
	), VamtamPostFormats::post_layout_info() );

	if ( has_post_format( 'quote' ) && ! $blog_query->is_single( $post ) && ($news || ! $show_content) ) {
		$post_data['content'] = '';
	} else {
		if ( $blog_query->is_single( $post ) ) {
			$post_data['content'] = get_the_content();
		} elseif ( $show_content && ! $news ) {
			$post_data['content'] = get_the_content( esc_html__( 'Read more', 'morz' ), false );
		} else {
			$post_data['content'] = get_the_excerpt();
		}
	}

	$post_data = VamtamPostFormats::process( $post_data );

	if ( $post_data['format'] !== 'quote' ) {
		if ( $blog_query->is_single( $post ) || ( $show_content && ! $news ) ) {
			if ( $post_data['format'] === 'gallery' ) {
				VamtamPostFormats::block_gallery_beaver();
			}

			$post_data['content'] = apply_filters( 'the_content', $post_data['content'] );
			VamtamPostFormats::enable_gallery_beaver();
		}
	}

	$has_media = isset( $post_data['media'] ) ? 'has-image' : 'no-image';

	$article_class = array( 'post-article', $has_media . '-wrapper' );

	if ( $blog_query->is_single( $post ) ) {
		$article_class[] = 'single';
	}

	$inner_class   = array( $format . '-post-format', 'clearfix' );
	$inner_class[] = isset( $post_data['act_as_image'] ) ? 'as-image' : 'as-normal';
	$inner_class[] = isset( $post_data['act_as_standard'] ) ? 'as-standard-post-format' : ''
?>
<div class="<?php echo esc_attr( implode( ' ', $article_class ) ); ?>" itemscope itemtype="<?php class_exists( 'VamtamBlogModule' ) && VamtamBlogModule::schema_itemtype(); ?>" itemid="<?php the_permalink() ?>">
	<?php class_exists( 'VamtamBlogModule' ) && VamtamBlogModule::schema_meta(); ?>
	<div class="<?php echo esc_attr( implode( ' ', $inner_class ) ); ?>">
		<?php
			if ( $blog_query->is_single( $post ) ) {
				include locate_template( 'templates/post/main/single.php' );
			} elseif ( $news ) {
				include locate_template( 'templates/post/main/news.php' );
			} else {
				include locate_template( 'templates/post/main/loop.php' );
			}
		?>
	</div>
</div>

