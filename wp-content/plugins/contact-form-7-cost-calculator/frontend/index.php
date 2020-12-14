<?php 
class cf7_cost_calculator_frontend{ 
	function __construct()
	{
		add_action("wp_enqueue_scripts",array($this,"add_lib"),1000);

        add_filter('wpcf7_form_response_output', array($this,'add_settings'),999999,4);
	}
    function add_settings($output, $class, $content,$data){
        $settings = cf7_settings_calculator::get_settings($data->id);
        $output1 ="";
        foreach ($settings as $key => $data) {
          $output1 .= '<input type="hidden" value="'.$data.'" id="cf7-calculator-'.$key.'" >';
        }
        $output1 .='
            <style type="text/css" >
                .cf7-hide {
                    display: none !important;
                }
                .cf7-calculated-name {
                    width: 100%;
                    display: inline-block;
                }
            </style>';
        if($settings["right"]=="yes"){
           $output1 .='
            <style type="text/css" >
                .ctf7-total, .cf7-calculated-name {
                    text-align: right !important;
                }

            </style>';
         }
        return $output.$output1;
    }
	 /*
    }
    }
    * Add js and css
    */
    function add_lib(){
        wp_enqueue_script("cf7_calculator",CT_7_COST_PLUGIN_URL."frontend/js/cf7_calculator.js",array(),time());

    }
}
new cf7_cost_calculator_frontend;