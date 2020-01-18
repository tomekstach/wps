<?php

if ( function_exists( 'rev_slider_shortcode' ) ) {
	echo rev_slider_shortcode( array( // xss ok
		'alias' => $settings->alias,
	) );
}
