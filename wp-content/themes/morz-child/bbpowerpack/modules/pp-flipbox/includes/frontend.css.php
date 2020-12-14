.fl-node-<?php echo $id; ?> .pp-flipbox {
	<?php if( $settings->box_border_style != 'none' ) { ?>
	border-style: <?php echo $settings->box_border_style; ?>;
	border-width: <?php echo $settings->box_border_width; ?>px;
	<?php } ?>
	<?php if( $settings->top_padding ) { ?>padding: <?php echo $settings->top_padding; ?>px <?php echo $settings->side_padding; ?>px;<?php } ?>
	<?php if ( $settings->box_height == 'custom' ) { ?>
		height: <?php echo $settings->box_height_custom; ?>px;
	<?php } ?>
}

/* Front */
.fl-node-<?php echo $id; ?> .pp-flipbox-front {
	float: left;
	<?php if( $settings->front_background ) { ?>background: <?php echo pp_get_color_value($settings->front_background); ?>;<?php } ?>
	<?php if( $settings->front_border_color ) { ?>border-color: #<?php echo $settings->front_border_color; ?>;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-flipbox-front .pp-flipbox-title .pp-flipbox-front-title {
	<?php if( $settings->front_title_color ) { ?>color: #<?php echo $settings->front_title_color; ?>;<?php } ?>
	<?php if( $settings->front_title_font['family']	!= 'Default' ) { ?><?php FLBuilderFonts::font_css( $settings->front_title_font ); ?><?php } ?>
	<?php if( $settings->front_title_font_size ) { ?>font-size: <?php echo $settings->front_title_font_size; ?>px;<?php } ?>
	margin-top: <?php echo $settings->front_title_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->front_title_margin['bottom']; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-flipbox-front .pp-flipbox-description {
	<?php if( $settings->front_text_color ) { ?>color: #<?php echo $settings->front_text_color; ?>;<?php } ?>
	<?php if( $settings->front_text_font['family']	!= 'Default' ) { ?><?php FLBuilderFonts::font_css( $settings->front_text_font ); ?><?php } ?>
	<?php if( $settings->front_text_font_size ) { ?>font-size: <?php echo $settings->front_text_font_size; ?>px;<?php } ?>
	margin-top: <?php echo $settings->front_text_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->front_text_margin['bottom']; ?>px;
}

/* Back */
.fl-node-<?php echo $id; ?> .pp-flipbox-back {
	<?php if( $settings->back_background ) { ?>background: <?php echo pp_get_color_value($settings->back_background); ?>;<?php } ?>
	<?php if( $settings->back_border_color ) { ?>border-color: #<?php echo $settings->back_border_color; ?>;<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-flipbox-back .pp-flipbox-title .pp-flipbox-back-title {
	<?php if( $settings->back_title_color ) { ?>color: #<?php echo $settings->back_title_color; ?>;<?php } ?>
	<?php if( $settings->back_title_font['family']	!= 'Default' ) { ?><?php FLBuilderFonts::font_css( $settings->back_title_font ); ?><?php } ?>
	<?php if( $settings->back_title_font_size ) { ?>font-size: <?php echo $settings->back_title_font_size; ?>px;<?php } ?>
	margin-top: <?php echo $settings->back_title_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->back_title_margin['bottom']; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-flipbox-back .pp-flipbox-description {
	<?php if( $settings->back_text_color ) { ?>color: #<?php echo $settings->back_text_color; ?>;<?php } ?>
	<?php if( $settings->back_text_font['family']	!= 'Default' ) { ?><?php FLBuilderFonts::font_css( $settings->back_text_font ); ?><?php } ?>
	<?php if( $settings->back_text_font_size ) { ?>font-size: <?php echo $settings->back_text_font_size; ?>px;<?php } ?>
	margin-top: <?php echo $settings->back_text_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->back_text_margin['bottom']; ?>px;
}


<?php if( $settings->icon_type == 'icon' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon {
		<?php if( $settings->show_border == 'yes' ) { ?>
			<?php if( $settings->icon_border_color ) { ?>border-color: #<?php echo $settings->icon_border_color; ?>;<?php } ?>
			<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
			<?php if( $settings->icon_border_width ) { ?>border-width: <?php echo $settings->icon_border_width; ?>px;<?php } ?>
		<?php } ?>
		<?php if( $settings->icon_box_size ) { ?>padding: <?php echo $settings->icon_box_size; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon-inner {
		<?php if( $settings->icon_background ) { ?>background: #<?php echo $settings->icon_background; ?>;<?php } ?>
		<?php if( ( $settings->show_border == 'yes' && $settings->icon_border_radius ) || ( $settings->icon_background && $settings->icon_border_radius ) ) { ?>
			<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
		<?php } ?>
		<?php if( $settings->icon_color ) { ?>color: #<?php echo $settings->icon_color; ?>;<?php } ?>
		<?php if( $settings->icon_font_size ) { ?>font-size: <?php echo $settings->icon_font_size; ?>px;<?php } ?>
		<?php if( $settings->icon_width ) { ?>height: <?php echo $settings->icon_width; ?>px;<?php } ?>
		<?php if( $settings->icon_width ) { ?>width: <?php echo $settings->icon_width; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon-inner span.pp-icon,
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon-inner span.pp-icon:before {
		<?php if( $settings->icon_font_size ) { ?>font-size: <?php echo $settings->icon_font_size; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon:hover {
		<?php if( $settings->show_border == 'yes' ) { ?>
		<?php if( $settings->icon_border_color_hover ) { ?>	border-color: #<?php echo $settings->icon_border_color_hover; ?>;<?php } ?>
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox-icon-inner:hover {
		<?php if( $settings->icon_background_hover ) { ?>background: #<?php echo $settings->icon_background_hover; ?>;<?php } ?>
		<?php if( $settings->icon_color_hover ) { ?>color: #<?php echo $settings->icon_color_hover; ?>;<?php } ?>
	}
<?php } ?>
<?php if( $settings->icon_type == 'image' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-flipbox-image img {
		<?php if( $settings->show_border == 'yes' ) { ?>
			<?php if( $settings->icon_border_color ) { ?>border-color: #<?php echo $settings->icon_border_color; ?>;<?php } ?>
			<?php if( $settings->icon_border_width ) { ?>border-width: <?php echo $settings->icon_border_width; ?>px;<?php } ?>
		<?php } ?>
		<?php if( $settings->icon_border_radius ) { ?>border-radius: <?php echo $settings->icon_border_radius; ?>px;<?php } ?>
		<?php if( $settings->image_width ) { ?>height: <?php echo $settings->image_width; ?>px;<?php } ?>
		<?php if( $settings->icon_box_size ) { ?>padding: <?php echo $settings->icon_box_size; ?>px;<?php } ?>
		<?php if( $settings->image_width ) { ?>width: <?php echo $settings->image_width; ?>px;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox-image img:hover {
		<?php if( $settings->show_border == 'yes' ) { ?>
			<?php if( $settings->icon_border_color_hover ) { ?>border-color: #<?php echo $settings->icon_border_color_hover; ?>;<?php } ?>
		<?php } ?>
	}
<?php } ?>

<?php if( $settings->link_type == 'custom' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-flipbox .pp-more-link {
		<?php if( $settings->link_background ) { ?>background: #<?php echo $settings->link_background; ?>;<?php } ?>
		<?php if( $settings->link_color ) { ?>color: #<?php echo $settings->link_color; ?>;<?php } ?>
		<?php if( $settings->link_font['family']	!= 'Default' ) { ?><?php FLBuilderFonts::font_css( $settings->link_font ); ?><?php } ?>
		<?php if( $settings->link_font_size ) { ?>font-size: <?php echo $settings->link_font_size; ?>px;<?php } ?>
		<?php if( $settings->link_padding['top'] >= 0 ) { ?>
		padding-top: <?php echo $settings->link_padding['top']; ?>px;
		<?php } ?>
		<?php if( $settings->link_padding['right'] >= 0 ) { ?>
		padding-right: <?php echo $settings->link_padding['right']; ?>px;
		<?php } ?>
		<?php if( $settings->link_padding['bottom'] >= 0 ) { ?>
		padding-bottom: <?php echo $settings->link_padding['bottom']; ?>px;
		<?php } ?>
		<?php if( $settings->link_padding['left'] >= 0 ) { ?>
		padding-left: <?php echo $settings->link_padding['left']; ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-flipbox .pp-more-link:hover {
		<?php if( $settings->link_background_hover ) { ?>background: #<?php echo $settings->link_background_hover; ?>;<?php } ?>
		<?php if( $settings->link_color_hover ) { ?>color: #<?php echo $settings->link_color_hover; ?>;<?php } ?>
	}
<?php } ?>

/* Flips */
.fl-node-<?php echo $id; ?> .pp-flipbox {
	<?php if( $settings->flip_duration ) { ?>transition-duration: <?php echo $settings->flip_duration; ?>ms;<?php } ?>
}
