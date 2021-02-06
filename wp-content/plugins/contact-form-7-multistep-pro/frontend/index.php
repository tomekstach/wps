<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
class cf7_multistep_frontend {
    function __construct(){
        add_filter('get_post_metadata', array($this,'getqtlangcustomfieldvalue'), 10, 4);
        //add_action("wp_enqueue_scripts",array($this,"add_lib"),1000);
        //add_filter("wpcf7_additional_mail",array($this,"block_send_email"),10,2);
        //add_filter("wpcf7_validate",array($this,"wpcf7_validate"));
    }
    /*
    * Block send email
    */
    function block_send_email($email,$contact_form){
        if( isset( $_POST["_wpcf7_check_tab"] )) {
            $tabs = count ( cf7_multistep_get_setttings($contact_form->id) -1 );
            if( $tabs != $_POST["_wpcf7_check_tab"] ) {
                return true;
            }
        }
    }
    function wpcf7_validate($result){
        $result->invalidate("step","ok");
        return $result;
    }
    /*
    * Add js and css
    */
    function add_lib(){
      $post_id = get_the_ID();
      $type = get_post_meta( $post_id,"_cf7_multistep_type",true);

        if ($type != 0 && $type) {
          wp_deregister_script("contact-form-7");
            if (version_compare(WPCF7_VERSION, '4.8') >= 0) {
                wp_register_script("contact-form-7-custom", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7_4.8.js", array('jquery', 'jquery-form'), time(), true);
                $wpcf7 = array(
                'apiSettings' => array(
                    'root' => esc_url_raw(get_rest_url()),
                    'namespace' => 'contact-form-7/v1',
                ),
                'recaptcha' => array(
                    'messages' => array(
                        'empty' =>
                            __('Please verify that you are not a robot.', 'contact-form-7'),
                    ),
                ),
            );

                if (defined('WP_CACHE') && WP_CACHE) {
                    $wpcf7['cached'] = 1;
                }

                if (wpcf7_support_html5_fallback()) {
                    $wpcf7['jqueryUi'] = 1;
                }

                wp_localize_script('contact-form-7-custom', 'wpcf7', $wpcf7);
                wp_enqueue_script('contact-form-7-custom');
            //die("ok");
            } else {
                wp_register_script("contact-form-7-custom", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7.js", array('jquery', 'jquery-form'), time(), true);
                $_wpcf7 = array(
                'recaptcha' => array(
                    'messages' => array(
                        'empty' =>
                            __('Please verify that you are not a robot.', 'contact-form-7'),
                    ),
                ),
            );

                if (defined('WP_CACHE') && WP_CACHE) {
                    $_wpcf7['cached'] = 1;
                }

                if (wpcf7_support_html5_fallback()) {
                    $_wpcf7['jqueryUi'] = 1;
                }

                wp_localize_script('contact-form-7-custom', '_wpcf7', $_wpcf7);
                wp_enqueue_script('contact-form-7-custom');
            }
            wp_enqueue_script("cf7_multistep", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7-multistep.js", array("jquery"), time());
            wp_enqueue_style("cf7_multistep", CT_7_MULTISTEP_PLUGIN_URL."frontend/css/cf7-multistep.css");
        }
    }
    /*
    * Custom steps
    */
    function getqtlangcustomfieldvalue($value, $post_id, $meta_key, $single) {
        if( !is_admin() ):
            if( $meta_key == "_form" ){
                $type = get_post_meta( $post_id,"_cf7_multistep_type",true);
                if( $type != 0 && $type){
                  wp_deregister_script("contact-form-7");
            if (version_compare(WPCF7_VERSION, '4.8') >= 0) {
                wp_register_script("contact-form-7-custom", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7_4.8.js", array('jquery', 'jquery-form'), time(), true);
                $wpcf7 = array(
                'apiSettings' => array(
                    'root' => esc_url_raw(get_rest_url()),
                    'namespace' => 'contact-form-7/v1',
                ),
                'recaptcha' => array(
                    'messages' => array(
                        'empty' =>
                            __('Please verify that you are not a robot.', 'contact-form-7'),
                    ),
                ),
            );

                if (defined('WP_CACHE') && WP_CACHE) {
                    $wpcf7['cached'] = 1;
                }

                if (wpcf7_support_html5_fallback()) {
                    $wpcf7['jqueryUi'] = 1;
                }

                wp_localize_script('contact-form-7-custom', 'wpcf7', $wpcf7);
                wp_enqueue_script('contact-form-7-custom');
            //die("ok");
            } else {
                wp_register_script("contact-form-7-custom", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7.js", array('jquery', 'jquery-form'), time(), true);
                $_wpcf7 = array(
                'recaptcha' => array(
                    'messages' => array(
                        'empty' =>
                            __('Please verify that you are not a robot.', 'contact-form-7'),
                    ),
                ),
            );

                if (defined('WP_CACHE') && WP_CACHE) {
                    $_wpcf7['cached'] = 1;
                }

                if (wpcf7_support_html5_fallback()) {
                    $_wpcf7['jqueryUi'] = 1;
                }

                wp_localize_script('contact-form-7-custom', '_wpcf7', $_wpcf7);
                wp_enqueue_script('contact-form-7-custom');
            }
            wp_enqueue_script("cf7_multistep", CT_7_MULTISTEP_PLUGIN_URL."frontend/js/cf7-multistep.js", array("jquery"), time());
            wp_enqueue_style("cf7_multistep", CT_7_MULTISTEP_PLUGIN_URL."frontend/css/cf7-multistep.css");


                    $tabs = cf7_multistep_get_setttings($post_id,true);
                    $last_form = $tabs["check"];
                    unset($tabs["check"]);
                    $count_tab = count($tabs);
                    $settings =  cf7_multistep_get_setttings_stype($post_id);
                    ob_start();
                    ?>
                    <div class="hidden multistep-check">
                    <?php echo $last_form; ?>
                    </div>
                    <div class="hidden">
                         <input name="_wpcf7_check_tab" value="1" class="wpcf7_check_tab" type="hidden" />
                        <input class="multistep_total" value="<?php echo $count_tab  ?>" type="hidden" />
                    </div><!-- /.hidden -->
                    <div class="container-cf7-steps container-cf7-steps-<?php echo $type ?>">
                        <div class="container-multistep-header <?php  if( $type == 6 ) {echo "hidden";}?>">
                            <ul class="cf7-display-steps-container cf7-display-steps-container-<?php echo $type ?>">
                                    <?php
                                    $i=1;
                                    foreach( $tabs as $key=>$value):?>
                                	<li class="<?php if( $i== 1){echo "active"; $key_active = $key; } ?> cf7-steps-<?php echo $i ?>" data-i="<?php echo $i ?>" data-tab=".cf7-tab-<?php echo $i ?>">
                                		<?php _e(apply_filters("cf7_multistep_remove_key",$key),'contact-form-7-multistep-pro'); ?>
                                	</li>
                                    <?php
                                    $i++;
                                     endforeach; ?>
                            </ul>
                        </div>
                        <div class="container-body-tab">
                            <?php
                            $i=1;
                            foreach( $tabs as $key=>$value):?>
                            <div class="cf7-tab <?php if( $i!= 1){ echo "hidden";} ?> cf7-tab-<?php echo $i ?>" >
                                <div class="cf7-content-tab">

                                    <?php echo apply_filters("cf7_multistep",$value);  ?>
                                </div>
                                <div class="multistep-nav">
                                    <div class="multistep-nav-left">
                                        <?php  if( $i!=1): ?>
                                         <a href="#" class="multistep-cf7-first"><?php _e($settings["multistep_cf7_steps_first"],'contact-form-7-multistep-pro');  ?></a><a href="#" class="multistep-cf7-prev"><?php _e($settings["multistep_cf7_steps_prev"],'contact-form-7-multistep-pro');  ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="multistep-nav-right">
                                        <?php if( $count_tab != $i ): ?>

                                        <a href="#" class="multistep-cf7-next"><?php _e($settings["multistep_cf7_steps_next"],'contact-form-7-multistep-pro');  ?> <span class="ajax-loader hidden"></span></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php $i++; endforeach; ?>
                        </div>
                    </div>
                    <style type="text/css">
                     <?php if( $type != 5) { ?>
                        .cf7-display-steps-container li {
                            background: <?php echo $settings["multistep_cf7_steps_background"] ?> !important;
                            color: <?php echo $settings["multistep_cf7_steps_color"] ?> !important;
                        }
                        .cf7-display-steps-container li.active{
                            background: <?php echo $settings["multistep_cf7_steps_inactive_background"] ?> !important;
                            color: <?php echo $settings["multistep_cf7t_steps_inactive"] ?> !important;
                        }
                        .cf7-display-steps-container li:after {
                            border-left: 16px solid <?php echo $settings["multistep_cf7_steps_background"] ?> !important;
                         }
                        .cf7-display-steps-container li.active:after {
                            border-left: 16px solid <?php echo $settings["multistep_cf7_steps_inactive_background"] ?> !important;
                        }
                        .cf7-display-steps-container li.enabled:after{
                            border-left: 16px solid <?php echo $settings["multistep_cf7t_steps_completed_backgound"] ?> !important;
                        }
                        .cf7-display-steps-container li.enabled {
                            background: <?php echo $settings["multistep_cf7t_steps_completed_backgound"] ?> !important;
                            color: <?php echo $settings["multistep_cf7_steps_completed"] ?> !important;
                        }
                        
                        <?php
                            }else{
                        ?>
                        .cf7-display-steps-container li:before {
                            background: <?php echo $settings["multistep_cf7_steps_background"] ?> !important;
                            color: <?php echo $settings["multistep_cf7_steps_color"] ?> !important;
                        }
                        .cf7-display-steps-container li.active:before{
                            background: <?php echo $settings["multistep_cf7_steps_inactive_background"] ?> !important;
                            color: <?php echo $settings["multistep_cf7t_steps_inactive"] ?> !important;
                        }
                        .cf7-display-steps-container li:after {
                            border-left: 16px solid <?php echo $settings["multistep_cf7_steps_background"] ?> !important;
                         }
                        .cf7-display-steps-container li.active:after {
                            border-left: 16px solid <?php echo $settings["multistep_cf7_steps_inactive_background"] ?> !important;
                        }
                        .cf7-display-steps-container li.enabled:after{
                            border-left: 16px solid <?php echo $settings["multistep_cf7t_steps_completed_backgound"] ?> !important;
                        }
                        .cf7-display-steps-container li.enabled:before {
                            background: <?php echo $settings["multistep_cf7t_steps_completed_backgound"] ?> !important;
                            color: <?php echo $settings["multistep_cf7_steps_completed"] ?> !important;
                        }
                        <?php

                        }
                         ?>
                        }
                        .multistep-nav a{
                            background: <?php echo $settings["multistep_cf7_steps_background"] ?> !important;
                            color: <?php echo $settings["multistep_cf7_steps_color"] ?> !important;
                            padding: 5px 15px;
                            text-decoration: none;
                        }
                        .multistep-nav {
                                display: flex;
                                margin: 30px 0;
                        }
                        .multistep-nav div {
                            width: 100%;
                        }
                        .multistep-nav-right {
                            text-align: right;
                        }
                        .cf7-display-steps-container-1 li:last-child:after {
                            display: none !important;
                        }
                    </style>
                     <script type="text/javascript">
                        var cf7_step_confirm = <?php echo json_encode( cf7_multistep_get_data_confirm($post_id) ) ?>;
                    </script>
                    <?php
                    $value = ob_get_clean();
                }
            }
         endif;
             return $value;
    }

}
new cf7_multistep_frontend;