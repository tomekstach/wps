<?php

class VamtamRevslider extends FLBuilderModule {

	public function __construct() {
		$path = trailingslashit( 'modules/' . basename( dirname( __FILE__ ) ) );

		parent::__construct(array(
			'name'            => __( 'Slider Revolution', 'vamtam-elements-b' ),
			'description'     => '',
			'category'        => __( 'VamTam Modules', 'vamtam-elements-b' ),
			'partial_refresh' => false,
			'editor_export'   => false,
			'enabled'         => true,
			'dir'             => VAMTAMEL_B_DIR . $path,
			'url'             => VAMTAMEL_B_URL . $path,
		));
	}
}

FLBuilder::register_module( 'VamtamRevslider', array(
	'vamtam-revslider-tab-basic' => array(
		'title'    => __( 'Basic', 'vamtam-elements-b' ),
		'sections' => array(
			'vamtam-revslider-section-main' => array(
				'title'  => __( 'Main', 'vamtam-elements-b' ),
				'fields' => array(
					'alias' => array(
						'label'   => esc_html__( 'Slider', 'wpv' ),
						'default' => '',
						'type'    => 'select',
						'options' => class_exists( 'VamtamTemplates' ) ? VamtamTemplates::get_rev_sliders( '' ) : array(),
					),
				),
			),
		),
	),
) );
