<?php

/**
 * @package wp_w3all
 */
/*
Plugin Name: WordPress w3all phpBB integration
Plugin URI: http://axew3.com/w3
Description: Integration plugin between WordPress and phpBB. It provide free integration - users transfer/login/register. Easy, light, secure, powerful
Version: 2.0.8
Author: axew3
Author URI: http://www.axew3.com/w3
License: GPLv2 or later
Text Domain: wp-w3all-phpbb-integration
Domain Path: /languages/

=====================================================================================
Copyright (C) 2020 - axew3.com
=====================================================================================
*/
// Security
defined('ABSPATH') or die('forbidden');
if (!function_exists('add_action')) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

if (defined('W3PHPBBUSESSION') or defined('W3PHPBBLASTOPICS') or defined('W3PHPBBCONFIG') or defined('W3UNREADTOPICS') or defined('W3ALLPHPBBUAVA')) :
  echo 'Sorry, something goes wrong';
  exit;
endif;

// Set integration as 'Not Linked Users'
if (get_option('w3all_not_link_phpbb_wp') == 1) {
  define('WPW3ALL_NOT_ULINKED', true);
}
define('WPW3ALL_VERSION', '2.0.8');
define('WPW3ALL_MINIMUM_WP_VERSION', '4.0');
define('WPW3ALL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPW3ALL_PLUGIN_DIR', plugin_dir_path(__FILE__));

// FORCE the reset of cookie domain (removing the two chars // in front on next line)
// $w3reset_cookie_domain = '.mydomain.com'; // (set 'localhost' if you're on localhost) change to fit THE SAME COOKIE DOMAIN SETTING as you have set it in phpBB config. To RESET/force cookie domain setting: remove // chars in front of this line and save, than load any WP page one time. So comment out other time this line re-adding // chars and save.

// FORCE Deactivation WP_w3all plugin //
// $w3deactivate_wp_w3all_plugin = 'true';

$w3all_w_lastopicspost_max = get_option('widget_wp_w3all_widget_last_topics');
$config_avatars = get_option('w3all_conf_avatars');
$w3all_conf_pref = get_option('w3all_conf_pref');
$w3cookie_domain = get_option('w3all_phpbb_cookie');
$w3all_bruteblock_phpbbulist = empty(get_option('w3all_bruteblock_phpbbulist')) ? array() : get_option('w3all_bruteblock_phpbbulist');
$w3all_path_to_cms = get_option('w3all_path_to_cms');
$w3all_exclude_id1 = get_option('w3all_exclude_id1');

if (isset($w3reset_cookie_domain)) {
  update_option('w3all_phpbb_cookie', $w3reset_cookie_domain);
  $w3cookie_domain = $w3reset_cookie_domain;
}

$useragent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? esc_sql(trim($_SERVER['HTTP_USER_AGENT'])) : 'unknown';
$w3all_config_avatars = unserialize($config_avatars);
$w3all_get_phpbb_avatar_yn = isset($w3all_config_avatars['w3all_get_phpbb_avatar_yn']) ? $w3all_config_avatars['w3all_get_phpbb_avatar_yn'] : '';
$w3all_last_t_avatar_yn = isset($w3all_config_avatars['w3all_avatar_on_last_t_yn']) ? $w3all_config_avatars['w3all_avatar_on_last_t_yn'] : '';
$w3all_last_t_avatar_dim = isset($w3all_config_avatars['w3all_lasttopic_avatar_dim']) ? $w3all_config_avatars['w3all_lasttopic_avatar_dim'] : '';
$w3all_lasttopic_avatar_num = isset($w3all_config_avatars['w3all_lasttopic_avatar_num']) ? $w3all_config_avatars['w3all_lasttopic_avatar_num'] : '';
$w3all_avatar_replace_bp_yn = isset($w3all_config_avatars['w3all_avatar_replace_bp_yn']) ? $w3all_config_avatars['w3all_avatar_replace_bp_yn'] : '0'; // not used
$w3all_avatar_via_phpbb_file = isset($w3all_config_avatars['w3all_avatar_via_phpbb_file_yn']) ? $w3all_config_avatars['w3all_avatar_via_phpbb_file_yn'] : 0;

$w3all_conf_pref = unserialize($w3all_conf_pref);
$w3all_transfer_phpbb_yn = isset($w3all_conf_pref['w3all_transfer_phpbb_yn']) ? $w3all_conf_pref['w3all_transfer_phpbb_yn'] : '';
$w3all_phpbb_widget_mark_ru_yn = isset($w3all_conf_pref['w3all_phpbb_widget_mark_ru_yn']) ? $w3all_conf_pref['w3all_phpbb_widget_mark_ru_yn'] : '';
$w3all_phpbb_widget_FA_mark_yn = isset($w3all_conf_pref['w3all_phpbb_widget_FA_mark_yn']) ? $w3all_conf_pref['w3all_phpbb_widget_FA_mark_yn'] : 0;

$w3all_phpbb_user_deactivated_yn = isset($w3all_conf_pref['w3all_phpbb_user_deactivated_yn']) ? $w3all_conf_pref['w3all_phpbb_user_deactivated_yn'] : '';
$w3all_phpbb_wptoolbar_pm_yn = isset($w3all_conf_pref['w3all_phpbb_wptoolbar_pm_yn']) ? $w3all_conf_pref['w3all_phpbb_wptoolbar_pm_yn'] : '';
$w3all_exclude_phpbb_forums = isset($w3all_conf_pref['w3all_exclude_phpbb_forums']) ? $w3all_conf_pref['w3all_exclude_phpbb_forums'] : '';
$w3all_phpbb_lang_switch_yn = isset($w3all_conf_pref['w3all_phpbb_lang_switch_yn']) ? $w3all_conf_pref['w3all_phpbb_lang_switch_yn'] : 0;
$w3all_get_topics_x_ugroup = isset($w3all_conf_pref['w3all_get_topics_x_ugroup']) ? $w3all_conf_pref['w3all_get_topics_x_ugroup'] : 0;
$w3all_custom_output_files = isset($w3all_conf_pref['w3all_custom_output_files']) ? $w3all_conf_pref['w3all_custom_output_files'] : 0;
$w3all_profile_sync_bp_yn = isset($w3all_conf_pref['w3all_profile_sync_bp_yn']) ? $w3all_conf_pref['w3all_profile_sync_bp_yn'] : 0;
$w3all_add_into_spec_group = isset($w3all_conf_pref['w3all_add_into_spec_group']) ? $w3all_conf_pref['w3all_add_into_spec_group'] : 2;
$w3all_wp_phpbb_lrl_links_switch_yn = isset($w3all_conf_pref['w3all_wp_phpbb_lrl_links_switch_yn']) ? $w3all_conf_pref['w3all_wp_phpbb_lrl_links_switch_yn'] : 0;
$w3all_phpbb_mchat_get_opt_yn = isset($w3all_conf_pref['w3all_phpbb_mchat_get_opt_yn']) ? $w3all_conf_pref['w3all_phpbb_mchat_get_opt_yn'] : 0;
$w3all_anti_brute_force_yn = isset($w3all_conf_pref['w3all_anti_brute_force_yn']) ? $w3all_conf_pref['w3all_anti_brute_force_yn'] : 0;
$w3all_custom_iframe_yn = isset($w3all_conf_pref['w3all_custom_iframe_yn']) ? $w3all_conf_pref['w3all_custom_iframe_yn'] : 0;

// The follow get the max number of topics to retrieve
// it is passed on private static function last_forums_topics($ntopics = 10){
// to define W3PHPBBLASTOPICS when 'at MAX'
// so it is used on class.wp.w3all.widgets-phpbb.php 
// inside public function wp_w3all_phpbb_last_topics($post_text, $topics_number, $text_words) {
// to avoid another call if possible


if (!empty($w3all_w_lastopicspost_max)) {
  foreach ($w3all_w_lastopicspost_max as $row) {
    if (isset($row['topics_number'])) {
      $w3all_wlastopicspost_max[] = $row['topics_number'];
    }
  }
  $w3all_wlastopicspost_max = isset($w3all_wlastopicspost_max) && is_array($w3all_wlastopicspost_max) ? max($w3all_wlastopicspost_max) : 5;
} else {
  $w3all_wlastopicspost_max = 5;
}


if (defined('WP_ADMIN')) {

  function w3all_VAR_IF_U_CAN()
  {

    if (!current_user_can('manage_options') && isset($_POST["w3all_conf"]["w3all_url_to_cms"]) or !current_user_can('manage_options') && isset($_POST["w3all_conf"]["w3all_path_to_cms"])) {
      unset($_POST);
      die('<h3>you can\'t perfom this action.</h3>');
    }

    if (isset($_POST["w3all_conf"]["w3all_url_to_cms"])) {
      $_POST["w3all_conf"]["w3all_url_to_cms"] = trim($_POST["w3all_conf"]["w3all_url_to_cms"]);
    }

    if (isset($_POST["w3all_conf"]["w3all_path_to_cms"])) {
      // register the uninstall hook here
      register_uninstall_hook(__FILE__, array('WP_w3all_admin', 'clean_up_on_plugin_off'));
      $_POST["w3all_conf"]["w3all_path_to_cms"] = trim($_POST["w3all_conf"]["w3all_path_to_cms"]);
      $up_conf_w3all_url = admin_url() . 'options-general.php?page=wp-w3all-options';
      wp_redirect($up_conf_w3all_url);
      $config_file = $_POST["w3all_conf"]["w3all_path_to_cms"] . '/config.php';
      ob_start();
      include_once($config_file);
      ob_end_clean();
    }
  }
  add_action('init', 'w3all_VAR_IF_U_CAN');

  // do not fire phpbb_update_profile on wp update profile subsite profile
  // this result empty

  if (!empty($w3all_path_to_cms)) {   // or will search for some config file elsewhere instead 


    $config_file = get_option('w3all_path_to_cms') . '/config.php';
    if (file_exists($config_file)) {
      ob_start();
      include_once($config_file);
      ob_end_clean();
    }
  }

  if (defined('PHPBB_INSTALLED') && !isset($w3deactivate_wp_w3all_plugin)) {

    if (defined('WP_W3ALL_MANUAL_CONFIG')) {
      $w3all_config = array('dbms' => $w3all_dbms, 'dbhost' => $w3all_dbhost, 'dbport' => $w3all_dbport, 'dbname' => $w3all_dbname, 'dbuser'   => $w3all_dbuser, 'dbpasswd' => $w3all_dbpasswd, 'table_prefix' => $w3all_table_prefix, 'acm_type' => $w3all_acm_type);
    } else {
      $w3all_config = array('dbms' => $dbms, 'dbhost' => $dbhost, 'dbport' => $dbport, 'dbname' => $dbname, 'dbuser' => $dbuser, 'dbpasswd' => $dbpasswd, 'table_prefix' => $table_prefix, 'acm_type' => $acm_type);
    }

    require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all-phpbb.php');
    add_action('init', array('WP_w3all_phpbb', 'w3all_get_phpbb_config_res'), 1); // before any other
  }

  require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all-admin.php');
  require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all.widgets-phpbb.php');
  add_action('init', array('WP_w3all_admin', 'wp_w3all_init'));

  if (defined('PHPBB_INSTALLED') && !isset($w3deactivate_wp_w3all_plugin)) {

    add_action('init', array('WP_w3all_phpbb', 'wp_w3all_phpbb_conn_init'));
    add_action('init', array('WP_w3all_phpbb', 'wp_w3all_phpbb_init'), 2);

    function wp_w3all_phpbb_registration_save($user_id)
    {

      if (is_multisite() or defined('W3DISABLECKUINSERTRANSFER')) {
        return;
      } // or get error on activating MUMS user ... msmu user will use a different way
      // while transferring users from phpBB to Wp, disable also this hook: on views/wp_w3all_users_to_wp.php -> define( "W3DISABLECKUINSERTRANSFER", true );
      // the check in this case is done directly within the transfer process

      $wpu  = get_user_by('id', $user_id);

      if (!$wpu) {
        return;
      }

      $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($wpu->user_login, $wpu->user_email, 1);

      if ($wp_w3_ck_phpbb_ue_exist === true && function_exists('wp_delete_user')) {

        wp_delete_user($user_id); // remove WP user just created, username or email exist on phpBB
        if (is_multisite() == true) {
          wpmu_delete_user($user_id);
        }
        temp_wp_w3_error_on_update();
        exit;  // REVIEW // REVIEW // add_action( 'admin_notices', 

      }

      if (!$wp_w3_ck_phpbb_ue_exist) {
        $phpBB_user_add = WP_w3all_phpbb::create_phpBB_user_res($wpu);
      }
    }

    // review this
    function wp_w3all_up_phpbb_prof($user_id, $old_user_data)
    {

      $phpBB_upp = WP_w3all_phpbb::phpbb_update_profile($user_id, $old_user_data);

      $redirect_to = '';
      if ($phpBB_upp === true && current_user_can('manage_options')) {
        $redirect_to = admin_url() . 'user-edit.php?user_id=' . $user_id;
      }

      if ($phpBB_upp === true) {
        temp_wp_w3_error_on_update($redirect_to);
        exit;
      }
    }

    if (!defined("WPW3ALL_NOT_ULINKED")) {
      // stuff about profile changes WP to phpBB
      add_action('profile_update', 'wp_w3all_up_phpbb_prof', 10, 2);
      add_action('user_register', 'wp_w3all_phpbb_registration_save', 10, 1);
      add_action('delete_user', array('WP_w3all_phpbb', 'wp_w3all_phpbb_delete_user'));
      if (!defined("PHPBBCOOKIERELEASED")) {
        define("PHPBBCOOKIERELEASED", true);
        add_action('set_logged_in_cookie', 'wp_w3all_user_session_set', 10, 5);
      }
      if (!empty($w3all_phpbb_wptoolbar_pm_yn)) {
        add_action('admin_bar_menu', 'wp_w3all_toolbar_new_phpbbpm', 999);  // notify about new phpBB pm
      }
    }

    function wp_w3all_user_session_set($logged_in_cookie, $expire, $expiration, $user_id, $scheme)
    {
      $user = get_user_by('ID', $user_id);
      $phpBB_user_session_set = WP_w3all_phpbb::phpBB_user_session_set_res($user);
      return;
    }
  } // if defined phpbb installed end

} else { // not in admin

  // or will search for some config file elsewhere instead
  $w3all_path_to_cms = get_option('w3all_path_to_cms');
  if (!empty($w3all_path_to_cms)) {
    $config_file = get_option('w3all_path_to_cms') . '/config.php';
    if (file_exists($config_file)) {
      ob_start();
      include_once($config_file);
      ob_end_clean();
    }
  }

  if (defined('PHPBB_INSTALLED') && !isset($w3deactivate_wp_w3all_plugin)) {

    if (defined('WP_W3ALL_MANUAL_CONFIG')) {

      $w3all_config = array('dbms' => $w3all_dbms, 'dbhost' => $w3all_dbhost, 'dbport' => $w3all_dbport, 'dbname' => $w3all_dbname, 'dbuser' => $w3all_dbuser, 'dbpasswd' => $w3all_dbpasswd, 'table_prefix' => $w3all_table_prefix, 'acm_type' => $w3all_acm_type);
    } else {

      $w3all_config = array('dbms' => $dbms, 'dbhost' => $dbhost, 'dbport' => $dbport, 'dbname' => $dbname, 'dbuser' => $dbuser, 'dbpasswd' => $dbpasswd, 'table_prefix' => $table_prefix, 'acm_type' => $acm_type);
    }

    $phpbb_on_template_iframe = get_option('w3all_iframe_phpbb_link_yn');
    $wp_w3all_forum_folder_wp = get_option('w3all_forum_template_wppage'); // remove from iframe mode links on last topics than
    $w3all_url_to_cms         = get_option('w3all_url_to_cms');

    require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all-phpbb.php');
    require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all.widgets-phpbb.php');

    add_action('init', array('WP_w3all_phpbb', 'w3all_get_phpbb_config_res'), 1); // before any other wp_w3all
    add_action('init', array('WP_w3all_phpbb', 'wp_w3all_phpbb_init'), 2);

    if (!empty($w3all_phpbb_wptoolbar_pm_yn)) {
      add_action('admin_bar_menu', 'wp_w3all_toolbar_new_phpbbpm', 999);  // notify about new phpBB pm
    }

    //w3all Login widget check credentials // moved into if(! defined("WPW3ALL_NOT_ULINKED")){
    //if(isset($_POST['w3all_username']) && isset($_POST['w3all_password'])){
    //add_action( 'init', 'w3all_login_widget'); 
    function w3all_login_widget()
    {
      global $wpdb, $w3all_anti_brute_force_yn, $w3all_bruteblock_phpbbulist, $w3cookie_domain;
      $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
      $passed_uname = sanitize_user($_POST['w3all_username'], $strict = false);
      if (empty($passed_uname) or strlen($passed_uname) > 50) {
        if (strpos($_POST['redirect_to'], '?')) {
          wp_safe_redirect($_POST['redirect_to'] . '&reauth=2');
          exit;
        } else {
          wp_safe_redirect($_POST['redirect_to'] . '?reauth=2');
          exit;
        }
        return;
      }
      $user = empty($passed_uname) ? array() : WP_w3all_phpbb::wp_w3all_get_phpbb_user_info($passed_uname);

      if (!empty($user)) { // add this phpBB user in Wp if still not existent
        $ck_wpu_exists = username_exists($user[0]->username);
        $user_id = email_exists($user[0]->user_email);
        // phpBB username chars fix          	   	
        // phpBB need to have users without characters like ' that is not allowed in WP as username by default
        /*if ( preg_match('/[^-0-9A-Za-z _.@]/',$user[0]->username) ){
	          echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>'.$phpbb_user_session[0]->username.'</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
           return;
         }*/

        if (!$user_id && !$ck_wpu_exists) { // add this user that not exists in WP  
          $userdata = array(
            'user_login'       =>  $user[0]->username,
            'user_pass'        =>  $user[0]->user_password,
            'user_email'       =>  $user[0]->user_email,
            'user_registered'  =>  date_i18n('Y-m-d H:i:s', $user[0]->user_regdate),
            'role'             =>  'subscriber'
          );
          // adding as subscriber by default, or add code and edit query above to retrieve also user group
          $user_id = wp_insert_user($userdata);
          if (!is_wp_error($user_id)) {


            // * update user_login and user_nicename and force to be what needed
            $user_username = esc_sql($user[0]->username);
            $user_username_clean = esc_sql($user[0]->username_clean);
            $wpdb->query("UPDATE $wpu_db_utab SET user_login = '" . $user_username . "', user_nicename = '" . $user_username_clean . "' WHERE ID = '$user_id'");
          }
        }
      } // END // add this phpBB user in Wp if still not existent

      // if user just inserted, at this point $wp_signon fail, despite right credentials may passed
      if (!$ck_wpu_exists) {
        $pass_match = wp_check_password($_POST['w3all_password'], $user[0]->user_password, $user_id);

        if ($pass_match) {
          $remember = 1; // temp all remember
          // $wpu = get_user_by( 'ID', $user_id ); // this seem to sanitize returned value for non latin chars
          $wpu = $wpdb->get_row("SELECT * FROM $wpu_db_utab WHERE user_login = '" . $user[0]->username . "' OR ID = '" . $user_id . "'");

          wp_set_current_user($wpu->ID, $wpu->user_login);
          wp_set_auth_cookie($wpu->ID, $remember, is_ssl());
          do_action('wp_login', $wpu->user_login, $wpu);
          if (!defined("PHPBBCOOKIERELEASED") && $wpu->ID != 1 && $w3all_exclude_id1 != 1) {
            $phpBB_user_session_set = WP_w3all_phpbb::phpBB_user_session_set_res($wpu);
            define("PHPBBCOOKIERELEASED", true); // then the session will be set on_login hook, if this filter bypassed
          }
        }
      } else {
        $w3all_exec_u_login = wp_signon(array('user_login' => $_POST['w3all_username'], 'user_password' => trim($_POST['w3all_password']), 'remember' => 1), is_ssl()); // remember = true -> lead to fail login
      }

      // signon fail
      if (isset($w3all_exec_u_login) && is_wp_error($w3all_exec_u_login) or isset($pass_match) && !$pass_match) {
        if ($w3all_anti_brute_force_yn == 1 && isset($user[0]->user_id)) {
          $w3all_bruteblock_phpbbulist[$user[0]->user_id] = $user[0]->username;
          update_option('w3all_bruteblock_phpbbulist', $w3all_bruteblock_phpbbulist);
        }

        if (strpos($_POST['redirect_to'], '?')) {
          wp_safe_redirect($_POST['redirect_to'] . '&reauth=1');
          exit;
        } else {
          wp_safe_redirect($_POST['redirect_to'] . '?reauth=1');
          exit;
        }
      } else { // signon success
        wp_set_current_user($w3all_exec_u_login->data->ID, $w3all_exec_u_login->data->user_login);

        // Bruteforce phpBB session keys Prevention reset

        if (isset($user[0]) && isset($w3all_bruteblock_phpbbulist[$user[0]->user_id])) {
          unset($w3all_bruteblock_phpbbulist[$user[0]->user_id]);
          $w3all_bruteblock_phpbbulist = empty($w3all_bruteblock_phpbbulist) ? array() : $w3all_bruteblock_phpbbulist; // assure correct empty array to return
          update_option('w3all_bruteblock_phpbbulist', $w3all_bruteblock_phpbbulist);
        }
        // Remove cookie that fire wp_login block msg if it exist
        setcookie("w3all_bruteblock", "", time() - 31622400, "/", "$w3cookie_domain");
      }
      unset($GLOBALS['w3all_username'], $GLOBALS['w3all_password']); // unset nothing at all

      wp_safe_redirect($_POST['redirect_to']);
      exit;
    }

    //}

    // See step Bruteforce 'phpBB session keys Prevention check' for this, into class.wp.w3all-phpbb.php
    if (isset($_COOKIE["w3all_bruteblock"]) && $_COOKIE["w3all_bruteblock"] > 0) {
      function w3all_bruteblock_login_message($message)
      {
        return __('<strong>Notice: account Lockdown<br />Please re-login!</strong><br />You\'ve been logged out due to detected brute force attack against your Profile User Account!<br /><strong>To fix the problem, please login now here!</strong>', 'wp-w3all-phpbb-integration');
      }
      add_filter('login_message', 'w3all_bruteblock_login_message', 10, 1);
    }

    function wp_w3all_check_fields($errors = '', $sanitized_user_login, $user_email)
    {

      global $wpdb;

      if (WP_w3all_phpbb::w3_phpbb_ban($phpbb_u = '', $sanitized_user_login, $user_email) === true) {
        $errors->add('w3all_user_banned', __('<strong>ERROR</strong>: provided email is not correct or the email address or the IP address result banned on our forum.', 'wp-w3all-phpbb-integration'));
        return $errors;
      }

      $test = WP_w3all_phpbb::phpBB_user_check2($errors, $sanitized_user_login, $user_email);

      if ($test === true) {
        $errors->add('w3all_user_exist', __('<strong>ERROR</strong>: provided email or username already exist on our forum database.', 'wp-w3all-phpbb-integration'));
        return $errors;
      }

      return $errors;
    }


    function wp_w3all_wp_after_password_reset($user, $new_pass)
    {
      $phpBB_user_pass_set = WP_w3all_phpbb::phpbb_pass_update_res($user, $new_pass);
      $phpBB_user_activate = WP_w3all_phpbb::wp_w3all_wp_after_pass_reset($user);
    }

    function wp_w3all_phpbb_registration_save2($user_id)
    {

      $wpu = get_user_by('ID', $user_id);

      $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($wpu->user_login, $wpu->user_email, 0);

      if (!$wp_w3_ck_phpbb_ue_exist) {
        $phpBB_user_add = WP_w3all_phpbb::create_phpBB_user_res($wpu);
      }
    }

    function wp_w3all_phpbb_login($user_login, $user = '')
    {
      global $w3cookie_domain;

      // execute from 1.9.9 only if // on wp_check_password filter not fired
      if (!defined("PHPBBCOOKIERELEASED")) {
        $phpBB_user_session_set = WP_w3all_phpbb::phpBB_user_session_set_res($user);
        define("PHPBBCOOKIERELEASED", true);
      }
    }

    function wp_w3all_up_wp_prof_on_phpbb($user_id, $old_user_data)
    {

      $phpBB_user_up_prof_on_wp_prof_up = WP_w3all_phpbb::phpbb_update_profile($user_id, $old_user_data);

      if ($phpBB_user_up_prof_on_wp_prof_up === true) {

        temp_wp_w3_error_on_update();
        exit;
      }
    }

    if (!defined("WPW3ALL_NOT_ULINKED")) {
      if (isset($_POST['w3all_username']) && isset($_POST['w3all_password'])) {
        add_action('init', 'w3all_login_widget');
      }
      add_filter('auth_cookie_expiration', 'w3all_rememberme_long');
      add_filter('registration_errors', 'wp_w3all_check_fields', 10, 3); // this prevent any user addition, if phpBB email or username already exist in phpBB
      add_action('user_register', 'wp_w3all_phpbb_registration_save2', 10, 1);
      add_action('after_password_reset', 'wp_w3all_wp_after_password_reset', 10, 2);
      // a phpBB user not logged into phpBB, WP login first time 
      add_action('wp_authenticate', array('WP_w3all_phpbb', 'w3_check_phpbb_profile_wpnu'), 10, 1);
      add_action('wp_logout', array('WP_w3all_phpbb', 'wp_w3all_phpbb_logout'));
      add_action('profile_update', 'wp_w3all_up_wp_prof_on_phpbb', 10, 2);
      add_action('wp_login', 'wp_w3all_phpbb_login', 10, 2);
      if ($w3all_phpbb_widget_mark_ru_yn == 1) {
        add_action('init', array('WP_w3all_phpbb', 'w3all_get_unread_topics'), 9);
        if ($w3all_phpbb_widget_FA_mark_yn == 1) {
          add_action('wp_head', 'wp_w3all_add_phpbb_font_awesome');
        }
      }
      add_action('init', 'w3all_add_phpbb_user');
    }

    if (get_option('w3all_iframe_phpbb_link_yn') == 1) {
      add_action('wp_enqueue_scripts', 'w3all_iframe_href_switch');
    }

    function wp_w3all_add_phpbb_font_awesome()
    {
      // retrieve css font awesome from phpBB 
      echo "<link rel=\"stylesheet\" href=\"" . get_option('w3all_url_to_cms') . "/assets/css/font-awesome.min.css\" />
";
    }

    function w3all_iframe_href_switch()
    {
      // just switch href, to point to WP page, that contain/display phpBB iframe // -> views/phpbb_last_topics.php and views/phpbb_last_topics_output_shortcode.php
      echo "<script type=\"text/javascript\">function w3allIframeHref(ids,res){ ids='#'+ids;jQuery(ids).attr('href',res); }</script>
";
    }

    function phpbb_auth_login_url($login_url, $redirect, $force_reauth)
    {

      global $w3all_url_to_cms, $phpbb_on_template_iframe, $wp_w3all_forum_folder_wp;

      if ($phpbb_on_template_iframe == 1) {

        $wp_w3all_forum_folder_wp = "index.php/" . $wp_w3all_forum_folder_wp;
        $redirect = $wp_w3all_forum_folder_wp . '/?mode=login';
        return $redirect;
      } else { // lost pass no iframe

        $redirect = $w3all_url_to_cms . '/ucp.php?mode=login';
        return $redirect;
      }
    }

    function phpbb_reset_pass_url($lostpassword_url, $redirect)
    {

      global $w3all_url_to_cms, $phpbb_on_template_iframe, $wp_w3all_forum_folder_wp;

      if ($phpbb_on_template_iframe == 1) { // lost pass phpBB link iframe mode

        $wp_w3all_forum_folder_wp = "index.php/" . $wp_w3all_forum_folder_wp;
        $redirect = $wp_w3all_forum_folder_wp . '/?mode=sendpassword';
        return $redirect;
      } else { // lost pass no iframe

        $redirect = $w3all_url_to_cms . '/ucp.php?mode=sendpassword';
        return $redirect;
      }
    }



    function phpbb_register_url($register_url)
    {
      global $w3all_url_to_cms, $phpbb_on_template_iframe, $wp_w3all_forum_folder_wp;

      if ($phpbb_on_template_iframe == 1) {

        $wp_w3all_forum_folder_wp = "index.php/" . $wp_w3all_forum_folder_wp;
        $redirect = $wp_w3all_forum_folder_wp . '/?mode=register';
        return $redirect;
      } else { // register no iframe, direct link to phpBB

        $redirect = $w3all_url_to_cms . '/ucp.php?mode=register';
        return $redirect;
      }
    }

    function w3all_rememberme_long($expire)
    { // Set remember me wp cookie to expire in one year
      // the same do phpBB_user_session_set()
      return 31536000; // YEAR_IN_SECONDS;
    }
  } // end PHPBB_INSTALLED
} // end not in admin

if (defined('PHPBB_INSTALLED') && !isset($w3deactivate_wp_w3all_plugin)) {

  // get all phpBB user capabilities
  // TODO: put this into main user query, on class.wp.w3all-phpbb.php
  if ($w3all_phpbb_mchat_get_opt_yn > 0) {
    add_action('wp_head', 'wp_w3all_add_custom_js_css');
    // add_action('wp_footer','wp_w3all_add_wp_footer_common_js');
  }

  function wp_w3all_add_wp_footer_common_js()
  {
    // nothing to do at moment
  }

  function wp_w3all_add_custom_js_css()
  {
    global $w3all_custom_output_files;
    if (is_page(get_option('w3all_forum_template_wppage'))) { // avoid on page-forum? maybe yes maybe not: you need more options to check against in case, to make the joke work in any forum page situation. So in the while this is it
      return;
    }
    echo '<script type="text/javascript" src="' . plugins_url() . '/wp-w3all-phpbb-integration/addons/resizer/iframeResizer.min.js"></script>';
    if ($w3all_custom_output_files == 1) { // custom file
      include_once(ABSPATH . 'wp-content/plugins/wp-w3all-config/custom_js_css.php');
    } else { // default plugin file
      include_once(WPW3ALL_PLUGIN_DIR . '/addons/custom_js_css.php');
    }
  }

  // Swap WordPress default Login, Register and Lost Password links
  if ($w3all_wp_phpbb_lrl_links_switch_yn > 0) {
    // this affect the lost password url on WP  
    add_filter('lostpassword_url', 'phpbb_reset_pass_url', 10, 2);
    // this affect the register url on WP
    add_filter('register_url', 'phpbb_register_url', 10, 1);
    // this affect the login url on WP
    // try to avoid if direct call to wp-admin directly: in this case if option "Membership -> Anyone can register" is set to NO, this will return:
    // Warning: call_user_func_array() expects parameter 1 to be a valid callback, function 'phpbb_auth_login_url' not found or invalid function name /wp-includes/class-wp-hook.php on line 288 
    if (strpos($_SERVER['SCRIPT_NAME'], 'wp-admin') === false) {
      add_filter('login_url', 'phpbb_auth_login_url', 10, 3);
    }
  }

  if (!is_admin()) {

    // w3allfeed // unique shortcode that can run without integration active, so this is added more below
    //add_shortcode( 'w3allfeed', array( 'WP_w3all_phpbb', 'wp_w3all_feeds_short' ) );
    add_shortcode('w3allphpbbupm', array('WP_w3all_phpbb', 'wp_w3all_phpbb_upm_short'));
    add_shortcode('w3allforumpost', array('WP_w3all_phpbb', 'wp_w3all_get_phpbb_post_short'));
    add_shortcode('w3allastopics', array('WP_w3all_phpbb', 'wp_w3all_get_phpbb_lastopics_short'));
    add_shortcode('w3allastopicforumsids', array('WP_w3all_phpbb', 'wp_w3all_phpbb_last_topics_single_multi_fp_short'));
    // the query inside the function search all latest updated topics that contains ALMOST an attach and will return only the older (so the first, time based) inserted attachment that belong to the topic
    add_shortcode('w3allastopicswithimage', array('WP_w3all_phpbb', 'wp_w3all_get_phpbb_lastopics_short_wi'));
    if ($w3all_phpbb_mchat_get_opt_yn == 1) {
      add_shortcode('w3allphpbbmchat', array('WP_w3all_phpbb', 'wp_w3all_get_phpbb_mchat_short'));
    }
    if ($w3all_custom_iframe_yn == 1) {
      add_shortcode('w3allcustomiframe', array('WP_w3all_phpbb', 'wp_w3all_custom_iframe_short'));
      // do not re-add the iframe lib if on page-forum.php and if not possible to check add by the way 
      if (!empty($_SERVER['REQUEST_URI']) && !strpos($_SERVER['REQUEST_URI'], $wp_w3all_forum_folder_wp) or empty($_SERVER['REQUEST_URI'])) {
        add_action('wp_head', array('WP_w3all_phpbb', 'wp_w3all_add_iframeResizer_lib'));
      }
    }
  }

  // signup common check: on signup user check for duplicate // check for $_POST vars passed in case if need to add something for some else
  // this has been added for Buddypress compatibility, but work for any signup fired
  if (!defined("WPW3ALL_NOT_ULINKED")) {
    add_filter('validate_username', 'w3all_on_signup_check', 10, 2);
    add_action('init', 'w3all_add_phpbb_user');
  }
  function w3all_on_signup_check($valid, $username)
  {

    if (isset($_POST['signup_username']) && isset($_POST['signup_email'])) {
      $username = sanitize_user($_POST['signup_username'], $strict = false);
      $email = sanitize_email($_POST['signup_email']);
      if (!is_email($email)) {
        echo $message = __('<h3>Error: email address not valid.</h3><br />', 'wp-w3all-phpbb-integration') . '<h4><a href="' . get_edit_user_link() . '">' . __('Return back', 'wp_w3all_phpbb') . '</a><h4>';
        exit;
      }
      $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($username, $email, 0);
      if ($wp_w3_ck_phpbb_ue_exist === true) {
        temp_wp_w3_error_on_update();
        exit;
      }
    }

    return $valid;
  }

  // END signup common check

  function temp_wp_w3_error_on_update($redirect_to = '')
  {

    if (!empty($redirect_to) && current_user_can('manage_options')) {
      echo $message = __('<h3>Error: username or email already exist</h3> The username or/and email address provided already exist, or result associated, to another existent user account on our forum database.<br />', 'wp-w3all-phpbb-integration') . '<h4><a href="' . $redirect_to . '">' . __('Please return back', 'wp_w3all_phpbb') . '</a><h4>';
    } else {
      echo $message = __('<h3>Error: username or email already exist</h3> The username or/and email address provided already exist, or result associated, to another existent user account on our forum database.<br />', 'wp-w3all-phpbb-integration') . '<h4><a href="' . get_edit_user_link() . '">' . __('Please return back', 'wp_w3all_phpbb') . '</a><h4>';
    }
  }


  function wp_w3all_toolbar_new_phpbbpm($wp_admin_bar)
  {
    global $w3all_phpbb_wptoolbar_pm_yn;

    if (defined("W3PHPBBUSESSION") && $w3all_phpbb_wptoolbar_pm_yn == 1) {
      $phpbb_user_session = unserialize(W3PHPBBUSESSION);
      if ($phpbb_user_session[0]->user_unread_privmsg > 0) {
        $hrefmode = get_option('w3all_iframe_phpbb_link_yn') == 1 ? get_home_url() . "/index.php/" . get_option('w3all_forum_template_wppage') . '/?i=pm&amp;folder=inbox">' : get_option('w3all_url_to_cms') . '/ucp.php?i=pm&amp;folder=inbox';
        $args_meta = array('class' => 'w3all_phpbb_pmn');
        $args = array(
          'id'    => 'w3all_phpbb_pm',
          'title' => __('You have ', 'wp-w3all-phpbb-integration') . $phpbb_user_session[0]->user_unread_privmsg . __(' unread forum PM', 'wp-w3all-phpbb-integration'),
          'href'  => $hrefmode,
          'meta' => $args_meta
        );

        $wp_admin_bar->add_node($args);
        unset($phpbb_user_session);
      }
    } else {
      return false;
    }
  }

  if (!function_exists('wp_hash_password') && !defined("WPW3ALL_NOT_ULINKED")) :

    function wp_hash_password($password)
    {
      $pass = WP_w3all_phpbb::phpBB_password_hash($password);
      return $pass;
    }

  endif;

  if (!function_exists('wp_check_password') && !defined("WPW3ALL_NOT_ULINKED")) :

    function wp_check_password($password, $hash, $user_id)
    {
      global $wpdb, $wp_hasher;

      $password = trim($password);

      if ($user_id < 1) {
        return;
      }

      $is_phpbb_admin = ($user_id == 1) ? 1 : 0; // switch for phpBB admin // 1 admin 0 all others
      //$wpu = get_user_by( 'ID', $user_id );
      $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
      $wpu = $wpdb->get_row("SELECT * FROM $wpu_db_utab WHERE ID = '" . $user_id . "'");
      if (!empty($wpu)) {
        $changed = WP_w3all_phpbb::check_phpbb_passw_match_on_wp_auth($wpu->user_login, $is_phpbb_admin);

        if ($changed !== false) {
          $hash = $changed;
        }

        // If the hash is still md5...
        if (strlen($hash) <= 32) {
          $check = hash_equals($hash, md5($password));
        }

        if (!isset($check) or $check !== true) { // md5 check failed or not fired above ...
          // new style phpass portable hash.
          if (empty($wp_hasher)) {
            require_once(ABSPATH . WPINC . '/class-phpass.php');
            // By default, use the portable hash from phpass
            $wp_hasher = new PasswordHash(8, true);
          }

          $check = $wp_hasher->CheckPassword($password, $hash); // WP check
        }
        //echo $hash;exit;
        // AstoSoft
        if ($check !== true && strlen($hash) > 32 && strlen($password) > 1) { // Wp check failed ... check that isn't an md5 at this point before to follow or get PHP Fatal error in ... addons/bcrypt/bcrypt.php:111
          require_once(WPW3ALL_PLUGIN_DIR . 'addons/bcrypt/bcrypt.php');
          $password = htmlspecialchars($password);
          $ck = new w3_Bcrypt();
          $check = $ck->checkPassword($password, $hash);
        }

        if ($check === true) {
          if ($wpu) {

            $phpBB_user_session_set = WP_w3all_phpbb::phpBB_user_session_set_res($wpu);
            define("PHPBBCOOKIERELEASED", true); // then the session will be set on_login hook, if this filter bypassed
          } else {
            $check = false;
          }
        }

        return apply_filters('check_password', $check, $password, $hash, $user_id);
      } else {
        return apply_filters('check_password', false, $password, $hash, $user_id);
      }
    }

  endif;


  function wp_w3all_remove_bbcode_tags($post_str, $words)
  {

    $post_string = preg_replace('/[[\/\!]*?[^\[\]]*?]/', '', $post_str);

    $post_string = strip_tags($post_string);

    $post_s = $post_string;

    $post_string = explode(' ', $post_string);

    if (count($post_string) < $words) : return $post_s;
    endif;

    $post_std = '';
    $i = 0;
    $b = $words;

    foreach ($post_string as $post_st) {

      $i++;
      if ($i < $b + 1) { // offset of 1

        $post_std .= $post_st . ' ';
      }
    }

    //$post_std = $post_std . ' ...'; // if should be a link to the post, do it on phpbb_last_topics

    return $post_std;
  }

  /////////////////////////   
  // W3ALL WPMS MU START
  /////////////////////////

  function w3all_wpmu_activate_user_phpbb($user_id, $password, $meta)
  {
    global $w3all_config, $w3all_phpbb_user_deactivated_yn;

    $w3db_conn = WP_w3all_phpbb::wp_w3all_phpbb_conn_init();
    $user = get_user_by('id', $user_id);
    $user_info = get_userdata($user->ID);
    $wp_user_role = implode(', ', $user_info->roles);

    $phpbb_user_data = WP_w3all_phpbb::wp_w3all_get_phpbb_user_info($user->user_email);
    $password = WP_w3all_phpbb::phpBB_password_hash($password);
    if ($phpbb_user_data[0]->user_type == 1) {
      $res = $w3db_conn->query("UPDATE " . $w3all_config["table_prefix"] . "users SET user_type = '0', user_password = '" . $password . "' WHERE username = '" . $user->user_login . "'");
    }
  }

  function w3all_wpmu_new_user_up_pass($user_id)
  {

    $wpu  = get_user_by('id', $user_id);
    $phpBB_u_activate = WP_w3all_phpbb::wp_w3all_wp_after_pass_reset_msmu($wpu); // msmu: the pass updated is the one of WP
  }

  function w3all_wpmu_new_user_signup($user, $user_email, $key, $meta)
  {
    $phpBB_user_add = WP_w3all_phpbb::create_phpBB_user_wpms_res($user, $user_email, $key, $meta);
  }

  function w3all_wpmu_validate_user_signup($result)
  {

    $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($result['user_name'], $result['user_email'], 0);
    if ($wp_w3_ck_phpbb_ue_exist === true) {
      temp_wp_w3_error_on_update();
      exit;
    }

    return $result;
  }

  function w3all_wpmu_delete_user($id)
  {
    global $wpdb;
    WP_w3all_phpbb::wp_w3all_phpbb_delete_user_signup($id);
    // for compatibility, this delete will remove user from wp signup table also  
  }

  function w3all_after_signup_site($domain, $path, $title, $user, $user_email, $key, $meta)
  {
    $phpBB_user_add = WP_w3all_phpbb::create_phpBB_user_wpms_res($user, $user_email, $key, $meta);
  }

  function w3all_wpmu_new_blog($data)
  {
    $user = wp_get_current_user();
    $phpBB_u_activate = WP_w3all_phpbb::wp_w3all_wp_after_pass_reset_msmu($user);
  }

  function w3all_wpmu_new_blog_by_admin($data)
  {
    $user = wp_get_current_user();
    $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($user->user_login, $user->user_email, 1);

    if ($wp_w3_ck_phpbb_ue_exist === false) { // could be added a site for an existent user
      // pass $key as string 'is_admin_action' to switch
      $phpBB_user_add = WP_w3all_phpbb::create_phpBB_user_wpms_res($user_id, $user->user_email, $key = 'is_admin_action', $meta);
    }
  }

  function w3all_pre_check_phpbb_u()
  {
    //msmu fix
    if (isset($_POST['newuser']) or isset($_POST['add-user'])) {
      if (isset($_POST['id'])) {
        $user = get_user_by('ID', intval($_POST['id']));
      } elseif (isset($_POST['newuser'])) {
        $user = get_user_by('login', intval($_POST['newuser']));
      }
      // since this is an (existent in wp) user added via mums to a blog, do not follow, the user exist and should be not necessary to check email if it exist in phpBB
      if (!empty($user)) {
        return;
      }
    }

    if (isset($_POST['add-user']) && current_user_can('create_users')) {
      $username = sanitize_user($_POST['user']['username'], $strict = false);
      $email = sanitize_email($_POST['user']['email']);
      $wp_w3_ck_phpbb_ue_exist = WP_w3all_phpbb::phpBB_user_check($username, $email, 1);
      if ($wp_w3_ck_phpbb_ue_exist === true) {
        temp_wp_w3_error_on_update();
        exit;
      }
    }
  }

  if (is_multisite() && !defined("WPW3ALL_NOT_ULINKED")) {
    // admin

    if (defined('WP_ADMIN')) {
      add_action('init',  'w3all_pre_check_phpbb_u');
    }
    add_action('init', 'w3all_network_admin_actions');
    function w3all_network_admin_actions()
    {
      if (defined('WP_ADMIN') && current_user_can('create_users')) {
        add_action('wp_insert_site', 'w3all_wpmu_new_blog_by_admin', 10, 6);
      }
    }
    // user with site registration
    add_action('wp_insert_site', 'w3all_wpmu_new_blog', 10, 6);
    add_action('after_signup_site', 'w3all_after_signup_site', 10, 7);
    add_filter('wpmu_validate_user_signup', 'w3all_wpmu_validate_user_signup', 10, 1);
    // no site user registration
    add_action('wpmu_delete_user', 'w3all_wpmu_delete_user', 10, 1);
    add_action('after_signup_user', 'w3all_wpmu_new_user_signup', 10, 4); // see wp_w3all_phpbb_registration_save more above about this
    add_action('wpmu_activate_user', 'w3all_wpmu_activate_user_phpbb', 10, 3);
    add_action('wpmu_new_user', 'w3all_wpmu_new_user_up_pass', 10, 1);
  }

  /////////////////////////   
  // W3ALL WPMS MU END
  /////////////////////////

  /////////////////////////////////////
  // BUDDYPRESS profile fields and avatars integration START
  /////////////////////////////////////

  // This is UPDATE when it is done into WP side profile
  // while fields UPDATE - phpBB -> WP - is done into class.wp.w3all-phpbb.php -> function verify_phpbb_credentials()

  if (function_exists('buddypress')) { // IF Buddypress installed ...

    // as explained on procedure, these four (4) arrays can be populated with more and different values 
    // https://www.axew3.com/w3/2017/09/wordpress-and-buddypress-phpbb-profile-fields-integration/
    // so check that they not have been added already into phpBB root config.php or custom WP_w3all config.php file

    if (!isset($w3_bpl_profile_occupation) or !is_array($w3_bpl_profile_occupation)) {
      $w3_bpl_profile_occupation = array(
        "en" => "occupation",
        "it" => "occupazione",
        "fr" => "occupation",
        "de" => "occupation",
        "nl" => "bezetting",
        "es" => "ocupacion"
      );
    }

    if (!isset($w3_bpl_profile_location) or !is_array($w3_bpl_profile_location)) {
      $w3_bpl_profile_location = array(
        "en" => "location",
        "it" => "locazione",
        "fr" => "emplacement",
        "de" => "lage",
        "nl" => "plaats",
        "es" => "location"
      );
    }

    if (!isset($w3_bpl_profile_interests) or !is_array($w3_bpl_profile_interests)) {
      $w3_bpl_profile_interests = array(
        "en" => "interests",
        "it" => "interessi",
        "fr" => "interets",
        "de" => "interest",
        "nl" => "belangen",
        "es" => "intereses"
      );
    }

    if (!isset($w3_bpl_profile_website) or !is_array($w3_bpl_profile_website)) {
      $w3_bpl_profile_website = array(
        "en" => "website",
        "it" => "sito web",
        "fr" => "site web",
        "de" => "website",
        "nl" => "website",
        "es" => "sitio web"
      );
    }

    // The check about email/url is done in another way, so this aim to update/check only (existent and that match!) profile fields
    function w3all_xprofile_updated_profile($user_id, $posted_field_ids, $errors, $old_values, $new_values)
    {
      global $wpdb, $w3all_config;

      if (!empty($errors)) {
        return;
      }

      /*
$uf = '';	   
	foreach($posted_field_ids as $f){   
   $uf .= "'".$f."',";
 }
$uf = substr($uf, 0, -1);

 $uf = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bp_xprofile_data, ".$wpdb->prefix ."bp_xprofile_fields, ".$wpdb->prefix ."usermeta 
  WHERE ".$wpdb->prefix."bp_xprofile_data.user_id = $user_id 
   AND ".$wpdb->prefix."bp_xprofile_data.field_id = ".$wpdb->prefix."bp_xprofile_fields.id 
   AND ".$wpdb->prefix."bp_xprofile_data.field_id IN(".$uf.") 
   AND ".$wpdb->prefix."usermeta.user_id = ".$wpdb->prefix."bp_xprofile_data.user_id  
   AND ".$wpdb->prefix ."usermeta.meta_key = 'bp_xprofile_visibility_levels'");
*/

      $uf = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bp_xprofile_data, " . $wpdb->prefix . "bp_xprofile_fields, " . $wpdb->prefix . "usermeta 
    WHERE " . $wpdb->prefix . "bp_xprofile_data.user_id = $user_id 
    AND " . $wpdb->prefix . "bp_xprofile_data.field_id = " . $wpdb->prefix . "bp_xprofile_fields.id 
    AND " . $wpdb->prefix . "usermeta.user_id = " . $wpdb->prefix . "bp_xprofile_data.user_id  
    AND " . $wpdb->prefix . "usermeta.meta_key = 'bp_xprofile_visibility_levels'");

      if (empty($uf)) {
        return;
      }

      $meta_val = unserialize($uf[0]->meta_value);
      global $w3_bpl_profile_occupation, $w3_bpl_profile_location, $w3_bpl_profile_interests, $w3_bpl_profile_website;
      // init vars as empty x phpBB (default) profile values
      $w3_youtube = '';
      $w3_googleplus = '';
      $w3_skype = '';
      $w3_twitter = '';
      $w3_facebook = '';
      $w3_yahoo = '';
      $w3_icq = '';
      $w3_aol = '';
      $w3_interests = '';
      $w3_occupation = '';
      $w3_location = '';
      $w3_website = '';
      $w3_wlm = ''; // windows live messenger, (default field in phpBB 3.1) is not considered here, will be ever empty for the update query: change if needed, adding on foreach 'if' condition, and so on the phpBB UPDATE query

      foreach ($uf as $uu => $ff) { // check what of these are public fields and assign update values, ignore all the rest
        // update cases
        // Buddypress can return, membersonly, public, adminsonly
        // so, leave the assigned empty var above (so reset in phpBB to empty value) if the field is not set as Public by the user in Buddypress profile

        if (stripos($ff->name, 'youtube') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_youtube = $ff->value;
        } elseif (stripos($ff->name, 'google') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_googleplus = $ff->value;
        } elseif (stripos($ff->name, 'skype') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_skype = $ff->value;
        } elseif (stripos($ff->name, 'twitter') !== false  && $meta_val[$ff->field_id] == 'public') {
          $w3_twitter = $ff->value;
        } elseif (stripos($ff->name, 'facebook') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_facebook = $ff->value;
        } elseif (stripos($ff->name, 'yahoo') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_yahoo = $ff->value;
        } elseif (stripos($ff->name, 'icq') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_icq = $ff->value;
        } elseif (stripos($ff->name, 'aol') !== false && $meta_val[$ff->field_id] == 'public') {
          $w3_aol = $ff->value;
        } elseif (array_search(trim(strtolower($ff->name)), $w3_bpl_profile_interests) && $meta_val[$ff->field_id] == 'public') {
          $w3_interests = $ff->value;
        } elseif (array_search(trim(strtolower($ff->name)), $w3_bpl_profile_occupation) && $meta_val[$ff->field_id] == 'public') {
          $w3_occupation = $ff->value;
        } elseif (array_search(trim(strtolower($ff->name)), $w3_bpl_profile_location) && $meta_val[$ff->field_id] == 'public') {
          $w3_location = $ff->value;
        } elseif (array_search(trim(strtolower($ff->name)), $w3_bpl_profile_website) && $meta_val[$ff->field_id] == 'public') {
          $w3_website = $ff->value;
        } else {
        }
      }

      $phpbb_config_file = $w3all_config;
      $w3phpbb_conn = WP_w3all_phpbb::wp_w3all_phpbb_conn_init();
      $phpbb_config = unserialize(W3PHPBBCONFIG);
      if (defined('W3PHPBBUSESSION')) {
        $us = unserialize(W3PHPBBUSESSION);
        $uid = $us[0]->user_id;
      } else {
        return;
      }

      $wpu = get_user_by('ID', $user_id);

      if (!$wpu) {
        return;
      }

      // is this an admin updating/editing this user profile?
      // if current_user_can( 'manage_options' ) here, but add (role) in case if some other group on WP allow powered users to edit others users profiles
      // OR updating the profile of another user, the 'logged' WP user executing the update will be updated in phpBB, and not the needed passed $user_id ...

      if (current_user_can('manage_options') && $wpu->user_email != $us[0]->user_email) {
        $uid = $w3phpbb_conn->get_var("SELECT user_id FROM " . $phpbb_config_file["table_prefix"] . "users WHERE username = '$wpu->user_login'");
        if ($uid < 2) {
          return;
        }
      }

      $phpbb_version = substr($phpbb_config["version"], 0, 3);

      // phpBB version 3.2>
      if ($phpbb_version == '3.2') {
        $w3phpbb_conn->query("INSERT INTO " . $phpbb_config_file["table_prefix"] . "profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_location, pf_phpbb_youtube, pf_phpbb_twitter, pf_phpbb_googleplus, pf_phpbb_skype, pf_phpbb_facebook, pf_phpbb_icq, pf_phpbb_website, pf_phpbb_yahoo, pf_phpbb_aol)
        VALUES ('$uid','$w3_interests','$w3_occupation','$w3_location','$w3_youtube','$w3_twitter','$w3_googleplus','$w3_skype','$w3_facebook','$w3_icq','$w3_website','$w3_yahoo','$w3_aol') ON DUPLICATE KEY UPDATE 
         pf_phpbb_interests = '$w3_interests', pf_phpbb_occupation = '$w3_occupation', pf_phpbb_location = '$w3_location', pf_phpbb_youtube = '$w3_youtube', pf_phpbb_twitter = '$w3_twitter', pf_phpbb_googleplus = '$w3_googleplus', pf_phpbb_skype = '$w3_skype', pf_phpbb_facebook = '$w3_facebook', pf_phpbb_icq = '$w3_icq', pf_phpbb_website = '$w3_website', pf_phpbb_yahoo = '$w3_yahoo', pf_phpbb_aol = '$w3_aol'");
      } else { // phpbb 3.1<
        // note that WLM (windows live messenger) is default field only on phpBB 3.1< but it has been not considered and is ever empty here ... change if needed
        $w3phpbb_conn->query("INSERT INTO " . $phpbb_config_file["table_prefix"] . "profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_facebook, pf_phpbb_googleplus, pf_phpbb_icq, pf_phpbb_location, pf_phpbb_skype, pf_phpbb_twitter, pf_phpbb_website, pf_phpbb_wlm, pf_phpbb_yahoo, pf_phpbb_youtube, pf_phpbb_aol)
         VALUES ('$uid','$w3_interests','$w3_occupation','$w3_facebook','$w3_googleplus','$w3_icq','$w3_location','$w3_skype','$w3_twitter','$w3_website','','$w3_yahoo','$w3_youtube','$w3_aol') ON DUPLICATE KEY UPDATE 
           pf_phpbb_interests = '$w3_interests', pf_phpbb_occupation = '$w3_occupation', pf_phpbb_facebook = '$w3_facebook', pf_phpbb_googleplus = '$w3_googleplus', pf_phpbb_icq = '$w3_icq', pf_phpbb_location = '$w3_location', pf_phpbb_skype = '$w3_skype', pf_phpbb_twitter = '$w3_twitter', pf_phpbb_website = '$w3_website', pf_phpbb_wlm = '', pf_phpbb_yahoo = '$w3_yahoo', pf_phpbb_youtube = '$w3_youtube', pf_phpbb_aol = '$w3_aol'");
      }
    }

    // ... custom avatar URL for users, remote avatar need to be enabled in phpBB for this to work ...
    function w3all_xprofile_avatar_uploaded($item_id, $avatar_data_type,  $avatar_data)
    {
      global $w3all_config;
      $args = array('item_id' => $item_id, 'html' => false);
      $avaUrl = bp_core_fetch_avatar($args);
      // extract the img url in old way not working anymore
      //preg_match('~.*?[src=]"(.*?)".*?~i', bp_core_fetch_avatar($args), $matches, PREG_OFFSET_CAPTURE);
      //if(isset($matches[1][0])){ 
      //$wp_ava_url = $matches[1][0];

      if (!empty($avaUrl)) {
        $wpu = get_user_by('ID', $item_id);
        $w3db_conn = WP_w3all_phpbb::wp_w3all_phpbb_conn_init();
        $res = $w3db_conn->query("UPDATE " . $w3all_config["table_prefix"] . "users SET user_avatar = '" . $avaUrl . "', user_avatar_type = 'avatar.driver.remote' WHERE username = '" . $wpu->user_login . "'");
      }
    }

    // hint: when avatar deletion, should be instead set in phpBB as Gravatar like WP/BP do?
    function w3all_bp_avatar_phpbb_delete($args)
    {
      global $w3all_config;

      $wpu = get_user_by('ID', $args['item_id']);
      $wpu_eh = WP_w3all_phpbb::w3all_phpbb_email_hash($wpu->user_email);
      $w3db_conn = WP_w3all_phpbb::wp_w3all_phpbb_conn_init();
      $phpbb_config_file = $w3all_config;
      $res = $w3db_conn->query("UPDATE " . $phpbb_config_file["table_prefix"] . "users SET user_avatar = '', user_avatar_type = '' WHERE username = '" . $wpu->user_login . "'");
    }

    if (!defined("WPW3ALL_NOT_ULINKED")) {
      if ($w3all_profile_sync_bp_yn == 1) {
        add_action('xprofile_updated_profile', 'w3all_xprofile_updated_profile', 10, 5);
      }
      if ($w3all_get_phpbb_avatar_yn == 1 && $w3all_avatar_replace_bp_yn == 1) {
        add_action('xprofile_avatar_uploaded', 'w3all_xprofile_avatar_uploaded', 10, 3);
        add_action('bp_core_delete_existing_avatar', 'w3all_bp_avatar_phpbb_delete', 10, 1);
        add_filter('bp_core_fetch_avatar', array('WP_w3all_phpbb', 'w3all_bp_core_fetch_avatar'), 10, 9);
      }
    }
  } // END - IF Buddypress installed 

  ///////////////////////////////////
  // BUDDYPRESS profile fields and avatars END
  ///////////////////////////////////


} // END   if ( defined('PHPBB_INSTALLED') ){ // 2nd //

// This is for feed shortcode param 'w3feed_text_words' and may valid only for a phpBB feed
// If this param passed, and is a phpBB feed, as on 3.2.5 the feed content return
// last part containing 'Statistics:' text - the follow grab and reassign 
// statistics on bottom of the item, removing 'Statistics:'
function wp_w3all_R_num_of_words_parse($post_str, $words)
{
  $pos0 = strpos($post_str, "<p>");
  if (preg_match('/(.+)(<p>.?Statistics:(.+)<\/p>)/', $post_str, $str_post_data) > 0) {

    if (isset($str_post_data[1])) {
      $pcontent = '<p>' . trim($str_post_data[1]) . ' </p>';
    }
    if (isset($str_post_data[3])) {
      $pinfo = '<p>' . $str_post_data[3] . '</p>';
    }
  } else {
    $pinfo = '';
    $pcontent = $post_str;
  }

  $post_string = explode(' ', $pcontent);

  if (count($post_string) > $words) {

    $post_std = '';
    $i = 0;
    $b = $words;

    foreach ($post_string as $post_st) {

      $i++;
      if ($i < $b + 1) { // offset of 1

        $post_std .= $post_st . ' ';
      }
    }

    $post_std .= ' ...';
  } else {
    $post_std = $pcontent;
  }

  $post_std .= $pinfo;

  return $post_std;
}

if (!is_admin()) {
  require_once(WPW3ALL_PLUGIN_DIR . 'class.wp.w3all-phpbb.php');
  add_shortcode('w3allfeed', array('WP_w3all_phpbb', 'wp_w3all_feeds_short'));
}

// WP_w3all - this extract ever the correct cookie domain (except for sub hosted/domains like: mydomain.my-hostingService-domain.com)
// no more used since 1.9.0
function w3all_extract_cookie_domain($w3cookie_domain)
{

  require_once(WPW3ALL_PLUGIN_DIR . 'addons/w3_icann_domains.php');

  $count_dot = substr_count($w3cookie_domain, ".");

  if ($count_dot >= 3) {
    preg_match('/.*(\.)([-a-z0-9]+)(\.[-a-z0-9]+)(\.[a-z]+)/', $w3cookie_domain, $w3m0, PREG_OFFSET_CAPTURE);
    $w3cookie_domain = $w3m0[2][0] . $w3m0[3][0] . $w3m0[4][0];
  }

  $ckcd = explode('.', $w3cookie_domain);

  if (!in_array('.' . $ckcd[1], $w3all_domains)) {
    $w3cookie_domain = preg_replace('/^[^\.]*\.([^\.]*)\.(.*)$/', '\1.\2', $w3cookie_domain);
  }

  $w3cookie_domain = '.' . $w3cookie_domain;

  $pos = strpos($w3cookie_domain, '.');
  if ($pos != 0) {
    $w3cookie_domain = '.' . $w3cookie_domain;
  }

  return $w3cookie_domain;
}

function w3all_add_phpbb_user()
{
  if (isset($_GET["w3insu"])) {
    $uw = base64_decode(trim($_GET["w3insu"]));
  } else {
    return;
  }

  $phpBB_un = sanitize_user($uw, $strict = false);

  if (empty($phpBB_un) or strlen($phpBB_un) > 50) {
    echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system, or your username is too long (max 49 chars allowed)</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>' . $phpbb_user_session[0]->username . '</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
    return;
  }

  //if ( preg_match('/[^-0-9A-Za-z _.@]/',$phpBB_un) ){
  //       echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>'.$phpbb_user_session[0]->username.'</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
  //     return;
  // }  

  $user = get_user_by('login', $phpBB_un);

  if ($user === false) {

    $phpbb_user = WP_w3all_phpbb::wp_w3all_get_phpbb_user_info($phpBB_un);

    if (empty($phpbb_user) or !$phpbb_user) {
      return;
    }

    $phpbb_user[0]->username = sanitize_user($phpbb_user[0]->username, $strict = false);

    if ($phpbb_user[0]->group_name == 'ADMINISTRATORS') {
      $role = 'administrator';
    } elseif ($phpbb_user[0]->group_name == 'GLOBAL_MODERATORS') {
      $role = 'editor';
    } else {
      $role = 'subscriber';
    }  // for all others phpBB Groups default to WP subscriber

    $userdata = array(
      'user_login'       =>  $phpbb_user[0]->username,
      'user_pass'        =>  $phpbb_user[0]->user_password,
      'user_email'       =>  $phpbb_user[0]->user_email,
      'user_registered'  =>  date_i18n('Y-m-d H:i:s', $phpbb_user[0]->user_regdate),
      'role'             =>  $role
    );

    $user_id = wp_insert_user($userdata);

    if (isset($_GET["w3rtb"])) {
      $br = base64_decode(trim($_GET["w3rtb"]));
      header("Location: $br"); /* Redirect to phpBB after this onfly wp user insertion */
      exit;
    }
  }
}
