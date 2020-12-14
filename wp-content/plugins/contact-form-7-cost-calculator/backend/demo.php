<?php
if( ! class_exists( "cf7_import_demo" ) ) {
	class cf7_import_demo{
		function __construct(){
			add_filter("wpcf7_editor_panels",array($this,"custom_form"));
		}
		function custom_form($panels){
			$panels["form-panel-import-demo"] = array(
					'title' => __( 'Import Demo', 'contact-form-7' ),
					'callback' => array($this,"page" ));

			return $panels;
		}
		function page($post){
			$content ='<table class="form-table">';
			echo apply_filters("contact_form_7_import",$content,$post);
			echo '</table>';
		}
	}
	new cf7_import_demo();
}
add_filter("contact_form_7_import","contact_form_7_import_costc_alculator");
function contact_form_7_import_costc_alculator($content){
	$new_content ='<tr>
					<th scope="row">
						<label for="">
							Cost Calculator Plugin
						</label>
					</th>
					<td>
						<a class="button cf7_import_demo_calculator" href="#">Click import content demo</a>
					</td>
				</tr>';
	return $content . $new_content;
}