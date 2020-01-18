<?php
/**
 * Post content template
 *
 * @package vamtam/morz
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $multipage;

if ( empty( $post_data['content'] ) && isset( $post_data['media'] ) && ! $multipage ) return;

?>
<div class="post-content the-content the-content-parent">
	<?php
		do_action( 'vamtam_before_post_content' );

		if ( ! empty( $post_data['content'] ) ) {
			echo $post_data['content']; // xss ok
		}

		do_action( 'vamtam_after_post_content' );

		wp_link_pages( array(
			'before' => '<nav class="navigation post-pagination" role="navigation"><span class="screen-reader-text">' . esc_html__( 'Pages:', 'morz' ) . '</span>',
			'after'  => '</nav>',
		) );
	?>
</div>

