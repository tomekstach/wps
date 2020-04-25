<?php


include locate_template( 'templates/post/header.php' );

$meta_author   = vamtam_get_optionb( 'post-meta', 'author' );
$meta_date     = vamtam_get_optionb( 'post-meta', 'date' );
$meta_comments = vamtam_get_optionb( 'post-meta', 'comments' ) && comments_open();
$meta_tax      = vamtam_get_optionb( 'post-meta', 'tax' );

?><div class="post-content-outer single-post">

	<?php if ( $meta_author || $meta_date || $meta_comments || $meta_tax || is_customize_preview() ) : ?>
		<div class="limit-wrapper">
			<div class="meta-top">
				<div class="meta-left">
					<?php if ( $meta_author || $meta_date || $meta_comments || is_customize_preview() ) : ?>
						<div class="with-separator">
							<?php if ( $meta_author || is_customize_preview() ) : ?>
								<span class="author vamtam-meta-author" <?php VamtamTemplates::display_none( $meta_author ) ?>><?php echo esc_html( _x( 'Author:', 'As in: "by Author Name"', 'morz' ) ) ?><?php the_author_posts_link()?></span>
							<?php endif ?>

							<?php if ( $meta_date || is_customize_preview() ) : ?>
								<span class="post-date vamtam-meta-date" itemprop="datePublished" <?php VamtamTemplates::display_none( $meta_date ) ?>><?php the_time( get_option( 'date_format' ) ); ?> </span>
							<?php endif ?>

							<?php get_template_part( 'templates/post/meta/comments' ); ?>
						</div>
					<?php endif ?>

					<?php if ( $meta_tax || is_customize_preview() ): ?>
						<div>
							<?php get_template_part( 'templates/post/meta/tags' ); ?>
							<?php get_template_part( 'templates/post/meta/categories' ); ?>
						</div>
					<?php endif ?>
				</div>

				<?php if ( function_exists( 'sharing_display' ) ) : ?>
					<div class="meta-right">
						<?php get_template_part( 'templates/share' ); ?>
					</div>
				<?php endif ?>
			</div>
		</div>
	<?php endif ?>

	<?php if ( isset( $post_data['media'] ) && ( vamtam_get_optionb( 'show-single-post-image' ) || is_customize_preview() ) ) : ?>
		<div class="post-media post-media-image" <?php VamtamTemplates::display_none( vamtam_get_optionb( 'show-single-post-image' ) ) ?>>
			<div class='media-inner'>
				<?php echo $post_data['media']; // xss ok ?>
			</div>
		</div>
	<?php endif; ?>

	<?php include locate_template( 'templates/post/content.php' ); ?>
</div>

