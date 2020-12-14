<?php
/**
* Plugin Name: Contact Form 7 Cost Calculator - Price Calculation 
* Plugin URI: https://codecanyon.net/user/rednumber/portfolio
* Description: Create forms with field values calculated based in other form field values for contact form 7
* Author: Rednumber
* Version: 5.4
* Author URI: https://codecanyon.net/user/rednumber/portfolio
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define( 'CT_7_COST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_7_COST_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CT_7_COST_TEXT_DOMAIN', "cf7_cost" );
include_once(ABSPATH.'wp-admin/includes/plugin.php');
/*
* Include pib
*/
if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )  ) {
    include CT_7_COST_PLUGIN_PATH."backend/index.php";
    include CT_7_COST_PLUGIN_PATH."frontend/index.php";
}
/*
* Check plugin contact form 7
*/
class cf7_cost_init {
    function __construct(){
       add_action('admin_notices', array($this, 'on_admin_notices' ) );
       set_error_handler( array($this, 'error_handler') );
    }
    function on_admin_notices(){
        if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )  ) {
            echo '<div class="error"><p>' . __('Plugin need active plugin Contact Form 7', CT_7_COST_TEXT_DOMAIN) . '</p></div>';
        }
    }
    public function error_handler($errno, $errstr, $errfile, $errline, $errcontext = array()) {
        $error_file = str_replace('\\', '/', $errfile);
        $content_dir = str_replace('\\', '/', WP_CONTENT_DIR . '/plugins/contact-form-7');
        $content_dir_this = str_replace('\\', '/', WP_CONTENT_DIR . '/plugins/contact-form-7-cost-calculator');
        if (strpos($error_file, $content_dir) !== false) {
            return true;
        }
        if (strpos($error_file, $content_dir_this) !== false) {
            return true;
        }
        return false;
    }

}
new cf7_cost_init;