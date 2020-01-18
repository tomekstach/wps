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
		</div>
	<?php endif; ?>
	<div class="post-content-outer">
		<?php
			include locate_template( 'templates/post/header.php' );
			include locate_template( 'templates/post/content.php' );
		?>
	</div>

	<div class="pp-content-grid-more-link clearfix">
		<a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>" target="_self" class="pp-content-grid-more pp-more-link-button" role="button">
			<?php esc_html_e( 'Read More', 'morz' ) ?>
		</a>
	</div>
</div>
