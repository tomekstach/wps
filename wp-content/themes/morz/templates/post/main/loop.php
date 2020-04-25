<div class="post-row">
	<?php if ( $show_media && isset( $post_data['media'] ) ) : ?>
		<div class="post-media">
			<div class='media-inner'>
				<?php $has_link = has_post_format( 'image' ) || ( isset( $post_data['act_as_image'] ) && $post_data['act_as_image'] ) ?>
				<?php if ( $has_link ) :  ?>
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>">
				<?php endif ?>
					<?php echo $post_data['media']; // xss ok ?>
				<?php if ( $has_link ) :  ?>
					</a>
				<?php endif ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="post-content-outer">
		<?php
			include locate_template( 'templates/post/header-large.php' );
			include locate_template( 'templates/post/main/actions.php' );
			include locate_template( 'templates/post/content.php' );
			include locate_template( 'templates/post/meta-loop.php' );
		?>
	</div>
</div>
