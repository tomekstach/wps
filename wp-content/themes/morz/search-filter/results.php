<?php
/**
 * Search & Filter Pro
 *
 * Sample Results Template
 *
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 *
 * Note: these templates are not full page templates, rather
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think
 * of it as a template part
 *
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs
 * and using template tags -
 *
 * http://codex.wordpress.org/Template_Tags
 *
 */

global $post, $wp_query;

if ( $query->have_posts() )
{
	if ( ! isset( $blog_query ) ) {
		$blog_query = $wp_query;
	}

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
	<div class="row page-wrapper">
		<article id="post-<?php the_ID(); ?>" <?php post_class( VamtamTemplates::get_layout() ); ?>>
			<div class="page-content clearfix">
				<div class="pp-dealers-wrapper">
	<?php
	while ($query->have_posts())
	{
		$query->the_post();
		$post_class   = array();
		$post_class[] = 'pp-content-post pp-content-grid-post pp-grid-style-3';

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
				VamtamPostFormats::block_gallery_beaver();
				$post_data['content'] = do_shortcode(apply_filters( 'the_content', get_the_content() ));
				VamtamPostFormats::enable_gallery_beaver();
			} elseif ( $show_content && ! $news ) {
				VamtamPostFormats::block_gallery_beaver();
				$post_data['content'] = do_shortcode(apply_filters( 'the_content', get_the_content( esc_html__( 'Read more', 'morz' ), false ) ));
				VamtamPostFormats::enable_gallery_beaver();
			} else {
				$post_data['content'] = do_shortcode(get_the_excerpt());
			}
		}

		$post_data = VamtamPostFormats::process( $post_data );

		$has_media = isset( $post_data['media'] ) ? 'has-image' : 'no-image';

		$article_class = array( 'post-article', $has_media . '-wrapper' );

		if ( $blog_query->is_single( $post ) ) {
			$article_class[] = 'single';
		}

		$inner_class	= array( $format . '-post-format', 'clearfix' );
		$inner_class[]	= isset( $post_data['act_as_image'] ) ? 'as-image' : 'as-normal';
		$inner_class[]	= isset( $post_data['act_as_standard'] ) ? 'as-standard-post-format' : '';
		$post_tags		= get_the_tags();
		$class			= get_post_class();
		?>
			<div <?php post_class( implode( ' ', $post_class ) ) ?>>
				<div>
					<div class="<?php echo esc_attr( implode( ' ', $article_class ) ); ?>" itemscope itemtype="<?php class_exists( 'VamtamBlogModule' ) && VamtamBlogModule::schema_itemtype(); ?>" itemid="<?php the_permalink() ?>">
						<div class="<?php echo esc_attr( implode( ' ', $inner_class ) ); ?>">
							<div class="post-row">
								<?php if ( $show_media && isset( $post_data['media'] ) ) : ?>
									<div class="post-media">
										<div class='media-inner'>
											<?php if ( has_post_format( 'image' ) || ( isset( $post_data['act_as_image'] ) && $post_data['act_as_image'] ) ) :  ?>
												<a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>">
													<?php echo $post_data['media']; // xss ok ?>
												</a>
											<?php else : ?>
												<?php echo $post_data['media']; // xss ok ?>
											<?php endif ?>
										</div>
										<?php if (is_object($post_tags[0])) :?>
											<div class="post-tag"><?php echo $post_tags[0]->name;?></div>
										<?php endif ?>
									</div>
								<?php endif; ?>
								<div class="post-content-outer">
									<?php if (is_object($post_tags[0]) and (in_array('category-menedzer-radzi', $class) or in_array('category-faq', $class))) :?>
									<div class="tags">
										<?php foreach ($post_tags as $tag) :?>
										<div class="post-tag"><?php echo $post_tags[0]->name;?></div>
										<?php endforeach;?>
									</div>
									<?php endif; ?>
									<?php
										include locate_template( 'templates/post/header.php' );
										include locate_template( 'templates/post/content.php' );
									?>
								</div>

								<div class="pp-content-grid-more-link clearfix">
									<a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>" target="_self" class="pp-content-grid-more pp-more-link-button" role="button">
										<span><?php esc_html_e( 'Read More', 'morz' ) ?></span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	?>
</div>

			<div class="pagination">
				<?php
					/* example code for using the wp_pagenavi plugin */
					if (function_exists('wp_pagenavi'))
					{
						echo "<br />";
						wp_pagenavi( array( 'query' => $query ) );
					}
				?>
			</div>
	<?php
}
else
{
	echo "Brak wynikow wyszukiwania.";
}
?>
		</div>
	</article>
</div>
