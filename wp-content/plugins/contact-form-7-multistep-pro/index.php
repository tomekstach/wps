<?php
/*
Plugin Name: Contact Form 7 Multi-step Pro
Plugin URI: https://codecanyonwp.com/contact-form-7-multistep
Description: Plugins help provides step by step UI for your long forms with (too) many fields.
Author: Rednumber
Version: 6.1
Author URI: https://codecanyonwp.com/
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define( 'CT_7_MULTISTEP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_7_MULTISTEP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
//define( ''contact-form-7-multistep-pro'', "cf7_step" );
define( 'WPCF7_MULTI_VERSION', '5.0' );
include_once(ABSPATH.'wp-admin/includes/plugin.php');
/*
* Include pib
*/
if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )  ) {
    include CT_7_MULTISTEP_PLUGIN_PATH."backend/index.php";
    include CT_7_MULTISTEP_PLUGIN_PATH."backend/demo.php";
    include CT_7_MULTISTEP_PLUGIN_PATH."backend/confirm.php";
    include CT_7_MULTISTEP_PLUGIN_PATH."frontend/index.php";
}
/*
* Check plugin contact form 7
*/
class cf7_multistep_checkout_init {
    function __construct(){
       add_action('admin_notices', array($this, 'on_admin_notices' ) );
       set_error_handler( array($this, 'error_handler') );
       load_plugin_textdomain( 'cf7_step', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    function on_admin_notices(){
        if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )  ) {
            echo '<div class="error"><p>' . __('Plugin need active plugin Contact Form 7', 'contact-form-7-multistep-pro') . '</p></div>';
        }
    }
    public function error_handler($errno, $errstr, $errfile, $errline, $errcontext = array()) {
        $error_file = str_replace('\\', '/', $errfile);
        $content_dir = str_replace('\\', '/', WP_CONTENT_DIR . '/plugins/contact-form-7');
        $content_dir_this = str_replace('\\', '/', WP_CONTENT_DIR . '/plugins/contact-form-7-multistep-pro');
        if (strpos($error_file, $content_dir) !== false) {
            return true;
        }
        if (strpos($error_file, $content_dir_this) !== false) {
            return true;
        }
        return false;
    }
}
new cf7_multistep_checkout_init;