<?php
class WP_w3all_phpbb {
	
// lost on the way

 // protected $config = '';
 // protected $w3db_conn = '';
 // protected $phpbb_config = '';
 // protected $phpbb_user_session = '';
  
public static function wp_w3all_phpbb_init() {
	
	global $w3all_get_phpbb_avatar_yn;

	if ( ! defined("WPW3ALL_NOT_ULINKED") ):
		self::verify_phpbb_credentials();
	endif;
	
 if ( $w3all_get_phpbb_avatar_yn == 1 ): 
    self::init_w3all_avatars(); 
 endif;
      	
}
	
private static function w3all_wp_logout(){
	   		global $w3all_config,$useragent;
	  	$phpbb_config = W3PHPBBCONFIG;
	  	$phpbb_config = unserialize($phpbb_config);
	    $w3cookie_domain = get_option('w3all_phpbb_cookie');
	  	$phpbb_config_file = $w3all_config;
	  	$w3phpbb_conn = self::w3all_db_connect();
	     	
        $k   = $phpbb_config["cookie_name"].'_k';
        $sid = $phpbb_config["cookie_name"].'_sid';
        $u   = $phpbb_config["cookie_name"].'_u';
         
   if( isset($_COOKIE[$k]) OR isset($_COOKIE[$sid]) ){
   	
   $k_md5 = isset($_COOKIE[$k]) ? md5($_COOKIE[$k]) : '';
 	 $u_id =  isset($_COOKIE[$u]) ? $_COOKIE[$u] : '';
 	 $s_id =  isset($_COOKIE[$sid]) ? $_COOKIE[$sid] : '';
        	
         if ( preg_match('/[^0-9A-Za-z]/',$k_md5) OR preg_match('/[^0-9A-Za-z]/',$s_id) OR preg_match('/[^0-9]/',$u_id) ){
 	           	 die( "Please clean up cookies on your browser." );
 	            }     
 	if( !empty($s_id) && !empty($u_id) ){
   $w3phpbb_conn->query("DELETE FROM ".$phpbb_config_file["table_prefix"]."sessions WHERE session_id = '$s_id' AND session_user_id = '$u_id' OR session_user_id = '$u_id' AND session_browser = '$useragent'");
  }
  if( !empty($k_md5) && !empty($u_id) ){
   $w3phpbb_conn->query("DELETE FROM ".$phpbb_config_file["table_prefix"]."sessions_keys WHERE key_id = '$k_md5' AND user_id = '$u_id'");
  }
   	// remove phpBB cookies 
 	    setcookie ("$k", "", time() - 31622400, "/");
 	    setcookie ("$sid", "", time() - 31622400, "/"); 
 	    setcookie ("$u", "", time() - 31622400, "/"); 
 	    setcookie ("$k", "", time() - 31622400, "/", "$w3cookie_domain");
 	    setcookie ("$sid", "", time() - 31622400, "/", "$w3cookie_domain"); 
 	    setcookie ("$u", "", time() - 31622400, "/", "$w3cookie_domain"); 
   }
   
   unset($phpbb_user_session);
	  
	  wp_logout();

    wp_redirect( home_url() ); exit;
  
 }

private static function w3all_db_connect(){

 global $w3all_config;
 // check that the connection do not require specified db port
 // @mrmoh https://wordpress.org/support/users/mrmoh/
 $w3all_config["dbhost"] = empty($w3all_config["dbport"]) ? $w3all_config["dbhost"] : $w3all_config["dbhost"] . ':' . $w3all_config["dbport"];
 $w3db_conn = new wpdb($w3all_config["dbuser"], $w3all_config["dbpasswd"], $w3all_config["dbname"], $w3all_config["dbhost"]);
  if(!empty($w3db_conn->error)){
  	if (!defined('WPW3ALL_NOT_ULINKED')){
  	  define('WPW3ALL_NOT_ULINKED', true);
  	}
  	  if($_GET['page'] == 'wp-w3all-options'){
       echo __('<div class="" style="width:auto;background-color:#FFF;position:fixed;top:50;right:0;left:0;text-align:center;z-index:99999999;padding:20px"><h3 style="margin:0 10px 10px 10px"><span style="color:#FF0000;">WARNING</span></h3><strong>Error establishing a phpBB database connection.</strong><br />The w3all integration plugin will not work properly (widgets, shortcodes).<br /><span style="color:#FF0000">Integration Running as USERS NOT LINKED</span> until this message display.<br />Check db connection values into linked phpBB config.php file.</div><br />', 'wp-w3all-phpbb-integration');
      }
   }
 return $w3db_conn;
}

private static function w3all_get_phpbb_config(){
	
	 global $w3all_config, $w3cookie_domain;
    $w3db_conn = self::w3all_db_connect();

   $a = $w3db_conn->get_results("SELECT * FROM ". $w3all_config["table_prefix"] ."config WHERE config_name IN('allow_autologin','avatar_gallery_path','avatar_path','avatar_salt','cookie_domain','cookie_name','default_dateformat','default_lang','max_autologin_time','rand_seed','rand_seed_last_update','script_path','session_length','version') ORDER BY config_name ASC");

      // Order is alphabetical 
      $res = array( 'allow_autologin' => $a[0]->config_value,
                    'avatar_gallery_path'     => $a[1]->config_value,
                    'avatar_path'     => $a[2]->config_value,
                    'avatar_salt'     => $a[3]->config_value,
                    'cookie_domain'   => $a[4]->config_value,
                    'cookie_name'     => $a[5]->config_value, 
                    'default_dateformat'      => $a[6]->config_value,
                    'default_lang'    => $a[7]->config_value,
                    'max_autologin_time'      => $a[8]->config_value,
                    'rand_seed'               => $a[9]->config_value,
                    'rand_seed_last_update'   => $a[10]->config_value,
                    'script_path'     => $a[11]->config_value,
                    'session_length'  => $a[12]->config_value,
                    'version'  => $a[13]->config_value
                  );
                  
      if( empty($res) OR ! $res ){
      	return;
      }

if( $res["cookie_domain"] != $w3cookie_domain ){
	$up = $res["cookie_domain"];
	update_option( 'w3all_phpbb_cookie', $up );
}
   
// unlink wp uid1 and phpBB uid2 install admins if needed
if(get_option('w3all_exclude_id1') == 1){
 $current_user = wp_get_current_user();
 $u = $res["cookie_name"].'_u';
if ( $current_user->ID == 1 && !defined("WPW3ALL_NOT_ULINKED") OR isset($_COOKIE[$u]) && $_COOKIE[$u] == 2 && !defined("WPW3ALL_NOT_ULINKED") ) {
 define('WPW3ALL_NOT_ULINKED', true);
}
}   
                  
    $res_d = serialize($res); // to pass array into define prior php7
    define( "W3PHPBBCONFIG", $res_d );
	       
	return $res;
}

private static function verify_phpbb_credentials(){
           global $w3all_config, $wpdb, $w3all_phpbb_lang_switch_yn, $useragent, $w3all_profile_sync_bp_yn, $w3all_phpbb_mchat_get_opt_yn;
           $config = $w3all_config;
        	 $phpbb_config = unserialize(W3PHPBBCONFIG);
        	 $w3db_conn = self::w3all_db_connect();

      if( isset($_GET['action']) && $_GET['action'] == 'logout' ){	
      	self::w3all_wp_logout();
      	return;
      }
 
        	  $k   = $phpbb_config["cookie_name"].'_k';
            $sid = $phpbb_config["cookie_name"].'_sid';
            $u   = $phpbb_config["cookie_name"].'_u';
   
          // HERE INSIDE WE ARE SECURE //
         $_COOKIE[$u] = (isset($_COOKIE[$u])) ? $_COOKIE[$u] : 1;
         $_COOKIE[$sid] = (isset($_COOKIE[$sid])) ? $_COOKIE[$sid] : ''; 
      
 	     if ( $_COOKIE[$u] > 1 ){ // phpBB: uid 1 guest, uid 2 default install admin
 	      
 	        if ( !isset($_COOKIE[$k]) ){ $_COOKIE[$k] = ''; }
 	        	
 	           if ( preg_match('/[^0-9A-Za-z]/',$_COOKIE[$k]) OR preg_match('/[^0-9A-Za-z]/',$_COOKIE[$sid]) OR preg_match('/[^0-9]/',$_COOKIE[$u]) ){
 	           	
                die( "Clean up cookie on your browser please." );
 	            }
 	            
 	           $phpbb_k   = $_COOKIE[$k];
 	           $phpbb_sid = $_COOKIE[$sid];
 	           $phpbb_u   = $_COOKIE[$u];

 	         // group id:  1=anonimous; 5=admin; 6=bots; 
         	 // user_type: 1=not active accounts: confirmation email, deactivated (and i presume coppa?)
    
if ( empty( $phpbb_k ) ){ // it is not a remember login
 	 
  $phpbb_user_session = $w3db_conn->get_results("SELECT *  
    FROM ". $config["table_prefix"] ."users  
    JOIN ". $config["table_prefix"] ."sessions ON ". $config["table_prefix"] ."sessions.session_id =  '".$phpbb_sid."'   
     AND ". $config["table_prefix"] ."sessions.session_user_id = ". $config["table_prefix"] ."users.user_id 
     AND ". $config["table_prefix"] ."sessions.session_user_id = '".$phpbb_u."' 
     AND ". $config["table_prefix"] ."sessions.session_browser = '".$useragent."' 
    JOIN ". $config["table_prefix"] ."groups ON ". $config["table_prefix"] ."groups.group_id = ". $config["table_prefix"] ."users.group_id 
      LEFT JOIN ". $config["table_prefix"] ."profile_fields_data ON ". $config["table_prefix"] ."profile_fields_data.user_id = ". $config["table_prefix"] ."sessions.session_user_id
      LEFT JOIN ". $config["table_prefix"] ."banlist ON ". $config["table_prefix"] ."banlist.ban_userid = ". $config["table_prefix"] ."users.user_id
      GROUP BY ". $config["table_prefix"] ."users.user_id");
   
  } else { // remember me auto login
       	
 $phpbb_user_session = $w3db_conn->get_results("SELECT *  
    FROM ". $config["table_prefix"] ."users  
    JOIN ". $config["table_prefix"] ."sessions_keys ON ". $config["table_prefix"] ."sessions_keys.key_id = '".md5($phpbb_k)."' 
     AND ". $config["table_prefix"] ."users.user_id = ". $config["table_prefix"] ."sessions_keys.user_id 
      LEFT JOIN ". $config["table_prefix"] ."sessions ON ". $config["table_prefix"] ."sessions.session_user_id = ". $config["table_prefix"] ."sessions_keys.user_id 
     AND ". $config["table_prefix"] ."sessions.session_browser = '".$useragent."' 
      LEFT JOIN ". $config["table_prefix"] ."groups ON ". $config["table_prefix"] ."groups.group_id = ". $config["table_prefix"] ."users.group_id 
      LEFT JOIN ". $config["table_prefix"] ."profile_fields_data ON ". $config["table_prefix"] ."profile_fields_data.user_id = ". $config["table_prefix"] ."sessions_keys.user_id
      LEFT JOIN ". $config["table_prefix"] ."banlist ON ". $config["table_prefix"] ."banlist.ban_userid = ". $config["table_prefix"] ."users.user_id
      GROUP BY ". $config["table_prefix"] ."users.user_id");               
  } 
  
  if ( empty( $phpbb_user_session ) OR $phpbb_user_session[0]->user_type == 1 OR $phpbb_user_session == 0 ){
   if ( is_user_logged_in() ) { 
  	 self::w3all_wp_logout();
  	}
  	return;
  } 
  
 // assure this array will contain the user_id
 $phpbb_user_session[0]->user_id = (!empty($phpbb_user_session[0]->user_id)) ? $phpbb_user_session[0]->user_id : $phpbb_user_session[0]->session_user_id;
   // push users acl options into this array
   // i've try to unify this query with the above, but i've not (still) been able to
if($w3all_phpbb_mchat_get_opt_yn > 0){ // actually load this only for mchat
	$phpbb_user_capabilities = array();
   $phpbb_u_aopts = $w3db_conn->get_results("SELECT * FROM ".$config["table_prefix"]."acl_options 
    JOIN ".$config["table_prefix"]."acl_groups ON ". $config["table_prefix"] ."acl_groups.auth_option_id = ". $config["table_prefix"] ."acl_options.auth_option_id  
    AND ". $config["table_prefix"] ."acl_groups.group_id = ". $phpbb_user_session[0]->group_id .""); 
       // push into [1] array index due to GROUP BY above (so ever [0] to not broke all things)
  if (!empty($phpbb_u_aopts)){
   $phpbb_u_opts = array_column($phpbb_u_aopts, 'auth_option');
   if (in_array("u_mchat_view", $phpbb_u_opts)) { // check all about this user, adding 'auth_option' name check in_array here
    $phpbb_user_capabilities[] = "u_mchat_view";
   }
 // $phpbb_user_capabilities = serialize($phpbb_user_capabilities); 
 // define("W3PHPBBUSERCAPABILITIES", $phpbb_user_capabilities);
 } 
} 	      
  // push on index [1] phpBB users capabilities	
  if(isset($phpbb_user_capabilities)){    
   $phpbb_user_session[] = $phpbb_user_capabilities;
  } 
   	      
   $w3_phpbb_user_session = serialize($phpbb_user_session);
   	define("W3PHPBBUSESSION", $w3_phpbb_user_session);

  if( $phpbb_user_session[0]->user_id == 2 ) { 
  	$wp_user_data = get_user_by( 'ID', 1 );
  } else {
     $wp_user_data = get_user_by( 'login', $phpbb_user_session[0]->username );
    }
    
    // if needed, set inline this user: if user logout in phpBB (but still have valid cookie in WP) and relogin using another uname in phpBB, then come in WP, set it inline or admin bar will display previous user name until next page reload
     $ck1 = wp_get_current_user();
    
    if ( $ck1->ID > 1 && $ck1->user_login != $wp_user_data->user_login ){
     wp_clear_auth_cookie();
     wp_set_current_user( $wp_user_data->ID, $wp_user_data->user_login );
    }

    // some lang may differ about notation on both phpBB and WP ... so this may sometime will be necessary to adjust ...
    // for example: phpBB Persian lang is 'fa' while in WP  could be 'ps'. Check also that could be also the contrary
    if( $phpbb_user_session[0]->user_lang == 'fa' ){ $phpbb_user_session[0]->user_lang = 'ps'; }
      
// check that this user isn't banned in phpBB: the check on banlist table is related only userid here due to the above query
         // BUT should be retrieved any userid 0 (zero) on the banlist table, that contain all banned email addresses and IPs list
         // this is done onlogin and onregister, while at moment isn't done here. May it could be added into w3all_get_phpbb_config() query so we'll have available here, the needed list to check against. 
         // OR in this way as is now, until an user isn't logged out in WP and return back with valid WP cookie but is banned by email or IP only in phpBB, until will not visit phpBB, will still result logged in WP side.   
       // The check about IP and email banned is instead done on WP onregister and onlogin
       // set this WP user as norole
        if( $phpbb_user_session[0]->ban_userid > 2 && $phpbb_user_session[0]->group_name != 'ADMINISTRATORS' ){
        	  $wp_user_data = get_user_by( 'login', $phpbb_user_session[0]->username );
        	  $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'usermeta' : $wpdb->prefix . 'usermeta';
	          $wpdb->query("UPDATE $wpu_db_utab SET meta_value = 'a:0:{}' WHERE user_id = '$wp_user_data->ID' AND meta_key = 'wp_capabilities'");
        	   self::w3all_wp_logout();
        } 
    
 if ( is_user_logged_in() ) { 

   		$current_user = wp_get_current_user();
      $wp_umeta = get_user_meta($current_user->ID, '', false);

// #####################################################
// START ONLY if BUDDYPRESS profile integration enabled

 if ( $w3all_profile_sync_bp_yn == 1 ){   
	
   global $w3_bpl_profile_occupation, $w3_bpl_profile_location, $w3_bpl_profile_interests, $w3_bpl_profile_website;
   
   // i've not find out any way to get BP profile data for the user at this point using Buddypress core functions ... 
   // if anybody know how to get these data for the user, without the following query, would be really great!
   // i've not follow check and this have not help: https://codex.buddypress.org/developer/loops-reference/the-profile-fields-loop-bp_has_profile/  
   // thus until no light about, next two WP queries ...
   
   // Any help on improve this would be very appreciated!
   // Should be done by ID, but how without resetting existing installations configurations about profile fields?
   // May it was less complicate, in certain conditions, but not suitable for all. 
   // So here the joke about DELETE or UPDATE or INSERT, and full integration of BP profile fields in phpBB profile fields, where fields names match as explained on procedure:
   //https://www.axew3.com/w3/2017/09/wordpress-and-buddypress-phpbb-profile-fields-integration/
   
   $bp_uf = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bp_xprofile_data, ".$wpdb->prefix ."bp_xprofile_fields, ".$wpdb->prefix ."usermeta 
     WHERE ".$wpdb->prefix."bp_xprofile_data.user_id = $current_user->ID 
    AND ".$wpdb->prefix."bp_xprofile_data.field_id = ".$wpdb->prefix."bp_xprofile_fields.id 
    AND ".$wpdb->prefix."usermeta.user_id = ".$wpdb->prefix."bp_xprofile_data.user_id  
    AND ".$wpdb->prefix ."usermeta.meta_key = 'bp_xprofile_visibility_levels'");

   $bp_f = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bp_groups_members, ".$wpdb->prefix."bp_xprofile_fields
     WHERE ".$wpdb->prefix."bp_groups_members.user_id = $current_user->ID
    AND ".$wpdb->prefix."bp_xprofile_fields.group_id = ".$wpdb->prefix."bp_groups_members.group_id");
 
   $db_bp_pf_data = $wpdb->prefix . 'bp_xprofile_data'; 

  if(!empty($bp_uf)){

   // any empty phpBB field that match the name field in BP, will be updated if not empty in phpBB
   // if empty in phpBB, will be deleted (as BP do) in BP xprofile_data table

   	foreach( $bp_uf as $uu => $ff ):
   	
   	// remove from this array containing all BP fields of this user, all values that have been passed on this foreach
   	// so we'll have values to INSERT, in case, if not UPDATED on the follow: no UPDATE, may because the field could be not existent on table: it is removed by BP on xprofile_data table (on update action) when a field is empty)
   	
   	  foreach( $bp_f as $u => $f ):
   	   if($ff->field_id == $f->id){
   	  	 unset($bp_f[$u]);
   	  	}
   	  
   	  endforeach;
 
 // UPDATE 'existent' WP recognized fields AND grab what need to be deleted (because empty field in phpBB)
     if ( stripos($ff->name, 'youtube' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_youtube ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_youtube) ){
        $youtube_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_youtube."'";
        $do_up = true;
       } else { $del = true; $del_youtube = $ff->field_id; }
       	
      } elseif ( stripos($ff->name, 'google' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_googleplus ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_googleplus) ){
        $googleplus_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_googleplus."'";
        $do_up = true;
      } else { $del = true; $del_googleplus = $ff->field_id; }
      	
      } elseif  ( stripos($ff->name, 'skype' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_skype ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_skype) ){
        $skype_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_skype."'";
        $do_up = true;
       } else { $del = true; $del_skype = $ff->field_id; }
      	
      } elseif  ( stripos($ff->name, 'twitter' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_twitter ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_twitter) ){
        $twitter_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_twitter."'";
        $do_up = true;
       } else { $del = true; $del_twitter = $ff->field_id; }
       	
      } elseif ( stripos($ff->name, 'facebook' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_facebook ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_facebook) ){
       $facebook_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_facebook."'";
       $do_up = true;
       } else { $del = true; $del_facebook = $ff->field_id; }
       	
      } elseif ( stripos($ff->name, 'yahoo' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_yahoo ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_yahoo) ){
       $yahoo_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_yahoo."'";
       $do_up = true;
      } else { $del = true; $del_yahoo = $ff->field_id; }
      
      } elseif ( stripos($ff->name, 'icq' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_icq ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_icq) ){
       $icq_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_icq."'";
       $do_up = true;
      } else { $del = true; $del_icq = $ff->field_id; }
       
      } elseif ( stripos($ff->name, 'aol' ) && $ff->value != $phpbb_user_session[0]->pf_phpbb_aol ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_aol) ){
       $aol_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_aol."'";
       $do_up = true;
      } else { $del = true; $del_aol = $ff->field_id; }
      
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_interests ) && $phpbb_user_session[0]->pf_phpbb_interests != $ff->value ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_interests) ){
       $interests_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_interests."'";
       $do_up = true;
      } else { $del = true; $del_interests = $ff->field_id; }
      	
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_occupation ) && $phpbb_user_session[0]->pf_phpbb_occupation != $ff->value ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_occupation) ){
       $occupation_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_occupation."'";
       $do_up = true;
      } else { $del = true; $del_occupation = $ff->field_id; }
      	
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_location ) && $phpbb_user_session[0]->pf_phpbb_location != $ff->value ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_location) ){
       $location_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_location."'";
       $do_up = true;
      } else { $del = true; $del_location = $ff->field_id; }
      	
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_website ) && $phpbb_user_session[0]->pf_phpbb_website != $ff->value ){
      if( !empty($phpbb_user_session[0]->pf_phpbb_website) ){
       $website_up = "WHEN '".$ff->field_id."' THEN '".$phpbb_user_session[0]->pf_phpbb_website."'";
       $do_up = true;
      } else { $del = true; $del_website = $ff->field_id; }
      	 
      } else { // nothing at moment 
      	}  
   
   	endforeach;
   	
} // end if(!empty($uf)){

// update 
 if( isset($do_up) ){
         
  $youtube_up = isset($youtube_up) ? $youtube_up : '';
  $googleplus_up = isset($googleplus_up) ? $googleplus_up : '';
  $skype_up = isset($skype_up) ? $skype_up : '';
  $twitter_up = isset($twitter_up) ? $twitter_up : '';
  $facebook_up = isset($facebook_up) ? $facebook_up : '';
  $yahoo_up = isset($yahoo_up) ? $yahoo_up : '';
  $icq_up = isset($icq_up) ? $icq_up : '';
  $aol_up = isset($aol_up) ? $aol_up : '';
  $interests_up = isset($interests_up) ? $interests_up : '';
  $occupation_up = isset($occupation_up) ? $occupation_up : '';
  $location_up = isset($location_up) ? $location_up : '';
  $website_up = isset($website_up) ? $website_up : '';

 	  $wpdb->query("UPDATE $db_bp_pf_data SET value = CASE field_id $youtube_up $googleplus_up $skype_up $twitter_up $facebook_up $yahoo_up $icq_up $aol_up $interests_up $occupation_up $location_up $website_up 
   ELSE value END WHERE user_id = '$current_user->ID'"); 

 }
 
// DELETE emtpy recognized BP fields, if value is empty in phpBB
// delete
 if( isset($del) ){

  $del_youtube = isset($del_youtube) ? "'$del_youtube'," : "";
  $del_googleplus = isset($del_googleplus) ? "'$del_googleplus'," : "";
  $del_skype = isset($del_skype) ? "'$del_skype'," : "";
  $del_twitter = isset($del_twitter) ? "'$del_twitter'," : "";
  $del_facebook = isset($del_facebook) ? "'$del_facebook'," : "";
  $del_yahoo = isset($del_yahoo) ? "'$del_yahoo'," : "";
  $del_icq = isset($del_icq) ? "'$del_icq'," : "";
  $del_aol = isset($del_aol) ? "'$del_aol'," : "";
  $del_interests = isset($del_interests) ? "'$del_interests'," : "";
  $del_occupation = isset($del_occupation) ? "'$del_occupation'," : "";
  $del_location = isset($del_location) ? "'$del_location'," : "";
  $del_website = isset($del_website) ? "'$del_website'," : "";

  $bp_del_fields_ids = $del_youtube . $del_googleplus . $del_skype . $del_twitter . $del_facebook . $del_yahoo . $del_icq . $del_aol . $del_interests . $del_occupation . $del_location . $del_website;
  $bp_del_fields_ids = substr($bp_del_fields_ids, 0, -1);

  $wpdb->query("DELETE FROM $db_bp_pf_data WHERE field_id IN( ".$bp_del_fields_ids." ) AND user_id = '$current_user->ID'");

}
 
 // INSERT recognized fields, if still not existent in BP profile data table (so not UPDATED on previous query)
 // thus, the follow should build all user's fields to INSERT, minus all the UPDATED above 
 // and after used to INSERT, if the case ...

date_default_timezone_set('UTC');
$last_updated = date('Y-m-d H:i:s');

	foreach( $bp_f as $uu => $ff ):
	
	  if ( stripos($ff->name, 'youtube' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_youtube) ){
       $youtube_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_youtube."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( stripos($ff->name, 'google' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_googleplus) ){
       $googleplus_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_googleplus."', '$last_updated' ),";
       $do_ins = true;
      } elseif  ( stripos($ff->name, 'skype' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_skype) ){
       $skype_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_skype."', '$last_updated' ),";
       $do_ins = true;
      } elseif  ( stripos($ff->name, 'twitter' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_twitter) ){
        $twitter_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_twitter."', '$last_updated' ),";
        $do_ins = true;
      } elseif ( stripos($ff->name, 'facebook' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_facebook) ){
       $facebook_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_facebook."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( stripos($ff->name, 'yahoo' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_yahoo) ){
       $yahoo_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_yahoo."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( stripos($ff->name, 'icq' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_icq) ){
       $icq_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_icq."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( stripos($ff->name, 'aol' ) !== false && !empty($phpbb_user_session[0]->pf_phpbb_aol) ){
       $aol_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_aol."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_interests ) && !empty($phpbb_user_session[0]->pf_phpbb_interests) ){
       $interests_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_interests."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_occupation ) && !empty($phpbb_user_session[0]->pf_phpbb_occupation) ){
       $occupation_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_occupation."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_location ) && !empty($phpbb_user_session[0]->pf_phpbb_location) ){
       $location_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_location."', '$last_updated' ),";
       $do_ins = true;
      } elseif ( array_search(trim(strtolower($ff->name)), $w3_bpl_profile_website ) && !empty($phpbb_user_session[0]->pf_phpbb_website) ){
       $website_up = "( '', '$ff->id', '$current_user->ID', '".$phpbb_user_session[0]->pf_phpbb_website."', '$last_updated' ),";
       $do_ins = true;
      } else { // nothing at moment 
      	}  
	
 endforeach;
	
	// which of those need to be inserted?
  $youtube_up = isset($youtube_up) ? $youtube_up : '';
  $googleplus_up = isset($googleplus_up) ? $googleplus_up : '';
  $skype_up = isset($skype_up) ? $skype_up : '';
  $twitter_up = isset($twitter_up) ? $twitter_up : '';
  $facebook_up = isset($facebook_up) ? $facebook_up : '';
  $yahoo_up = isset($yahoo_up) ? $yahoo_up : '';
  $icq_up = isset($icq_up) ? $icq_up : '';
  $aol_up = isset($aol_up) ? $aol_up : '';
  $interests_up = isset($interests_up) ? $interests_up : '';
  $occupation_up = isset($occupation_up) ? $occupation_up : '';
  $location_up = isset($location_up) ? $location_up : '';
  $website_up = isset($website_up) ? $website_up : '';	

// insert
 if(isset($do_ins)){
 	$insert_uf = $youtube_up . $googleplus_up . $skype_up . $twitter_up . $facebook_up . $yahoo_up . $icq_up . $aol_up . $interests_up . $occupation_up . $location_up . $website_up;
  $insert_uf = substr($insert_uf, 0, -1);
  $wpdb->query("INSERT INTO ".$db_bp_pf_data." ( id, field_id, user_id, value, last_updated ) VALUES $insert_uf");
 }

}

// END ONLY if BUDDYPRESS profile integration enabled
// #####################################################

   		if( empty($wp_umeta['locale'][0]) ){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   	  if( strlen(get_locale()) == 2 ){ $wp_lang_x_phpbb = strtolower(get_locale()); 
   		  	} else {
   		     $wp_lang_x_phpbb = substr(get_locale(), 0, strpos(get_locale(), '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     } 
   		} else {
   				if( strlen($wp_umeta['locale'][0]) == 2 ){ $wp_lang_x_phpbb = strtolower($wp_umeta['locale'][0]); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr($wp_umeta['locale'][0], 0, strpos($wp_umeta['locale'][0], '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     }  
   				}

   			$wp_lang_x_phpbb = empty($wp_lang_x_phpbb) ? 'en' : $wp_lang_x_phpbb;
     
   		if (  ( time() - $phpbb_config["session_length"] ) > $phpbb_user_session[0]->session_time && empty( $phpbb_k ) ){
  
            self::w3all_wp_logout();  

 	     	} else { // update
                     // last visit update
   			 	           $w3db_conn->query("UPDATE ". $config["table_prefix"] ."sessions SET session_time = '".time()."' WHERE session_id = '$phpbb_sid' OR session_browser = '".$useragent ."' AND session_user_id = '".$phpbb_user_session[0]->user_id."'");
                      // last visit update also
                      // this has been removed and reverted to the old way.
                      // make the user resulting updated as online in phpBB also, while visiting wordpress and not phpBB, but cause the reset  
                      // of the phpBB "new posts" feature ex - ./search.php?search_id=newposts - will show nothing
                    /* 
                     $w3db_conn->query("UPDATE ". $config["table_prefix"] ."users, ". $config["table_prefix"] ."sessions 
                     SET ". $config["table_prefix"] ."users.user_lastvisit = '".time()."', ". $config["table_prefix"] ."sessions.session_time = '".time()."', ". $config["table_prefix"] ."sessions.session_last_visit = '".time()."' 
                      WHERE ". $config["table_prefix"] ."users.user_id = '".$phpbb_user_session[0]->user_id."' 
                     AND ". $config["table_prefix"] ."sessions.session_user_id = '".$phpbb_user_session[0]->user_id."'
                     AND ". $config["table_prefix"] ."sessions.session_browser = '".$useragent."'");
                    */
                // NOTE phpbb_update_profile do the update of same fields so if code changes are done here adding custom profile fields
                // look also to change the function phpbb_update_profile( 
                
                // Check that email, password and site url match on both for this user
                // WP $current_user at this point (onlogin) DO NOT contain all data fields
                // $current_user->user_pass for example
                // so this update is done any time user login wp, almost one time

              	  // check for match between wp and phpbb profile fields. If some profile field still not exist on phpBB at this point for this user

              $phpbb_user_session[0]->pf_phpbb_website = (!empty($phpbb_user_session[0]->pf_phpbb_website)) ? $phpbb_user_session[0]->pf_phpbb_website : $current_user->user_url;
      
       // only if something to update
       if( $phpbb_user_session[0]->user_password != $current_user->user_pass OR $phpbb_user_session[0]->user_email != $current_user->user_email OR $phpbb_user_session[0]->pf_phpbb_website != $current_user->user_url OR $phpbb_user_session[0]->user_lang != $wp_lang_x_phpbb && $w3all_phpbb_lang_switch_yn == 1 )
   	    {
   	    $wpu_db_utab = (is_multisite() == true) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
   	    
      		$phpbb_upass = $phpbb_user_session[0]->user_password;
      		$phpbb_uemail = $phpbb_user_session[0]->user_email;
          $phpbb_uurl = $phpbb_user_session[0]->pf_phpbb_website;
      		 
  if($phpbb_user_session[0]->user_lang != $wp_lang_x_phpbb && $w3all_phpbb_lang_switch_yn == 1){ 
      		   // get array of installed langs in WP and build one to check against keys
      		   // NOTE: to switch to a lang with different notation, example x Persian, FA x phpBB and PE x WP, this switch need to be added above where line:
      		   // if( $phpbb_user_session[0]->user_lang == 'fa' ){ $phpbb_user_session[0]->user_lang = 'ps'; }
             // adding the needed switch in case ... 
            $wpLangs = wp_get_installed_translations('core');
             foreach ($wpLangs as $k => $v){ 
	            if($k=='default'){ 
		           $langAK=$v;
	            }
             }
            $wp_langs = array_keys($langAK);
        if(is_array($wp_langs) && in_array($phpbb_user_session[0]->user_lang,$wp_langs)){
	         $x_wp_locale = $phpbb_user_session[0]->user_lang; // assign the same if found
        } 
        if (!isset($x_wp_locale)){
                $x_wp_locale = empty($phpbb_user_session[0]->user_lang) ? get_locale() : $phpbb_user_session[0]->user_lang . '_' . strtoupper($phpbb_user_session[0]->user_lang); // should build to be WP compatible into something like it_IT or set emtpy for en WP default
      		 }

      		        // switched above, so this is may not useful here: switch also different notations x langs type .. add/change into some other in case
      		        //  if( $x_wp_locale == 'fa' ){ // persian switch fa -> se / se -> fa
      		        // 	  $x_wp_locale = 'ps';
      		        // 	 }
      		        update_user_meta($current_user->ID, 'locale', $x_wp_locale); 
      		     } 
      		     
	              $wpdb->query("UPDATE $wpu_db_utab SET user_pass = '$phpbb_upass', user_email = '$phpbb_uemail', user_url = '$phpbb_uurl' WHERE ID = '$current_user->ID'");
    
     if ( is_admin() ) {  // going on profile directly, after user have change a phpBB profile field: a refresh is done here
     	                    // to show updated user's profile fields data, that have been already updated into WP db, but not loaded into actual loaded user instance      
       $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
   		if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
			// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
			if ( is_multisite() && !get_active_blog_for_user($current_user->ID) && !is_super_admin( $current_user->ID ) )
				$redirect_to = user_admin_url();
			elseif ( is_multisite() && !$current_user->has_cap('read') )
				$redirect_to = get_dashboard_url( $current_user->ID );
			elseif ( !$current_user->has_cap('edit_posts') OR is_admin() )
				$redirect_to = $current_user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
				else
				  $redirect_to = home_url();  
      }
      // fix by Jakub
			// wp_redirect( $redirect_to );
			// exit();
		}
              
  } // END // only if something to update
   		                  
} // END update
   	  
   	return;
   	     
} // END is_user_logged_in()
     
     // switch the admin 
   	$user_id = ($phpbb_user_session[0]->user_id == 2) ? '1' : $phpbb_user_session[0]->user_id;

      $phpbb_real_username = sanitize_user( $phpbb_user_session[0]->username, $strict = false );
    	$ck_wpun_exists = username_exists( $phpbb_real_username );
      $user_id = email_exists( $phpbb_user_session[0]->user_email );
 
      if ( ! $user_id && ! $ck_wpun_exists ) { // add this user that not exists in WP

        if ( $phpbb_user_session[0]->group_name == 'ADMINISTRATORS' ){
      	      $role = 'administrator';
            } elseif ( $phpbb_user_session[0]->group_name == 'GLOBAL_MODERATORS' ){
            	   $role = 'editor';
               }  else { $role = 'subscriber'; }  // for all others phpBB Groups default to WP subscriber
          
         //////// phpBB username chars fix          	   	
         // phpBB need to have users without characters like ' that is not allowed in WP as username by default
         // If old phpBB usersnames are like myuse'name on WP_w3all integration, do not add into WP
         // check for 2 more of these on this class.wp.w3all-phpbb.php
         if ( preg_match('/[^-0-9A-Za-z _.@]/',$phpbb_user_session[0]->username) ){
	          echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>'.$phpbb_user_session[0]->username.'</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
           return;
         }

              $userdata = array(
               'user_login'       =>  $phpbb_real_username,
               'user_pass'        =>  $phpbb_user_session[0]->user_password,
               'user_email'       =>  $phpbb_user_session[0]->user_email,
               'user_registered'  =>  date_i18n( 'Y-m-d H:i:s', $phpbb_user_session[0]->user_regdate ),
               'role'             =>  $role
               );
          
          $user_id = wp_insert_user( $userdata );
          $ins_coming_phpbbU = true;
          
          if ( ! is_wp_error( $user_id ) ) {   
          	
        	 $user = get_user_by( 'ID', $user_id );
     
      	  if ( $phpbb_real_username != $user->user_login  ) {
      	     $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
	           $phpbb_real_username = $wpdb->escape($phpbb_real_username);
	           $wpdb->query("UPDATE $wpu_db_utab SET user_login = '".$phpbb_real_username."', user_nicename = '".$phpbb_real_username."' WHERE ID = ".$user->ID."");
      	    }
           }
         }
         
   if ( ! is_user_logged_in() && ! is_wp_error( $user_id ) ) {
    $uname = sanitize_user( $phpbb_user_session[0]->username, $strict = false );
    $user = get_user_by( 'login', $uname ); // only by username here: an email can be changed into phpBB profile and still not updated in WP. TODO: change into user 'nicename' when all switched
    
   if($phpbb_user_session[0]->user_id == 2){ // switch default user admin
    	$user = get_user_by( 'ID', 1 );
    }
    
    if( !$user ) { return; }
    
     	$remember = ( empty($phpbb_k) ) ? false : true;
      $secure = is_ssl();
       
        self::phpBB_user_session_set($user);
        wp_set_current_user( $user->ID, $user->user_login );
        wp_set_auth_cookie( $user->ID, $remember, $secure );
        do_action( 'wp_login', $user->user_login, $user );
	  
// START w3all redirect to phpBB (user redirected onlogin by snippet added into phpBB, to add user in WP)
  // Redirect to phpBB, if redirected by 'phpBB onlogin': if snippet code in phpBB is used to redirect in WP and add the user +- at same time into WP (good for not iframe mode)
  //if(get_option( 'w3all_iframe_phpbb_link_yn' ) == 0){ // if not in iframe mode
     if(isset($_GET["w3allAU"])){
       $uw = base64_decode(trim($_GET["w3allAU"]));
      	header("Location: $uw"); /* Redirect to phpBB a coming 'onlogin' */
     	 exit;
     }

  if (isset($ins_coming_phpbbU) OR !strstr($_SERVER['REQUEST_URI'], 'wp-admin') OR !strstr($_SERVER['SCRIPT_NAME'], 'profile.php')){
		if( isset( $_REQUEST['redirect_to'] ) ){
			wp_redirect($_REQUEST['redirect_to']); exit;
		} else {
			if( strstr($_SERVER['REQUEST_URI'], 'wp-admin') OR strstr($_SERVER['SCRIPT_NAME'], 'profile.php')){
			  wp_redirect(admin_url( 'profile.php' )); exit;
			 }
			return;
		}
	}

		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
  	if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
			// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
			if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
				$redirect_to = user_admin_url();
			elseif ( is_multisite() && !$user->has_cap('read') )
				$redirect_to = get_dashboard_url( $user->ID );
			elseif ( !$user->has_cap('edit_posts') OR is_admin() )
				$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
	 // check if it is a login done via phpBB into WP iframed page		
	$forum_pname_wp = get_option('w3all_forum_template_wppage');
	if( isset($_SERVER['REQUEST_URI']) && !empty($forum_pname_wp) && strstr($_SERVER['REQUEST_URI'], $forum_pname_wp) ){
		$redirect_to = home_url() . '/index.php/' . $forum_pname_wp;
	}	

   if (empty($redirect_to)){
    wp_redirect(home_url());
	 exit();   
  }

			wp_redirect( $redirect_to );
			exit();
		}
		
  wp_redirect(home_url());
	exit(); 
    
    }
   
    return;

 }  // END // HERE INSIDE WE ARE SECURE // END // 
     if ( is_user_logged_in() && !isset( $phpbb_user_session ) OR is_user_logged_in() && empty( $phpbb_user_session )) { 
     	   wp_logout();
         wp_redirect( home_url() ); exit;
     }
 return;
    
}  // END // verify_phpbb_credentials(){ // END //


private static function last_forums_topics($ntopics = 10){
	
     global $w3all_config,$w3all_exclude_phpbb_forums,$w3all_wlastopicspost_max,$w3all_get_topics_x_ugroup;

     $w3db_conn = self::w3all_db_connect();
     $ntopics = (empty($ntopics)) ? '10' : $ntopics; 

if($w3all_get_topics_x_ugroup == 1){ // list of allowed forums to retrieve topics if option active
           
if (defined('W3PHPBBUSESSION')) {
   $us = unserialize(W3PHPBBUSESSION);
   $ug = $us[0]->group_id;
   $ui = $us[0]->user_id;
  } else {
	$ug = 1; // the default phpBB guest user group
	$ui = 1;
}
// this need to be adjusted if 'phpBB default schema' isn't the used one
$gaf = $w3db_conn->get_results("SELECT DISTINCT ".$w3all_config["table_prefix"]."acl_groups.forum_id FROM ".$w3all_config["table_prefix"]."acl_groups 
WHERE ".$w3all_config["table_prefix"]."acl_groups.auth_role_id != 16
AND ".$w3all_config["table_prefix"]."acl_groups.group_id = ".$ug."");

 if(empty($gaf)){
	 return array(); // no forum found that can show topics for this group ... 
 } else { 
 	    $gf = '';
 	     foreach( $gaf as $v ){
        $gf .= $v->forum_id.',';
       }
   $gf = substr($gf, 0, -1);
   $topics_x_ugroup = "AND T.forum_id IN(".$gf.")";
   
 }} else {
	$topics_x_ugroup = '';
}

       // > 1.6.7 >> added also user's info
       // query improvement by @reloadgg // see https://www.axew3.com/w3/forums/viewtopic.php?f=2&t=850
  if (empty( $w3all_exclude_phpbb_forums )){
              
   $topics = $w3db_conn->get_results("SELECT T.*, P.*, U.* 
    FROM ".$w3all_config["table_prefix"]."topics AS T
    JOIN ".$w3all_config["table_prefix"]."posts AS P on (T.topic_last_post_id = P.post_id and T.forum_id = P.forum_id)
    JOIN ".$w3all_config["table_prefix"]."users AS U on U.user_id = T.topic_last_poster_id
    WHERE T.topic_visibility = 1
    ".$topics_x_ugroup."
    AND P.post_visibility = 1
    ORDER BY T.topic_last_post_time DESC
    LIMIT 0,$ntopics");
                    
  } else { 
 
  	if ( preg_match('/^[0-9,]+$/', $w3all_exclude_phpbb_forums )) {
        	$exp = explode(",", $w3all_exclude_phpbb_forums);
        	$no_forums_list = '';
        	 foreach($exp as $k => $v){
	          $no_forums_list .= "'".$v."',";
           }
            $nfl = substr($no_forums_list, 0, -1);
            $no_forums_list = "AND T.forum_id NOT IN(".$nfl.")"; 
    } else {
            $no_forums_list = '';
           }
           
   $topics = $w3db_conn->get_results("SELECT T.*, P.*, U.* 
    FROM ".$w3all_config["table_prefix"]."topics AS T
    JOIN ".$w3all_config["table_prefix"]."posts AS P on (T.topic_last_post_id = P.post_id and T.forum_id = P.forum_id) 
    JOIN ".$w3all_config["table_prefix"]."users AS U on U.user_id = T.topic_last_poster_id 
    WHERE T.topic_visibility = 1 
    ".$no_forums_list." 
    ".$topics_x_ugroup." 
    AND P.post_visibility = 1
    ORDER BY T.topic_last_post_time DESC
    LIMIT 0,$ntopics");   
                         
	}

	  if( $w3all_wlastopicspost_max == $ntopics ){
	   $t = is_array($topics) ? serialize($topics) : serialize(array());
     define( "W3PHPBBLASTOPICS", $t ); // see also wp_w3all.php and method wp_w3all_assoc_phpbb_wp_users in this class
    }
	  return $topics; 
}

/*private static function last_forums_topics($ntopics = 10){
	
     global $w3all_config,$w3all_exclude_phpbb_forums,$w3all_wlastopicspost_max,$w3all_get_topics_x_ugroup;
  
     $config = $w3all_config;
     $w3db_conn = self::w3all_db_connect();
     
     $ntopics = (empty($ntopics)) ? '10' : $ntopics; 

if($w3all_get_topics_x_ugroup == 1){ // list of allowed forums to retrieve topics if option active
           
if (defined('W3PHPBBUSESSION')) {
   $us = unserialize(W3PHPBBUSESSION);
   $ug = $us[0]->group_id;
   $ui = $us[0]->user_id;
  } else {
	$ug = 1; // the default phpBB guest user group
	$ui = 1;
}
  
// $gaf = $w3db_conn->get_results("SELECT DISTINCT forum_id FROM ".$config["table_prefix"]."acl_groups WHERE group_id = ".$ug." ORDER BY forum_id");
$gaf = $w3db_conn->get_results("SELECT DISTINCT ".$w3all_config["table_prefix"]."acl_groups.forum_id FROM ".$w3all_config["table_prefix"]."acl_groups 
WHERE ".$w3all_config["table_prefix"]."acl_groups.auth_role_id != 16
AND ".$w3all_config["table_prefix"]."acl_groups.group_id = ".$ug."
");

 if(empty($gaf)){
	 return array(); // no forum found that can show topics for this group ... 
 } else { 
 	    $gf = '';
 	     foreach( $gaf as $v ){
        $gf .= $v->forum_id.',';
       }
   $gf = substr($gf, 0, -1);
   $topics_x_ugroup = "AND P.forum_id IN(".$gf.")";
}

} else {
	$topics_x_ugroup = '';
}

       // From 1.6.7 added also user's info
       // TODO: should retrieve only needed data, and not all user's data  
       
   if (empty( $w3all_exclude_phpbb_forums )){
         	
         	    //$topics = $w3db_conn->get_results("SELECT * FROM ".$config["table_prefix"]."posts, ".$config["table_prefix"]."topics WHERE (SELECT MAX(topic_last_post_time) FROM ".$config["table_prefix"]."topics WHERE topic_visibility = 1) AND ".$config["table_prefix"]."posts.post_id = ".$config["table_prefix"]."topics.topic_last_post_id AND ".$config["table_prefix"]."posts.topic_id = ".$config["table_prefix"]."topics.topic_id AND ".$config["table_prefix"]."posts.post_visibility = 1 ORDER BY post_time DESC LIMIT 0,$ntopics");
              $topics = $w3db_conn->get_results("SELECT DISTINCT T.*, P.*, U.* FROM ".$config["table_prefix"]."topics AS T, ".$config["table_prefix"]."posts AS P, ".$config["table_prefix"]."users AS U 
              WHERE T.topic_visibility = 1 
              AND T.topic_last_post_id = P.post_id 
              AND P.post_visibility = 1 
              ".$topics_x_ugroup." 
              AND U.user_id = T.topic_last_poster_id 
              GROUP BY P.topic_id
              ORDER BY T.topic_last_post_time DESC LIMIT 0,$ntopics");

     } else {
        	
        	      if ( preg_match('/^[0-9,]+$/', $w3all_exclude_phpbb_forums )) { 

        	         	$exp = explode(",", $w3all_exclude_phpbb_forums);
        	         	$no_forums_list = '';
                    while (list(, $value) = each($exp)) {
	                        $no_forums_list .= "'".$value."',";
                        }
                         
                    $no_forums_list = substr($no_forums_list, 0, -1);
                     
        	  		   //$topics = $w3db_conn->get_results("SELECT * FROM ".$config["table_prefix"]."posts, ".$config["table_prefix"]."topics WHERE (SELECT MAX(topic_last_post_time) FROM ".$config["table_prefix"]."topics WHERE topic_visibility = 1) AND ".$config["table_prefix"]."topics.forum_id NOT IN(".$no_forums_list.")  AND ".$config["table_prefix"]."posts.post_id = ".$config["table_prefix"]."topics.topic_last_post_id AND ".$config["table_prefix"]."posts.topic_id = ".$config["table_prefix"]."topics.topic_id AND ".$config["table_prefix"]."posts.post_visibility = 1 ORDER BY post_time DESC LIMIT 0,$ntopics");
                   $topics = $w3db_conn->get_results("SELECT DISTINCT T.*, P.*, U.* FROM ".$config["table_prefix"]."topics AS T, ".$config["table_prefix"]."posts AS P, ".$config["table_prefix"]."users AS U 
                   WHERE T.topic_visibility = 1  
                   AND T.forum_id NOT IN(".$no_forums_list.") 
                   AND T.topic_last_post_id = P.post_id 
                   AND P.post_visibility = 1 
                   ".$topics_x_ugroup."
                   AND U.user_id = T.topic_last_poster_id 
                   GROUP BY P.topic_id
                   ORDER BY T.topic_last_post_time DESC LIMIT 0,$ntopics");
                  
                  } else {  
        	          //$topics = $w3db_conn->get_results("SELECT * FROM ".$config["table_prefix"]."posts, ".$config["table_prefix"]."topics WHERE (SELECT MAX(topic_last_post_time) FROM ".$config["table_prefix"]."topics WHERE topic_visibility = 1) AND ".$config["table_prefix"]."posts.post_id = ".$config["table_prefix"]."topics.topic_last_post_id AND ".$config["table_prefix"]."posts.topic_id = ".$config["table_prefix"]."topics.topic_id AND ".$config["table_prefix"]."posts.post_visibility = 1 ORDER BY post_time DESC LIMIT 0,$ntopics");
                    $topics = $w3db_conn->get_results("SELECT DISTINCT T.*, P.*, U.* FROM ".$config["table_prefix"]."topics AS T, ".$config["table_prefix"]."posts AS P, ".$config["table_prefix"]."users AS U 
                    WHERE T.topic_visibility = 1 
                    AND T.topic_last_post_id = P.post_id 
                    AND P.post_visibility = 1 
                    ".$topics_x_ugroup." 
                    AND U.user_id = T.topic_last_poster_id 
                    GROUP BY P.topic_id
                    ORDER BY T.topic_last_post_time DESC LIMIT 0,$ntopics");
                   }                
	          }

	  if( $w3all_wlastopicspost_max == $ntopics ){
	   $t = is_array($topics) ? serialize($topics) : serialize(array());
     define( "W3PHPBBLASTOPICS", $t ); // see also wp_w3all.php and method wp_w3all_assoc_phpbb_wp_users in this class
    }
	  return $topics; 
}*/


private static function phpBB_user_session_set($wp_user_data){
	      global $w3all_config,$wpdb,$useragent;
       $phpbb_config_file = $w3all_config;
       
      if(!defined("W3PHPBBCONFIG")){
       	$phpbb_config = self::w3all_get_phpbb_config();
       } else {
	      $phpbb_config = unserialize(W3PHPBBCONFIG);
	     }
	     
       $w3phpbb_conn = self::w3all_db_connect();
       $w3cookie_domain = get_option('w3all_phpbb_cookie');
      	
        $k   = $phpbb_config["cookie_name"].'_k';
        $sid = $phpbb_config["cookie_name"].'_sid';
        $u   = $phpbb_config["cookie_name"].'_u';
         
         if ( !$wp_user_data OR $wp_user_data->ID < 1 ){
		  	      return; 
		      }    

     if( $wp_user_data->ID == 1 ){ // switch admin
         	$phpbb_user_id = 2;
       } else { 
               $phpbb_u = $w3phpbb_conn->get_row("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE username = '$wp_user_data->user_login'");
          if( empty($phpbb_u) ){ return; }
    
        if( self::w3_phpbb_ban($phpbb_u) === true ){
        	self::w3all_wp_logout();  
        }
       
       if( $wp_user_data->ID > 1 ){ // switch not admin
         	$phpbb_user_id = $phpbb_u->user_id;	
       }
     
             
                if ( $phpbb_u->user_type == 1 ){ // is this user deactivated/banned in phpBB? / logout/and deactivate in WP
                 //update_user_meta($user_id, 'wp_capabilities', 'a:0:{}'); maybe substitute with this
                  //$wpu_db_utab = $wpdb->prefix . 'usermeta';
	                $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'usermeta' : $wpdb->prefix . 'usermeta';
	                $wpdb->query("UPDATE $wpu_db_utab SET meta_value = 'a:0:{}' WHERE user_id = '$wp_user_data->ID' AND meta_key = 'wp_capabilities'");
	                return; 
               }
             }

       $time = time();
       $val = md5($phpbb_config["rand_seed"] . microtime()); // to user_form_salt
       $val = md5($val);
       $phpbb_config["rand_seed"] = md5( $phpbb_config["rand_seed"] . $val . rand() ); // the rand seed to be updated
       $phpbb_rand_seed = $phpbb_config["rand_seed"];
       
        $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."config SET config_value = '$phpbb_rand_seed' WHERE config_name = 'rand_seed'");
        $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."config SET config_value = '$time' WHERE config_name = 'rand_seed_last_update'");
        $w3_unique_id = substr($val, 4, 16); 
        $w3session_id = md5($w3_unique_id);
     
  //   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_form_salt = '$val' WHERE user_id = '$user_id'");
   
        $uip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $auto_login = 1; 
           $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."sessions (session_id, session_user_id, session_last_visit, session_start, session_time, session_ip, session_browser, session_forwarded_for, session_page, session_viewonline, session_autologin, session_admin, session_forum_id) 
          VALUES ('$w3session_id', '$phpbb_user_id', '$time', '$time', '$time', '$uip', '$useragent', '', 'index.php', '1', '$auto_login', '0', '0')");
 
     // $key_id = unique_id(hexdec(substr($this->session_id, 0, 8))); // phpBB

      $key_id = hexdec(substr($w3session_id, 0, 8));
      $valk = $phpbb_config["rand_seed"] . microtime() . $key_id;
      $valk = md5($valk);
      $key_id_k  = substr($valk, 4, 16); // to k
      $key_id_sk = md5($key_id_k); // to sessions_keys

         $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."sessions_keys (key_id, user_id, last_ip, last_login) 
          VALUES ('$key_id_sk', '$phpbb_user_id', '$uip', '$time')");

	    $cookie_expire = $time + (($phpbb_config['max_autologin_time']) ? 86400 * (int) $phpbb_config['max_autologin_time'] : 31536000);

      $secure = is_ssl();
      if(empty($w3cookie_domain)){
      	$w3cookie_domain = 'localhost';
      }
	    setcookie ("$k", "$key_id_k", $cookie_expire, "/", $w3cookie_domain, $secure);
 	    setcookie ("$sid", "$w3session_id", $cookie_expire, "/", $w3cookie_domain, $secure); 
 	    setcookie ("$u", "$phpbb_user_id", $cookie_expire, "/", $w3cookie_domain, $secure);  
    //  wp_set_current_user( $wp_user_data->ID, $wp_user_data->user_login );
}

public static function w3_phpbb_ban($phpbb_u = '', $uname = '', $uemail = ''){
	// all the follow not check username ban, because the check about same username exists in phpBB is done in different way already and a banned username in phpBB, ever exists into phpBB users table ...
	if ( false === is_email( $uemail ) ) {
		return; // return true will add the error, but in this case it is thrown by WP (wrong email) ...
}
		global $w3all_config;
	  $w3phpbb_conn = self::w3all_db_connect();

if( !empty($phpbb_u) ){ // check uid, ip and email for ban
	  $phpbb_banl = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."banlist WHERE ban_userid = $phpbb_u->user_id OR ban_userid = '0'", ARRAY_A);
 if( !empty($phpbb_banl) ){
       $ban_userid = array_column($phpbb_banl, 'ban_userid');
       $ban_user_ip = array_column($phpbb_banl, 'ban_ip');
       $ban_user_email = array_column($phpbb_banl, 'ban_email');
  if ( in_array($phpbb_u->user_id, $ban_userid) OR in_array($_SERVER['REMOTE_ADDR'], $ban_user_ip) OR in_array($phpbb_u->user_email, $ban_user_email) ) {
    return true; // the user is banned on phpBB forum    
  }
  return false;
 }
}
// this is to be improved and done in different way ... 
// the follow just check ever for '.domain.com' where banned emails contain '*' (wildcard check)

$uemail_domain = substr(strrchr($uemail, "@"), 1);
$uemail_domain = '.' . $uemail_domain;
$ed = explode(".", $uemail_domain);
$ed = array_slice($ed, -2, 2);
if(!empty($ed) && count($ed) == 2){
	$ued = '';
foreach($ed as $e){
	$ued .= $e . '.'; // .domain.com
}}
$ued = substr($ued, 0, -1);

//$etype = substr_count($uemail_domain, '.');  // 1 -> domain email (*@mail.com) // > 1 subdomain (*@*.domain.tld)
 
 $phpbb_banl = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."banlist WHERE ban_userid = '0'", ARRAY_A);

if( empty($phpbb_banl) ){
	return;
}

 $ban_user_email = array_column($phpbb_banl, 'ban_email');
 $btype = '';
foreach($ban_user_email as $bue){
 // $btype = substr_count($bue, '.'); // 1 -> domain email (*@mail.com) // > 1 subdomain (*@*.domain.tld)
 // if (stristr($bue, '*') && $btype == 1 ){
 	if (stristr($bue, '*')){
    $eb = explode("*", $bue); 
    if(in_array('.'.$ued,$eb) OR in_array('@'.$ued,$eb) OR in_array($ued,$eb)){
     	return true; // the email is banned on phpBB forum
    }
 } /*elseif (stristr($bue, '*') && $btype > 1 ){
   $eb = explode("*", $bue);
   if(in_array($ued,$eb) OR in_array('.'.$ued,$eb)){
    	return true; // the email is banned on phpBB forum
  	}
  } */
}

if( ! empty($uemail) ){
       $ban_user_ip = array_column($phpbb_banl, 'ban_ip');
       $ban_user_email = array_column($phpbb_banl, 'ban_email');

  if ( in_array($_SERVER['REMOTE_ADDR'], $ban_user_ip) OR in_array($uemail, $ban_user_email) ) {
    return true; // the user is banned on phpBB forum    
  }
  return false;
}

	return false;
}

private static function create_phpBB_user($wpu){

	global $w3all_config, $w3all_phpbb_user_deactivated_yn, $w3all_phpbb_lang_switch_yn, $w3all_add_into_spec_group;
	 $w3phpbb_conn = self::w3all_db_connect();
   $phpbb_config = unserialize(W3PHPBBCONFIG);
   $default_dateformat = $phpbb_config["default_dateformat"];
   $default_lang = $phpbb_config["default_lang"];
   
   // temp fix added also on
   // private static function create_phpBB_user_wpms($username = '', $user_email = '', $key = '', $meta = ''){
   // get info about phpBB group where the user need to added: this should may be added on query where W3PHPBBCONFIG is defined
  
   /*$phpbb_group = $w3phpbb_conn->get_row("SELECT * FROM ".$w3all_config["table_prefix"]."groups WHERE group_id = '$w3all_add_into_spec_group'");
   	if( empty($phpbb_group) OR ! $phpbb_group ){
    $group_color = '';
   } else { $group_color = $phpbb_group->group_colour; }
   */
  $phpbb_group = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."ranks
   RIGHT JOIN ".$w3all_config["table_prefix"]."groups ON ".$w3all_config["table_prefix"]."groups.group_rank = ".$w3all_config["table_prefix"]."ranks.rank_id
   AND ".$w3all_config["table_prefix"]."ranks.rank_min = '0'
   AND ".$w3all_config["table_prefix"]."groups.group_id = '$w3all_add_into_spec_group'",ARRAY_A);  
 
 if(!empty($phpbb_group)){  
 	$urank_id_a = array();
   foreach($phpbb_group as $kv){
   	foreach($kv as $k => $v){
     if($k == 'group_id' && $v == $w3all_add_into_spec_group){
    	$urank_id_a = $kv;
     }
   }}
 if (empty($urank_id_a)){
   foreach($phpbb_group as $kv){
   	foreach($kv as $k => $v){
   	if($k == 'rank_special' && $v == 0){
    $urank_id_a = $kv;
    goto this1; // break to the first found ('it seem' to me the default phpBB behavior)??
    }
   }} 
 }
this1:
if ( empty($urank_id_a) ){ 
	$rankID = 0;
	$group_color = '';
 } else {
if ( empty($urank_id_a['rank_id']) ){ 
	$rankID = 0; $group_color = $urank_id_a['group_colour'];
	} else { 
	$rankID = $urank_id_a['rank_id']; $group_color = $urank_id_a['group_colour'];
}}

} // END if(!empty($phpbb_group) OR ! $phpbb_group){    
else { 	$rankID = 0; $group_color = ''; }

  /* if($w3all_phpbb_lang_switch_yn == 1){
   		if(strlen(get_locale()) == 0){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   $wp_lang_x_phpbb = 'en'; // no lang setting, assume en by default
   			} else { 
   				if( strlen(get_locale()) == 2 ){ $wp_lang_x_phpbb = strtolower(get_locale()); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr(get_locale(), 0, strpos(get_locale(), '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     }
   				   //$wp_lang_x_phpbb = strtolower(substr($wp_lang, 0, strpos($wp_lang, '_'))); // should extract Lang code ISO Code that is phpBB suitable for this lang
   				}
   				
   				// switch for different languages notations
   				if( $wp_lang_x_phpbb == 'ps' ){ $wp_lang_x_phpbb = 'fa'; // persian
   					 }
     } */

  if(!isset($default_lang) OR empty($default_lang)){ $wp_lang_x_phpbb = 'en'; }
  else { $wp_lang_x_phpbb = $default_lang; }
        
    if( empty($wpu) ){ return; }
     
     //maybe to be added as option
     // if you wish to setup gravatar by default into phpBB profile for the user when register in WP
     $uavatar = $avatype = ''; // this will not affect queries if the two here below are commented
     //$uavatar = get_option('show_avatars') == 1 ? $wpu->user_email : '';
     //$avatype = (empty($uavatar)) ? '' : 'avatar.driver.gravatar';
     
     $wpu->user_login = esc_sql($wpu->user_login);
     $u = $phpbb_config["cookie_name"].'_u';
            
            if ( preg_match('/[^0-9]/',$_COOKIE[$u]) ){
 	           	
                die( "Clean up cookie on your browser please!" );
 	            }
 	            
 	           $phpbb_u = $_COOKIE[$u];
 	        
 	    // only need to fire when user do not exist on phpBB already, and/or user is an admin that add an user manually 
   if ( $phpbb_u < 2 OR !empty($phpbb_u) && current_user_can( 'manage_options' ) === true ) {
      
      // check that the user need to be added as activated or not into phpBB
      	
        $phpbb_user_type = ($w3all_phpbb_user_deactivated_yn == 1) ? 1 : 0; 
        if(current_user_can( 'manage_options' ) === true){ // an admin adding user
        	$phpbb_user_type = 0;
        }      
        
      $wpu->user_registered = time($wpu->user_registered); // as phpBB do
	    $user_email_hash = self::w3all_phpbb_email_hash($wpu->user_email);
	     
      $wpur = $wpu->user_registered;
      $wpul = $wpu->user_login;
      $wpup = $wpu->user_pass;
      $wpue = $wpu->user_email;
      $time = time();
      
      $wpunn = esc_sql(utf8_encode(strtolower($wpul)));
      $wpul  = esc_sql($wpul);
      $w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."users (user_id, user_type, group_id, user_permissions, user_perm_from, user_ip, user_regdate, username, username_clean, user_password, user_passchg, user_email, user_email_hash, user_birthday, user_lastvisit, user_lastmark, user_lastpost_time, user_lastpage, user_last_confirm_key, user_last_search, user_warnings, user_last_warning, user_login_attempts, user_inactive_reason, user_inactive_time, user_posts, user_lang, user_timezone, user_dateformat, user_style, user_rank, user_colour, user_new_privmsg, user_unread_privmsg, user_last_privmsg, user_message_rules, user_full_folder, user_emailtime, user_topic_show_days, user_topic_sortby_type, user_topic_sortby_dir, user_post_show_days, user_post_sortby_type, user_post_sortby_dir, user_notify, user_notify_pm, user_notify_type, user_allow_pm, user_allow_viewonline, user_allow_viewemail, user_allow_massemail, user_options, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_sig, user_sig_bbcode_uid, user_sig_bbcode_bitfield, user_jabber, user_actkey, user_newpasswd, user_form_salt, user_new, user_reminded, user_reminded_time)
         VALUES ('','$phpbb_user_type','$w3all_add_into_spec_group','','0','', '$wpur', '$wpul', '$wpunn', '$wpup', '0', '$wpue', '$user_email_hash', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '$wp_lang_x_phpbb', 'Europe/Rome', '$default_dateformat', '1', '$rankID', '$group_color', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', 0, 't', 'a', '0', '1', '0', '1', '1', '1', '1', '230271', '$uavatar', '$avatype', '0', '0', '', '', '', '', '', '', '', '0', '0', '0')");
      
      $phpBBlid = $w3phpbb_conn->insert_id;
   
     //$w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."user_group (group_id, user_id, group_leader, user_pending) VALUES ('2','$phpBBlid','0','0')");
     $w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."user_group (group_id, user_id, group_leader, user_pending) VALUES ('$w3all_add_into_spec_group','$phpBBlid','0','0')");
     //$w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."acl_users (user_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES ('$phpBBlid','0','0','6','0')");
    	
    	// TODO: unify all these updates	
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = config_value + 1 WHERE config_name = 'num_users'");

       $newest_member = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."users WHERE user_id = (SELECT Max(user_id) FROM ".$w3all_config["table_prefix"]."users) AND group_id != '6'");
       $uname = $newest_member[0]->username;
       $uid   = $newest_member[0]->user_id;
     
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = '$wpul' WHERE config_name = 'newest_username'");
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = '$uid' WHERE config_name = 'newest_user_id'");

    // define( "W3PHPBBCREATEDUSER", true); // this is for the phpbb_update_profile function fired after this
 
 }

}

// check existance in phpBB of username and email
public static function phpBB_user_check( $sanitized_user_login, $user_email, $is_admin_action = 1 ){

	      global $w3all_config;
        $phpbb_config_file = $w3all_config;
	      $w3phpbb_conn = self::w3all_db_connect();
        //$phpbb_config = self::get_phpbb_config();
         
      if(!defined("W3PHPBBCONFIG")){
       	$phpbb_config = self::w3all_get_phpbb_config();
       } else {
	      $phpbb_config = unserialize(W3PHPBBCONFIG);
	     }

        $u = $phpbb_config["cookie_name"].'_u';
            
            if ( isset($_COOKIE["$u"]) && preg_match('/[^0-9]/',$_COOKIE[$u]) ){
 	           	
                die( "Clean up cookie on your browser." );
 	            }
 	            
 	      $sanitized_user_login = esc_sql($sanitized_user_login);   
 	      $user_email = esc_sql($user_email);  
 	      $user_email_hash = self::w3all_phpbb_email_hash($user_email);
 	      
 	     if( $is_admin_action == 1 ){
 	     	         $phpbb_any = $w3phpbb_conn->get_row("SELECT username, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' OR username = '$sanitized_user_login'");
         if ( null !== $phpbb_any ) {
           return true;
 	       }
 	     }
 	     	       
 	     	  $_COOKIE[$u] = (isset($_COOKIE[$u])) ? $_COOKIE[$u] : '';
 	     	  
 	        if ( $_COOKIE["$u"] < 2 && $is_admin_action == 0 ){ // check only for phpBB user that come as NOT logged in - or get 'undefined wp_delete' error
         $phpbb_any = $w3phpbb_conn->get_row("SELECT username, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' OR username = '$sanitized_user_login'");
       if ( null !== $phpbb_any ) {
        return true;
     }
     
    }
    
     return false;
}

// add_filter( 'registration_errors', 'wp_w3all_check_fields', 10, 3 );
public static function phpBB_user_check2( $errors, $sanitized_user_login, $user_email ){

// as wp 4.9 if you're logged in wp and you point to 'wp-login.php?action=register' you can see that you can register a new user even when logged as user.
// wp will add this new user and will present the login form: but if you return inot wp home, you see that you result logged as previous user
// since in this case the plugin will detect that the user is logged, the new registered wp user will not be added also into phpBB at same time!
// so avoid a logged in user in WP to register a new account in WP! 
if ( is_user_logged_in() ) {
    $errors->add( 'w3_ck_ulogged_try_to_reg_error', __( '<strong>ERROR</strong>: You\'re logged in! Back to main site and logout before to create a new account.', 'wp-w3all-phpbb-integration' ) );
    return $errors;
}

	  global $w3all_config;
	   $w3phpbb_conn = self::w3all_db_connect();
     $user_email_hash = self::w3all_phpbb_email_hash($user_email); 
       // user_email_hash > 0 needed to return correct value for errors on -> wp_w3all.php -> function wp_w3all_check_fields
         $phpbb_anybody = $w3phpbb_conn->get_row("SELECT username, user_email FROM ".$w3all_config["table_prefix"]."users WHERE user_email_hash > 0 AND user_email_hash = '$user_email_hash' OR username = '$sanitized_user_login'");
       
       if ( null !== $phpbb_anybody ) {
        return true;
     }

  return false;
}

public static function check_phpbb_passw_match_on_wp_auth ( $username, $is_phpbb_admin = 0 ) {
  
     global $wpdb, $w3all_config;
     $phpbb_config_file = $w3all_config;
     
   if( empty($username) ){ return; }

	    $w3phpbb_conn = self::w3all_db_connect();

      $wpu = get_user_by('login', $username);
      
      if( $is_phpbb_admin == 1 ){ // wp default install admin

      $phpbb_pae = $w3phpbb_conn->get_row("SELECT user_password, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_id = '2'");

	     if( $phpbb_pae->user_password != $wpu->user_pass && !empty($phpbb_pae->user_password) ){
	
	     $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
  
	        $wpdb->query("UPDATE $wpu_db_utab SET user_pass = '$phpbb_pae->user_password' WHERE ID = '1'");
        
        return $phpbb_pae->user_password;
     }
  }
 
    if( $is_phpbb_admin == 0 ){ // passw change for all others

       	  $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
  
     $phpbb_pae = $w3phpbb_conn->get_row("SELECT user_password, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE username = '$wpu->user_login'");

// do not check if user isn't created in phpBB (empty result) may due to the fact in MUMS the sub-admin have not link his sub-site to the forum in wp_w3all config
	     if( !empty($phpbb_pae) && $phpbb_pae->user_password != $wpu->user_pass && !empty($phpbb_pae->user_password) ){

	       $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
	        $wpdb->query("UPDATE $wpu_db_utab SET user_pass = '$phpbb_pae->user_password' WHERE user_login = '$wpu->user_login'");

        return $phpbb_pae->user_password;
    }
  }
  
  return false;
   
}


public static function wp_w3all_phpbb_logout() {
	 global $w3all_config,$w3cookie_domain,$useragent;
      $phpbb_config_file = $w3all_config;
  	  $w3phpbb_conn = self::w3all_db_connect();
	  	//$phpbb_config = self::get_phpbb_config();
	  	$phpbb_config = unserialize(W3PHPBBCONFIG);
        	  	
        $k   = $phpbb_config["cookie_name"].'_k';
        $sid = $phpbb_config["cookie_name"].'_sid';
        $u   = $phpbb_config["cookie_name"].'_u';
        
     if(isset($_COOKIE[$k])){   
      if ( preg_match('/[^0-9A-Za-z]/',$_COOKIE[$k]) OR preg_match('/[^0-9A-Za-z]/',$_COOKIE[$sid]) OR preg_match('/[^0-9]/',$_COOKIE[$u]) ){
 	           	 die( "Please clean up cookies on your browser." );
 	            }

   $k_md5 = md5($_COOKIE[$k]);
 	 $u_id = $_COOKIE[$u];
 	 $s_id = $_COOKIE[$sid];

    // logout phpBB user
    $w3phpbb_conn->query("DELETE FROM ".$phpbb_config_file["table_prefix"]."sessions WHERE session_id = '$s_id' AND session_user_id = '$u_id' OR session_user_id = '$u_id' AND session_browser = '$useragent'");
    $w3phpbb_conn->query("DELETE FROM ".$phpbb_config_file["table_prefix"]."sessions_keys WHERE key_id = '$k_md5' AND user_id = '$u_id'");
  
 	// remove phpBB cookies
   
   // AstoSoft - Start
      /*setcookie ("$k", "", time() - 31622400, "/");
 	    setcookie ("$sid", "", time() - 31622400, "/"); 
 	    setcookie ("$u", "", time() - 31622400, "/"); 
 	    setcookie ("$k", "", time() - 31622400, "/", "$w3cookie_domain");
 	    setcookie ("$sid", "", time() - 31622400, "/", "$w3cookie_domain"); 
       setcookie ("$u", "", time() - 31622400, "/", "$w3cookie_domain"); */
       // AstoSoft - End
   }

}

    
public static function phpbb_pass_update($user, $new_pass) { 

     	 global $w3all_config,$wpdb;
     
     $phpbb_config_file = $w3all_config;
     $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();

        	$wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';

// TODO: why this is here? look not useful and crazy ... check and remove the following query, $user object should contain already all needed data ... should, but check before ...
	     $ud = $wpdb->get_row("SELECT * FROM  $wpu_db_utab WHERE ID = '$user->ID'");

    if ( $user->ID == 1 ){ // update phpBB admin uid2
      	
       $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$ud->user_pass' WHERE	user_id = '2'");
     // should reset any session here
     // $w3phpbb_conn->query("DELETE ".$phpbb_config_file["table_prefix"]."sessions, ".$phpbb_config_file["table_prefix"]."sessions_keys FROM ".$phpbb_config_file["table_prefix"]."sessions INNER JOIN ".$phpbb_config_file["table_prefix"]."sessions_keys.....   ");


      } else { 
     
       $user_email_hash = self::w3all_phpbb_email_hash($ud->user_email);
       // $ulogin = esc_sql($user->user_login);

       $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$ud->user_pass' WHERE 	user_email_hash = '".$user_email_hash."' OR username = '".$user->user_login."'");
     //  should reset any session here
	   //  $w3phpbb_conn->query("DELETE ".$phpbb_config_file["table_prefix"]."sessions, ".$phpbb_config_file["table_prefix"]."sessions_keys FROM ".$phpbb_config_file["table_prefix"]."sessions INNER JOIN ".$phpbb_config_file["table_prefix"]."sessions_keys.....   ");
   
     } 

 } 

 public static function phpbb_update_profile($user_id, $old_user_data) { // changed from 1.7.6

   global $wpdb,$w3all_config,$w3all_phpbb_lang_switch_yn;
  
     $phpbb_config_file = $w3all_config;
     $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
     $phpbb_config = unserialize(W3PHPBBCONFIG);

     $phpbb_version = substr($phpbb_config["version"], 0, 3);
     
     $wpu = get_user_by('ID', $user_id);
     
     if( $wpu === false ){ return; }

     $phpbb_user_type = ( empty($wpu->roles) ) ? '1' : '0';
     $wpu->user_login = esc_sql($wpu->user_login); 
    // updated wp email hash      
     $user_email_hash = self::w3all_phpbb_email_hash($wpu->user_email);    
 
   if ( is_multisite() ) {
//$wp_user_p_blog = get_user_meta($user_id, 'primary_blog', true);
// a normal user result with no capability in MU???
// how do we get user capabilities for users that not choose for a site on register?
// they not come with a role defined elsewhere seem to me ... TODO check this ...

// temp fix: set user type by the way as active in phpBB ... TODO check: may there is something that interfere
   $phpbb_user_type = 0;
  }
  
 // switch install admin to check
 if($user_id == 1){ 
      $phpbb_is_there_anybody = $w3phpbb_conn->get_row("SELECT username, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' AND user_id != '2'");
 	  } else {
 	  	$phpbb_is_there_anybody = $w3phpbb_conn->get_row("SELECT username, user_email FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' AND username != '$wpu->user_login'");   
 	  }	    
 	    	       if ( null !== $phpbb_is_there_anybody ) { // skip admin install // revert // if there are usernames or email address, reset to old value and return error
      	
          $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
  
	      $wpdb->query("UPDATE $wpu_db_utab SET user_email = '$old_user_data->user_email' WHERE ID = '$wpu->ID'");
   
       return true;
       
     }
     
 if($user_id == 1){ 
     $uid = 2; 
  } else {
          $old_user_email_hash = self::w3all_phpbb_email_hash($old_user_data->user_email);
          $uid = $w3phpbb_conn->get_var("SELECT user_id FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$old_user_email_hash' OR username = '$wpu->user_login'"); // as there is not UID with the above query ... TODO change this
        }
    
  if ( empty($uid) OR $uid < 2 ){ return; }
      
    // see also function phpbb_verify_credentials(

     $u_url = $wpu->user_url;
     $wp_umeta = get_user_meta($wpu->ID, '', false);

   		if( empty($wp_umeta['locale'][0]) ){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   //$wp_lang_x_phpbb = 'en'; // no lang setting, assume en
   		   	  if( strlen(get_locale()) == 2 ){ $wp_lang_x_phpbb = strtolower(get_locale()); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr(get_locale(), 0, strpos(get_locale(), '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     } 
   		} else {
   				if( strlen($wp_umeta['locale'][0]) == 2 ){ $wp_lang_x_phpbb = strtolower($wp_umeta['locale'][0]); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr($wp_umeta['locale'][0], 0, strpos($wp_umeta['locale'][0], '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     }
   			}		
   				// switch for different languages notations
   				if( $wp_lang_x_phpbb == 'ps' ){ $wp_lang_x_phpbb = 'fa'; // persian
   					 }
   				// and to be sure
          if(!isset($wp_lang_x_phpbb) OR empty($wp_lang_x_phpbb)){ $wp_lang_x_phpbb = 'en'; }

   				
      if( $w3all_phpbb_lang_switch_yn == 1 ){ // do not update lang if not activated option
      	  if($uid == 2){ // if phpBB id 2 not update the user_type
      		   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash', user_lang = '$wp_lang_x_phpbb' WHERE user_id = '$uid'");
      	   } else { // note: here the user_type in phpBB will be set as 2 for any user on reactivation, but may should be based on group that user belong, may 3 if admin and so on ... so the above '$uid SELECT' query should be changed to achieve this ...
                   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '$phpbb_user_type', user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash', user_lang = '$wp_lang_x_phpbb' WHERE user_id = '$uid'");
                  }
       } else {
       	       if($uid == 2){ // if phpBB id 2 not update the user_type
       	          $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash' WHERE user_id = '$uid'");
                } else { // note: here the user_type in phpBB will be set as 2 for any user on reactivation, but may should be based on group that user belong, may 3 if admin and so on ... so the above '$uid SELECT' query should be changed to achieve this ...
              	        $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '$phpbb_user_type', user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash' WHERE user_id = '$uid'");
                       }
              }
              
// prevent error on update profiles fields, if field's number mismatch by these default arrays (default phpBB profile fields lists - phpBB 3.1 and 3.2 ) 

 if( $phpbb_version == '3.2' ){
  $default_phpbb_profile_fields_names = array("phpbb_location","phpbb_website","phpbb_interests","phpbb_occupation","phpbb_aol","phpbb_icq","phpbb_yahoo","phpbb_facebook","phpbb_twitter","phpbb_skype","phpbb_youtube","phpbb_googleplus");
} else {
  $default_phpbb_profile_fields_names = array("phpbb_location","phpbb_website","phpbb_interests","phpbb_occupation","phpbb_aol","phpbb_icq","phpbb_yahoo","phpbb_facebook","phpbb_twitter","phpbb_skype","phpbb_youtube","phpbb_googleplus","phpbb_wlm");
 }
  
  $phpbb_pf_cols = $w3phpbb_conn->get_results("SELECT field_name FROM ". $phpbb_config_file["table_prefix"] ."profile_fields");       

   foreach($phpbb_pf_cols as $ppf_cols){
   	if( !in_array($ppf_cols->field_name, $default_phpbb_profile_fields_names) OR count($phpbb_pf_cols) != count($default_phpbb_profile_fields_names) ){
   		// profile fields mismatch in phpBB from this default phpBB profile fields array ... no update 
   		$phpbb_mismatch_default_profile_fields = true;
   }   
  } 

   if (! isset($phpbb_mismatch_default_profile_fields) ){
  
      if (!empty($u_url)){
 	     // phpBB version 3.2>
  	      if( $phpbb_version == '3.2' ){
  	        $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_location, pf_phpbb_youtube, pf_phpbb_twitter, pf_phpbb_googleplus, pf_phpbb_skype, pf_phpbb_facebook, pf_phpbb_icq, pf_phpbb_website, pf_phpbb_yahoo, pf_phpbb_aol)
            VALUES ('$uid','','','','','','','','','','$u_url','','') ON DUPLICATE KEY UPDATE pf_phpbb_website = '$u_url'");
           } else { // phpbb <3.2
        	     	   $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_facebook, pf_phpbb_googleplus, pf_phpbb_icq, pf_phpbb_location, pf_phpbb_skype, pf_phpbb_twitter, pf_phpbb_website, pf_phpbb_wlm, pf_phpbb_yahoo, pf_phpbb_youtube, pf_phpbb_aol)
                   VALUES ('$uid','','','','','','','','','$u_url','','','','') ON DUPLICATE KEY UPDATE pf_phpbb_website = '$u_url'");
                  }
       }
    }  
}

/* public static function phpbb_update_profile($user_id, $old_user_data) {

// TODO: maybe unify calls for usermeta queries

   global $wpdb,$w3all_config,$w3all_phpbb_lang_switch_yn;
  
   $phpbb_config_file = $w3all_config;
   $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
   $phpbb_config = unserialize(W3PHPBBCONFIG);
   $current_user = wp_get_current_user();
   if ( $current_user->ID == $user_id && defined('W3PHPBBUSESSION')) {
    $us = unserialize(W3PHPBBUSESSION);
    $uid = $us[0]->user_id;
   } else {
  	       $uid = $w3phpbb_conn->get_var("SELECT user_id FROM ".$phpbb_config_file["table_prefix"]."users WHERE username = '$wpu->user_login'"); // as there is not UID with the above query ... TODO change this
          }
          
   $wpu_db_um_tab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'usermeta' : $wpdb->prefix . 'usermeta';
 // there are '_new_email' for WP and x Buddypress 'pending_email_change' possible usermeta values (different!)
 // remove both if email existent in phpBB
$um = $wpdb->get_results("SELECT * FROM $wpu_db_um_tab WHERE user_id = '$user_id' AND meta_key = '_new_email' OR user_id = '$user_id' AND meta_key = 'pending_email_change'",ARRAY_A);
 if(!empty($um)){
 $pending_emails_h = '';
 $mv = array_column($um, 'meta_value');
  foreach($mv as $mve){
  	$mve = unserialize($mve);
  	$mveh = self::w3all_phpbb_email_hash($mve['newemail']);
  	$pending_emails_h  .= "'".$mveh."',";
   }
  $pe = substr($pending_emails_h, 0, -1);
  $peh = "IN(".$pe.")";
 }

  $wpu = get_user_by('ID', $user_id);
  
   if( $wpu === false ){ return; }

     $phpbb_version = substr($phpbb_config["version"], 0, 3);
     $phpbb_user_type = ( empty($wpu->roles) ) ? '1' : '0';
     $wpu->user_login = esc_sql($wpu->user_login); 
     $user_email_hash = self::w3all_phpbb_email_hash($wpu->user_email);    

   if ( is_multisite() ) {
// $wp_user_p_blog = get_user_meta($user_id, 'primary_blog', true);
// a normal user result with no capability in MU???
// how do we get user capabilities for users that not choose for a site on register?
// they not come with a role defined elsewhere seem to me ... TODO check this ...

// temp fix: set user type by the way as active in phpBB ... TODO check: may there is something that interfere
   $phpbb_user_type = 0;
  }
  
// switch install admin to check
 if(isset($peh)){ // via user profile: check if it is an email updated via user profile or by admin on user's admin page
  if($user_id == 1){ // if there is an email pending: remove after if another in phpBB found
      $phpbb_is_there_anybody = $w3phpbb_conn->get_results("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash ".$peh." AND user_id != '2'");
 	  } else {
 	  	$phpbb_is_there_anybody = $w3phpbb_conn->get_results("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash ".$peh." AND username != '$wpu->user_login'");   
 	  }	  
 	} else { // just check that there is not another same email in phpBB
 		 if($user_id == 1){ 
      $phpbb_is_there_anybody = $w3phpbb_conn->get_results("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' AND user_id != '2'");
 	  } else {
 	  	$phpbb_is_there_anybody = $w3phpbb_conn->get_results("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' AND username != '$wpu->user_login'");   
 	  }
 	}
 	    
 	    if ( isset($phpbb_is_there_anybody) && !empty($phpbb_is_there_anybody) ) { // skip admin install // revert // if there are usernames or email address, reset to old value and return error
       $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'users' : $wpdb->prefix . 'users';
	     // reset to the old email if existent in phpBB and associated to another user
	     $wpdb->query("UPDATE $wpu_db_utab SET user_email = '$old_user_data->user_email' WHERE ID = '$wpu->ID'");
        // delete pending email changes, in phpBB exist same email for some other user
       $wpdb->query("DELETE FROM $wpu_db_um_tab WHERE user_id = '$user_id' AND meta_key = '_new_email' OR user_id = '$user_id' AND meta_key = 'pending_email_change'");
        return true;
       
     }

  if($user_id == 1){ $uid = 2; } 
  if($uid < 2){ return; }
 
    // see also function phpbb_verify_credentials(

     $u_url = $wpu->user_url;
     $wp_umeta = get_user_meta($wpu->ID, '', false);

   		if( empty($wp_umeta['locale'][0]) ){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   //$wp_lang_x_phpbb = 'en'; // no lang setting, assume en
   		   	  if( strlen(get_locale()) == 2 ){ $wp_lang_x_phpbb = strtolower(get_locale()); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr(get_locale(), 0, strpos(get_locale(), '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     } 
   		} else {
   				if( strlen($wp_umeta['locale'][0]) == 2 ){ $wp_lang_x_phpbb = strtolower($wp_umeta['locale'][0]); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr($wp_umeta['locale'][0], 0, strpos($wp_umeta['locale'][0], '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     }
   			}		
   				// may switch for different languages notations
   				if( $wp_lang_x_phpbb == 'ps' ){ $wp_lang_x_phpbb = 'fa'; // persian
   					 }
   					 
          if(!isset($wp_lang_x_phpbb) OR empty($wp_lang_x_phpbb)){ $wp_lang_x_phpbb = 'en'; }

   				
      if( $w3all_phpbb_lang_switch_yn == 1 ){ // do not update lang if not activated option
      	  if($uid == 2){ // if phpBB id 2 not update the user_type
      		   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash', user_lang = '$wp_lang_x_phpbb' WHERE user_id = '2'");
      	   } else { // note: here the user_type in phpBB will be set as 2 for any user on reactivation, but may should be based on group that user belong, may 3 if admin and so on ...
                   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '$phpbb_user_type', user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash', user_lang = '$wp_lang_x_phpbb' WHERE user_id = '$uid'");
                  }
       } else {
       	       if($uid == 2){ // if phpBB id 2 not update the user_type
       	          $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash' WHERE user_id = '2'");
                } else { // note: here the user_type in phpBB will be set as 2 for any user on reactivation, but may should be based on group that user belong, may 3 if admin and so on ...
              	        $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '$phpbb_user_type', user_password = '$wpu->user_pass', user_email = '$wpu->user_email', user_email_hash = '$user_email_hash' WHERE user_id = '$uid'");
                       }
              }
              
// prevent error on update profiles fields, if field's number mismatch by these default arrays (default phpBB profile fields lists - phpBB 3.1 and 3.2 ) 

 if( $phpbb_version == '3.2' ){
  $default_phpbb_profile_fields_names = array("phpbb_location","phpbb_website","phpbb_interests","phpbb_occupation","phpbb_aol","phpbb_icq","phpbb_yahoo","phpbb_facebook","phpbb_twitter","phpbb_skype","phpbb_youtube","phpbb_googleplus");
} else {
  $default_phpbb_profile_fields_names = array("phpbb_location","phpbb_website","phpbb_interests","phpbb_occupation","phpbb_aol","phpbb_icq","phpbb_yahoo","phpbb_facebook","phpbb_twitter","phpbb_skype","phpbb_youtube","phpbb_googleplus","phpbb_wlm");
 }
  
  $phpbb_pf_cols = $w3phpbb_conn->get_results("SELECT field_name FROM ". $phpbb_config_file["table_prefix"] ."profile_fields");       

 if( count($phpbb_pf_cols) != count($default_phpbb_profile_fields_names) ){
	$phpbb_mismatch_default_profile_fields = true;
 }
   foreach($phpbb_pf_cols as $ppf_cols){
   	if( !in_array($ppf_cols->field_name, $default_phpbb_profile_fields_names) ){
   		// profile fields mismatch in phpBB from this default phpBB profile fields array ... no update 
   		$phpbb_mismatch_default_profile_fields = true;
   }   
  } 

   if (! isset($phpbb_mismatch_default_profile_fields) ){
  
      if (!empty($u_url)){
 	     // phpBB version 3.2>
  	      if( $phpbb_version == '3.2' ){
  	        $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_location, pf_phpbb_youtube, pf_phpbb_twitter, pf_phpbb_googleplus, pf_phpbb_skype, pf_phpbb_facebook, pf_phpbb_icq, pf_phpbb_website, pf_phpbb_yahoo, pf_phpbb_aol)
            VALUES ('$uid','','','','','','','','','','$u_url','','') ON DUPLICATE KEY UPDATE pf_phpbb_website = '$u_url'");
           } else { // phpbb <3.2
        	     	   $w3phpbb_conn->query("INSERT INTO ".$phpbb_config_file["table_prefix"]."profile_fields_data (user_id, pf_phpbb_interests, pf_phpbb_occupation, pf_phpbb_facebook, pf_phpbb_googleplus, pf_phpbb_icq, pf_phpbb_location, pf_phpbb_skype, pf_phpbb_twitter, pf_phpbb_website, pf_phpbb_wlm, pf_phpbb_yahoo, pf_phpbb_youtube, pf_phpbb_aol)
                   VALUES ('$uid','','','','','','','','','$u_url','','','','') ON DUPLICATE KEY UPDATE pf_phpbb_website = '$u_url'");
                  }
       }
    }  
}
*/
public static function w3_check_phpbb_profile_wpnu($username){

	if(empty($username)): return; endif;

 global $w3all_config,$wpdb;
 $phpbb_config_file = $w3all_config;
 $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
 
    $user_id = username_exists( $username );
    $user_info = get_userdata( $user_id );

 if(isset($_POST["log"])){

     $username = sanitize_user( $username, $strict = false ); 
 
         //////// phpBB username chars fix
         // If old phpBB usersnames are like myuse'name on WP_w3all integration, do not add into WP by default (of course this could be removed)
         // check for 2 more of these on this class.wp.w3all-phpbb.php in case will be necessary to add something or remove
  
         if ( preg_match('/[^-0-9A-Za-z _.@]/',$username) ){
	          echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>'.$phpbb_user_session[0]->username.'</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
           return;
         }

          $username = esc_sql($username);
          $phpbb_user = $w3phpbb_conn->get_results("SELECT u.*, g.* 
                                               FROM ". $phpbb_config_file["table_prefix"] ."users u, ". $phpbb_config_file["table_prefix"] ."groups g 
                                               WHERE u.username = '".$username."' 
                                               AND u.group_id != '1' AND u.group_id != '6' 
                                               AND u.group_id = g.group_id");            

      if(empty($phpbb_user)){ return; }
      if ( $phpbb_user[0]->user_id < 3 ){ // exclude the default phpBB install admin
      	return; 
		  }

// activated on phpBB?
if( $phpbb_user[0]->user_type != 1 && empty($user_info->wp_capabilities) ){ // re-activate this 'No role' WP user

  if ( is_multisite() ) {

	 	 $wpu_db_utab = (is_multisite()) ? WPW3ALL_MAIN_DBPREFIX . 'usermeta' : $wpdb->prefix . 'usermeta';
	 	 $subscriber = 'a:1:{s:10:"subscriber";b:1;}';
	   $wpdb->query("UPDATE $wpu_db_utab SET meta_value = '$subscriber' WHERE user_id = '$user_id' AND meta_key = 'wp_capabilities'");

	  
  } else {
	 	 // user should be re-activated with proper role maybe: subscriber as default as it is 
	 	 // here the db tab will be the one of the site, the user will login first (or come as logged)
	 	 // so not switch the db table prefix here
	 	    $wpu_db_utab = $wpdb->prefix . 'usermeta';
	 	    $subscriber = 'a:1:{s:10:"subscriber";b:1;}';
	      $wpdb->query("UPDATE $wpu_db_utab SET meta_value = '$subscriber' WHERE user_id = '$user_id' AND meta_key = 'wp_capabilities'");
	    }	   
	   
}

 if ( !is_multisite() ) {
 if(!empty($user_info)){ 	
  $wp_urole = implode(', ', $user_info->roles);
  if( $phpbb_user[0]->user_type == 1 && !empty($user_info->wp_capabilities) ){ // workaround for this new WP user, may still not active into phpBB
   $user_email_hash = self::w3all_phpbb_email_hash($user_info->user_email);
   $w3phpbb_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '0' WHERE user_email_hash = '$user_email_hash' OR username = '$username'");
  }
 }
}

	 if ( ! $user_id  && $phpbb_user[0]->user_type != 1 ) { 

     if ( $phpbb_user[0]->group_name == 'ADMINISTRATORS' ){
      	  $role = 'administrator';
      	} elseif ( $phpbb_user[0]->group_name == 'GLOBAL_MODERATORS' ){
          $role = 'editor';
        }  else { $role = 'subscriber'; }  // for all others phpBB Groups default to WP subscriber
    
              $userdata = array(
               'user_login'       =>  $phpbb_user[0]->username,
               'user_pass'        =>  $phpbb_user[0]->user_password,
               'user_email'       =>  $phpbb_user[0]->user_email,
               'user_registered'  =>  date_i18n( 'Y-m-d H:i:s', $phpbb_user[0]->user_regdate ),
               'role'             =>  $role
               );
               
      $user_id = wp_insert_user( $userdata );
      
   if(is_wp_error( $user_id )){
       echo '<h3>Error: '.$user_id->get_error_message().'</h3>' . '<h4><a href="'.get_edit_user_link().'">Return back</a><h4>';
           exit;
    } else {
    	   $user = get_user_by( 'ID', $user_id );
         self::phpBB_user_session_set($user);
     }  
  }	  
 }

}    


public static function wp_w3all_get_phpbb_user_info($username){ // email/user_object/username
	
 global $w3all_config;
 $phpbb_config_file = $w3all_config;
 $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
 
         //////// phpBB username chars fix          	   	
         // phpBB need to have users without characters like ' that is not allowed in WP as username by default
         // If old phpBB usernames are like myuser'name, do not add into WP
         // check for 2 more of these on this class.wp.w3all-phpbb.php in case you need to add something or remove
          
          if ( preg_match('/[^-0-9A-Za-z _.@]/',$username) ){
	          echo '<p style="padding:30px;background-color:#fff;color:#000;font-size:1.3em">Sorry, your <strong>registered username on our forum contain characters not allowed on this CMS system</strong>, you can\'t be added or login in this site side (and you\'ll see this message) until logged in on forums as <b>'.$phpbb_user_session[0]->username.'</b>. Please return back and contact the administrator reporting about this error issue. Thank you <input type="button" value="Go Back" onclick="history.back(-1)" /></p>';
           return;
         }

   $uname_email = is_email( $username ) ? 'user_email_hash' : 'username';

  if(is_email( $username )){ 
   $username = self::w3all_phpbb_email_hash($username);
  } elseif (is_object( $username )) {
  	$username = $username->user_login;
  } else { $username = $username; }

  $username = esc_sql($username);

   $phpbb_user_data = $w3phpbb_conn->get_results("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users 
   JOIN ". $phpbb_config_file["table_prefix"] ."groups ON ". $phpbb_config_file["table_prefix"] ."groups.group_id = ". $phpbb_config_file["table_prefix"] ."users.group_id
  AND ".$phpbb_config_file["table_prefix"]."users.".$uname_email." = '".$username."'");
  
 return $phpbb_user_data;
 
} 

public static function wp_w3all_phpbb_delete_user ($user_id){
	
 global $w3all_config;
 $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
 
// Only deactivate user in phpBB if deleted on WP
// TODO: switch to email hash only
 $user = get_user_by( 'ID', $user_id );
 $user->user_login = esc_sql($user->user_login);
 $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."users SET user_type = '1' WHERE username = '$user->user_login'");
// temp fix for signups remove (if the user had not ativate his account maybe)
 global $wpdb;
 // exist the signup table?
 $wpu_db_utab = $wpdb->prefix . 'signups';
 $wpdb->query("SHOW TABLES LIKE '$wpu_db_utab'");
  if($wpdb->num_rows > 0){
  $wpdb->query("DELETE FROM $wpu_db_utab WHERE user_login = '$user->user_login'");
  }
}
    

public static function wp_w3all_phpbb_delete_user_signup($user_id){
	
 global $w3all_config;
 $w3phpbb_conn = self::wp_w3all_phpbb_conn_init();
 
// Only deactivate user in phpBB if deleted on WP

 $user = get_user_by( 'ID', $user_id );
 $user_email_hash = self::w3all_phpbb_email_hash($user->user_email);
 $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."users SET user_type = '1' WHERE user_email_hash = '$user_email_hash' OR username = '$user->user_login'");
if ( is_multisite() ) { // clean also signup of this user if WPMU on delete for compatibility with integration
	// the check is done against an user that exist into users table, not signup
	// we can't leave the user into signup table, while not result in user tab: because in phpBB an user could register with same email another user in the while
global $wpdb;
// cleanup also form signup
$wpu_db_utab = $wpdb->prefix . 'signups';
$wpdb->query("DELETE FROM $wpu_db_utab WHERE user_email = '$user->user_email' OR user_login = '$user->user_login'");
}
}


 public static function wp_w3all_wp_after_pass_reset( $user ) { 
	
	 global $w3all_config,$w3all_phpbb_user_deactivated_yn;
	
	$w3db_conn = self::wp_w3all_phpbb_conn_init();
    
    $user_info = get_userdata($user->ID);
    $wp_user_role = implode(', ', $user_info->roles);

if ( $w3all_phpbb_user_deactivated_yn == 1 && !empty($wp_user_role) OR $w3all_phpbb_user_deactivated_yn != 1 ){

		$phpbb_user_data = self::wp_w3all_get_phpbb_user_info($user->user_email);

		if ( $phpbb_user_data[0]->user_type == 1 ) {
			$res = $w3db_conn->query("UPDATE ".$w3all_config["table_prefix"]."users SET user_type = '0' WHERE user_email_hash = '".$phpbb_user_data[0]->user_email_hash."' OR username = '".$user->user_login."'");
     }

  }
}

//#######################
// START SHORTCODEs for phpBB contents into WP
//#######################

// wp_w3all_get_phpbb_mchat_short vers 1.0 x phpBB mchat
public static function wp_w3all_feeds_short( $atts ) {
	global $w3all_custom_output_files;
if(is_array($atts)){
	$atts = array_map ('trim', $atts);
} else {
	return;
}
/* 
$w3feed_url
$w3feed_items_num
$w3feed_text_words[0|intval(num of words)|content]
$w3feed_ul_class
$w3feed_li_class
$w3feed_inline_style
$w3feed_href_blank
*/
/* if ( !function_exists( 'wp_simplepie_autoload' ) ) { 
  require_once ABSPATH . WPINC . '/class-simplepie.php'; // native simplepie lib
 } */
//return;
include_once( ABSPATH . WPINC . '/feed.php' );	
	
 $feed_v = shortcode_atts( array(
    'w3feed_url' => '',
    'w3feed_items_num' => '10',
    'w3feed_text_words' => 'content',
    'w3feed_ul_class' => '',
    'w3feed_li_class' => '',
    'w3feed_inline_style' => '',
    'w3feed_href_blank' => ''
  ), $atts );
  
 $w3feed_ul_class = $feed_v['w3feed_ul_class'];
 $w3feed_li_class = $feed_v['w3feed_li_class'];
 $w3feed_text_words = $feed_v['w3feed_text_words'];
 $w3feed_inline_style = $feed_v['w3feed_inline_style'];
 $w3feed_href_blank = $feed_v['w3feed_href_blank'];
 
 $w3feed_inline_style = (empty($w3feed_inline_style)) ? '' : ' style="'.$w3feed_inline_style.'"';
 $w3feed_href_blank = ($w3feed_href_blank > 0) ? ' target="_blank"' : '';

 if(parse_url($feed_v['w3feed_url']) == null){
 	 ob_start();
	  echo'Error: passed (feed) URL is not valid';
	 return ob_get_clean();
  } 

// Get a SimplePie feed object from the specified feed source.
$rss = fetch_feed( $feed_v['w3feed_url'] );

$maxitems = 0;
 if ( ! is_wp_error( $rss ) ) {
    // Figure out how many total items there are, but limit it to passed val. 
    $maxitems = $rss->get_item_quantity( intval($feed_v['w3feed_items_num']) ); 
    // Build an array of all the items, starting with element 0 (first element).
    $rss_items = $rss->get_items( 0, $maxitems );
  } else {
	 ob_start();
    echo $rss->get_error_message();
   return ob_get_clean();
  }
    
/*
// another way, without using any lib (not completed)
$cont = file_get_contents($feed_v['w3feed_url']);
$p = xml_parser_create();
xml_parse_into_struct($p, $cont, $vals, $index);
xml_parser_free($p);

$topics = $titles = array();
foreach($vals as $val){
	if(in_array("CONTENT", $val)) {
	$topics[] = $val;
}
}
*/
   
	 if( $w3all_custom_output_files == 1 ) {
     $file = ABSPATH . 'wp-content/plugins/wp-w3all-config/wp_w3all_feeds_short.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  } else {
		 $file = WPW3ALL_PLUGIN_DIR . 'views/wp_w3all_feeds_short.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  }
	  
}

// wp_w3all_get_phpbb_mchat_short vers 1.0 x phpBB mchat
public static function wp_w3all_get_phpbb_mchat_short( $atts ) {
	global $w3all_url_to_cms, $w3all_custom_output_files;
	
	$mch = shortcode_atts( array(
        'mchat_w3_toggle' => '0',
    ), $atts );
  $wp_w3all_mchat_shortmode = intval($mch['mchat_w3_toggle']) > 0 ? 1 : 0;
  $phpbb_conf = unserialize(W3PHPBBCONFIG);
	$dd = $phpbb_conf['cookie_domain'];
if(!empty($phpbb_conf['cookie_domain'])){
  $p = strpos($phpbb_conf['cookie_domain'], '.');
   if($p !== false && $p === 0){
	  $document_domain = substr($phpbb_conf['cookie_domain'], 1);
   } else {
   	  $document_domain = $phpbb_conf['cookie_domain'];
     }
} else {
	$document_domain = 'localhost';
}	
$phpbb_conf = '';
 
	 if( $w3all_custom_output_files == 1 ) {
     $file = ABSPATH . 'wp-content/plugins/wp-w3all-config/wp_w3all_phpbb_mchat_short.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  } else {
		 $file = WPW3ALL_PLUGIN_DIR . 'views/wp_w3all_phpbb_mchat_short.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  }
}

// wp_w3all_get_phpbb_lastopics_short vers 1.0 x single or specifics multiple forums
public static function wp_w3all_phpbb_last_topics_single_multi_fp_short( $atts ) {
	global $w3all_config,$w3all_get_topics_x_ugroup,$w3all_lasttopic_avatar_num,$w3all_last_t_avatar_yn,$w3all_last_t_avatar_dim,$w3all_get_phpbb_avatar_yn,$w3all_phpbb_widget_mark_ru_yn,$w3all_custom_output_files,$w3all_phpbb_widget_FA_mark_yn;
	$w3all_url_to_cms = get_option( 'w3all_url_to_cms' );
  $w3phpbb_conn = self::w3all_db_connect();
 if(is_array($atts)){
	$atts = array_map ('trim', $atts);
 } else {
	return;
 }
  
    $ltm = shortcode_atts( array(
        'forums_id' => '0', 
        'page_in' => '0', // not used
        'topics_number' => '0', 
        'post_text' => '0',
        'text_words' => '0',
        'w3_ul_class' => '',
        'w3_li_class' => '',
        'w3_inline_style' => '',
        'w3_href_blank' => '0'
    ), $atts );

    if( empty($ltm['forums_id']) OR preg_match('/[^[,0-9]/',$ltm['forums_id']) ){
    	echo'Specified parameter <i>forums_id</i> on Shortcode <i>w3allastopicforumsids</i> not found or contain wrong characters. w3all shortcode error.<br /> The shortcode need to be added like this:<br /><pre>[w3allastopicforumsids topics_number="5" forums_id="4,8"]</pre><br />change \'4,8\' <strong>with existent phpBB forums ID to display here (also a single one).</strong>';
    	return;
    }
    $topics_number = intval($ltm['topics_number']) > 0 ? intval($ltm['topics_number']) : 5; // 5 by default if not specified
    $wp_w3all_post_text = intval($ltm['post_text']) > 0 ? intval($ltm['post_text']) : 0;
    $wp_w3all_text_words = intval($ltm['text_words']) > 0 ? intval($ltm['text_words']) : 30;

    $w3_ul_class_ids = empty($ltm['w3_ul_class']) ? '' : $ltm['w3_ul_class'];
    $w3_li_class_ids = empty($ltm['w3_li_class']) ? '' : $ltm['w3_li_class'];
    $w3_inline_style_ids = empty($ltm['w3_inline_style']) ? '' : ' style="'.$ltm['w3_inline_style'].'"';
    $w3_href_blank_ids = intval($ltm['w3_href_blank'] > 0) ? ' target="_blank"' : '';
      
if($w3all_get_topics_x_ugroup == 1){ // list of allowed forums to retrieve topics if option active
  if (defined('W3PHPBBUSESSION')) {
   $us = unserialize(W3PHPBBUSESSION);
   $ug = $us[0]->group_id;
  } else {
	 $ug = 1; // the default phpBB guest user group
  }
 //$gaf = $w3phpbb_conn->get_results("SELECT DISTINCT forum_id FROM ".$w3all_config["table_prefix"]."acl_groups WHERE group_id = ".$ug." ORDER BY forum_id");
 $gaf = $w3phpbb_conn->get_results("SELECT DISTINCT ".$w3all_config["table_prefix"]."acl_groups.forum_id FROM ".$w3all_config["table_prefix"]."acl_groups 
  WHERE ".$w3all_config["table_prefix"]."acl_groups.auth_role_id != 16
  AND ".$w3all_config["table_prefix"]."acl_groups.group_id = ".$ug."");
  
  if(!empty($gaf)){
 	    $gf = '';
 	     foreach( $gaf as $v ){
        $gf .= $v->forum_id.',';
       }
    $gf = substr($gf, 0, -1);
    $topics_x_ugroup = "AND T.forum_id IN(".$gf.")";
   }} else {
	   $topics_x_ugroup = '';
    }
     
    /* $topics = $w3phpbb_conn->get_results("SELECT DISTINCT T.*, P.*, U.* FROM ".$w3all_config["table_prefix"]."topics AS T, ".$w3all_config["table_prefix"]."posts AS P, ".$w3all_config["table_prefix"]."users AS U 
         WHERE T.topic_visibility = 1  
         AND T.forum_id IN(".$ltm['forums_id'].") 
         AND T.topic_last_post_id = P.post_id 
         AND P.post_visibility = 1 
         ".$topics_x_ugroup."
         AND U.user_id = T.topic_last_poster_id 
         GROUP BY P.topic_id
         ORDER BY T.topic_last_post_time DESC LIMIT 0,$topics_number");
     */
     // query improvement by @reloadgg // see https://www.axew3.com/w3/forums/viewtopic.php?f=2&t=850
   $topics = $w3phpbb_conn->get_results("SELECT T.*, P.*, U.* 
    FROM ".$w3all_config["table_prefix"]."topics AS T
    JOIN ".$w3all_config["table_prefix"]."posts AS P on (T.topic_last_post_id = P.post_id and T.forum_id = P.forum_id) 
    JOIN ".$w3all_config["table_prefix"]."users AS U on U.user_id = T.topic_last_poster_id 
    WHERE T.topic_visibility = 1 
    AND T.forum_id IN(".$ltm['forums_id'].") 
    ".$topics_x_ugroup." 
    AND P.post_visibility = 1
    ORDER BY T.topic_last_post_time DESC
    LIMIT 0,$topics_number");

	   $last_topics = is_array($topics) && !(empty($topics)) ? $topics : array();
    
   if ( $w3all_phpbb_widget_mark_ru_yn == 1 && is_user_logged_in() ) {
   	// $username = true is passed/used here to avoid the define Constant W3UNREADTOPICS, already defined for widgets and shortcodes that follow another flow
    // ... there is nothing to define in this case ... we hope there is not more than one shortcode instance on same page!
   	$phpbb_unread_topics = self::w3all_get_unread_topics($username = true, '', '', $topics_number, 0);
    $phpbb_unread_topics = empty($phpbb_unread_topics) ? array() : unserialize($phpbb_unread_topics);
   } 

	 if( $w3all_custom_output_files == 1 ) {
     $file = ABSPATH . 'wp-content/plugins/wp-w3all-config/phpbb_last_topics_forums_ids_shortcode.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  } else {
		 $file = WPW3ALL_PLUGIN_DIR . 'views/phpbb_last_topics_forums_ids_shortcode.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  }
}

// wp_w3all_get_phpbb_lastopics_short vers 1.0
public static function wp_w3all_get_phpbb_lastopics_short( $atts ) {
	global $w3all_lasttopic_avatar_num,$w3all_last_t_avatar_yn,$w3all_last_t_avatar_dim,$w3all_get_phpbb_avatar_yn,$w3all_phpbb_widget_mark_ru_yn,$w3all_custom_output_files,$w3all_phpbb_widget_FA_mark_yn;
	$w3all_url_to_cms = get_option( 'w3all_url_to_cms' );
  if(is_array($atts)){
	$atts = array_map ('trim', $atts);
} else {
	return;
}
    $ltm = shortcode_atts( array(
        'mode' => '0', 
        'topics_number' => '0', 
        'post_text' => '0',
        'text_words' => '0',
        'w3_ul_class' => '',
        'w3_li_class' => '',
        'w3_inline_style' => '',
        'w3_href_blank' => ''
    ), $atts );

    $mode = intval($ltm['mode']) > 0 ? 0 : 0; // not used at moment ... 
    $topics_number = intval($ltm['topics_number']) > 0 ? intval($ltm['topics_number']) : 0;
    $wp_w3all_post_text = intval($ltm['post_text']) > 0 ? intval($ltm['post_text']) : 0;
    $wp_w3all_text_words = intval($ltm['text_words']) > 0 ? intval($ltm['text_words']) : 0;
    
    $w3_ul_class_lt = empty($ltm['w3_ul_class']) ? '' : $ltm['w3_ul_class'];
    $w3_li_class_lt = empty($ltm['w3_li_class']) ? '' : $ltm['w3_li_class'];
    $w3_inline_style_lt = empty($ltm['w3_inline_style']) ? '' : $ltm['w3_inline_style'];
    $w3_href_blank_lt = $ltm['w3_href_blank'] > 0 ? ' target="_blank"' : ''; // not used at moment

   if ( $w3all_phpbb_widget_mark_ru_yn == 1 && is_user_logged_in() ) {
   	if (defined("W3UNREADTOPICS")){
     $phpbb_unread_topics = unserialize(W3UNREADTOPICS);
    } 
   }
    
   if (defined("W3PHPBBLASTOPICS")){
   	$last_topics = unserialize(W3PHPBBLASTOPICS); // see wp_w3all.php
  } else {
	 $last_topics =	WP_w3all_phpbb::last_forums_topics_res($topics_number);
	}
	
	 if( $w3all_custom_output_files == 1 ) {
     $file = ABSPATH . 'wp-content/plugins/wp-w3all-config/phpbb_last_topics_output_shortcode.php';
		 ob_start(); 
		  include($file);
		 return ob_get_clean();
	  } else {
		 $file = WPW3ALL_PLUGIN_DIR . 'views/phpbb_last_topics_output_shortcode.php';
	   ob_start();
	    include( $file );
	   return ob_get_clean();
	  }
}

// wp_w3all_get_phpbb_lastopics_short_wi vers 1.0 (with images)
// retrieve for each post/topic, the first topic's post img attach to display into a grid
// NOTE: as is the query the result will contain only topics with almost an attach inside on one of their posts:
// only the first (time based) inserted, will be retrieved to display
public static function wp_w3all_get_phpbb_lastopics_short_wi( $atts ) {
	global $w3all_config,$wp_w3all_forum_folder_wp,$w3all_lasttopic_avatar_num,$w3all_last_t_avatar_yn,$w3all_last_t_avatar_dim,$w3all_get_phpbb_avatar_yn,$w3all_phpbb_widget_mark_ru_yn,$w3all_custom_output_files,$w3all_phpbb_widget_FA_mark_yn,$w3all_get_topics_x_ugroup;
	 $phpbb_config = unserialize(W3PHPBBCONFIG);
   $w3db_conn = self::w3all_db_connect();
	 $w3all_url_to_cms = get_option( 'w3all_url_to_cms' );
   $atts = array_map ('trim', $atts);
   
    $ltm = shortcode_atts( array(
        'cat_id' => '0', 
        'topics_number' => '0', 
        'post_text' => '0',
        'text_words' => '0',
        'columns_number' => '2',
        'gap_columns' => '0',
    ), $atts );

    $cat_id = intval($ltm['cat_id']) > 0 ? intval($ltm['cat_id']) : 0;
    $topics_number = intval($ltm['topics_number']) > 0 ? intval($ltm['topics_number']) : 5;
    $wp_w3all_post_text = intval($ltm['post_text']) > 0 ? intval($ltm['post_text']) : 0;
    $wp_w3all_text_words = intval($ltm['text_words']) > 0 ? intval($ltm['text_words']) : 5;
    $wp_w3all_columns_number = intval($ltm['columns_number']) > 1 ? intval($ltm['columns_number']) : 2; // minimum 2 ... as code is on views/phpbb_last_topics_withimage_output_shortcode.php
    $wp_w3all_gap_columns = intval($ltm['gap_columns']) > 1 ? intval($ltm['gap_columns']) : 0; // gap space between columns, after calculated in %
    
   if ( $w3all_phpbb_widget_mark_ru_yn == 1 && is_user_logged_in() ) {
   	if (defined("W3UNREADTOPICS")){
     $phpbb_unread_topics = unserialize(W3UNREADTOPICS);
    } 
   }
   
if( $w3all_get_topics_x_ugroup == 1 ){
	if (defined('W3PHPBBUSESSION')) {
   $us = unserialize(W3PHPBBUSESSION);
   $ug = $us[0]->group_id;
  } else {
	$ug = 1; // the default phpBB guest user group
}
//$gaf = $w3db_conn->get_results("SELECT DISTINCT forum_id FROM ".$w3all_config["table_prefix"]."acl_groups WHERE group_id = ".$ug." ORDER BY forum_id");
  $gaf = $w3db_conn->get_results("SELECT DISTINCT ".$w3all_config["table_prefix"]."acl_groups.forum_id FROM ".$w3all_config["table_prefix"]."acl_groups 
  WHERE ".$w3all_config["table_prefix"]."acl_groups.auth_role_id != 16
  AND ".$w3all_config["table_prefix"]."acl_groups.group_id = ".$ug."");
 if(empty($gaf)){
	 return array(); // no forum found that can show topics for this group ... 
 } else { 
 	  $gf = '';
 	    foreach( $gaf as $v ){
    $gf .= $v->forum_id.',';
   }
   $gf = substr($gf, 0, -1);
   $topics_x_ugroup = "AND ". $w3all_config["table_prefix"] ."topics.forum_id IN(".$gf.")";
 }
}
 else {
	$topics_x_ugroup = '';
}

 $last_topics = $w3db_conn->get_results("SELECT * FROM  ". $w3all_config["table_prefix"] ."posts
  JOIN ". $w3all_config["table_prefix"] ."topics ON ". $w3all_config["table_prefix"] ."topics.topic_id = ". $w3all_config["table_prefix"] ."posts.topic_id 
   AND ". $w3all_config["table_prefix"] ."topics.topic_visibility = 1
   AND ". $w3all_config["table_prefix"] ."topics.topic_last_post_id = ". $w3all_config["table_prefix"] ."posts.post_id
   ".$topics_x_ugroup."
  JOIN ". $w3all_config["table_prefix"] ."forums ON ". $w3all_config["table_prefix"] ."forums.parent_id =  '".$cat_id."' 
   AND ". $w3all_config["table_prefix"] ."topics.forum_id = ". $w3all_config["table_prefix"] ."forums.forum_id
  JOIN ". $w3all_config["table_prefix"] ."attachments
   WHERE ". $w3all_config["table_prefix"] ."attachments.attach_id = (SELECT MIN(attach_id) FROM ". $w3all_config["table_prefix"] ."attachments WHERE ". $w3all_config["table_prefix"] ."attachments.topic_id = ". $w3all_config["table_prefix"] ."topics.topic_id)
  ORDER BY ". $w3all_config["table_prefix"] ."posts.post_time DESC LIMIT 0,$topics_number");

if ( count($last_topics) < 2 ) { echo 'Almost two topics with attachments required to display from choosen forums!'; return; }

	 if( $w3all_custom_output_files == 1 ) {
     $file = ABSPATH . 'wp-content/plugins/wp-w3all-config/phpbb_last_topics_withimage_output_shortcode.php';
		ob_start();
		  include($file);
		return ob_get_clean();
	  } else {
		 $file = WPW3ALL_PLUGIN_DIR . 'views/phpbb_last_topics_withimage_output_shortcode.php';
		ob_start(); 
	    include( $file );
	  return ob_get_clean();
	  }
}

// wp_w3all_get_phpbb_post_short Version 1.0
// This need to be rewrite/improved: all should be done following the [code][code] logic ...
public static function wp_w3all_get_phpbb_post_short( $atts ) {
	global $w3all_config;
	$w3db_conn = self::w3all_db_connect();
	
    $p = shortcode_atts( array(
        'id' => '0', 
        'plaintext' => '0', 
    ), $atts );
    
$p['id'] = intval($p['id']);
if($p['id'] == 0){
	return "w3all shortcode error.<br /> The shortcode need to be added like this:<br />[w3allforumpost id=\"150\"]<br />change '150' <strong>with the (existent) phpBB post ID to display here.</strong>"; 
}

$phpbb_post = $w3db_conn->get_results("SELECT T.*, P.* FROM ".$w3all_config["table_prefix"]."topics AS T, ".$w3all_config["table_prefix"]."posts AS P 
  WHERE T.topic_visibility = 1 
   AND T.topic_id = P.topic_id 
   AND P.post_visibility = 1 
   AND P.post_id = '".$p['id']."'
   ");

if( !$phpbb_post ){
	$res = '<b>w3all shortcode error:<br />the provided post ID to show do not match an existent phpBB post!</b>';
	return $res;
}
   
$p['plaintext'] = intval($p['plaintext']);
if($p['plaintext'] == 1){
	return preg_replace('/[[\/\!]*?[^\[\]]*?]/', '', $phpbb_post[0]->post_text); // REVIEW // remove all bbcode tags (not html nor fake tags) //
}

// handle bbcode [code]...[/code] in the right way ...
// the code is grabbed and replaced by a placeholder '#w3#bbcode#replace#' 
// so, after the others bbcode tags conversions, re-added 
preg_match_all('~\<s\>\[code\]\</s\>(.*?)\<e\>\[/code\]\</e\>~si', $phpbb_post[0]->post_text, $cmatches, PREG_SET_ORDER);
if($cmatches){ // remove and add custom placeholder
$cc = 0;
$phpbb_post[0]->post_text = preg_replace('~\<s\>\[code\]\</s\>(.*?)\<e\>\[/code\]\</e\>~si', '#w3#bbcode#replace#', $phpbb_post[0]->post_text, -1 ,$cc);
// split, add 'placeholders'
$ps = preg_split('/#w3#bbcode#replace#/', $phpbb_post[0]->post_text, -1, PREG_SPLIT_DELIM_CAPTURE);
$ccc = 0;
$res = '';
foreach($ps as $p => $s){
if($ccc < $cc){
 $res .= $s.'#w3#bbcode#replace#'.$ccc++; // append/assing number to placeholder for this split/string
} else { $res .= $s; } // follow add the latest text, if no more placeholders ...
}
} else { $res = $phpbb_post[0]->post_text; }

$res = self::w3all_bbcodeconvert($res); // convert all bbcode tags except [code]

if($cmatches){ // re-add grabbed bbcode blocks and wrap with proper html ...
$cccc = 0;
foreach($cmatches as $k => $v){
$res = str_ireplace('#w3#bbcode#replace#'.$cccc, '<code>'.$v[1].'</code>', $res);
$cccc++;
}
}

return $res;
}

public static function w3all_bbcodeconvert($text) {
	// a default (+- complete) phpBB bbcode array
	$find = array(
		'~\[b\](.*?)\[/b\]~usi',
		'~\[i\](.*?)\[/i\]~usi',
		'~\[u\](.*?)\[/u\]~usi',
		'~\[quote\](.*?)\[/quote\]~usi',
		'~\[size=(.*?)\](.*?)\[/size\]~usi',
		'~\[color=(.*?)\](.*?)\[/color\]~usi',
		'~\[url\](.*?)\[/url\]~s',
		'~\[url=(.*?)\](.*?)\[/url\]~s', // text url
		'~\[img\](http|https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~usi',
		'~\[media\](.*?)\[/media\]~usi', // media: see https://www.phpbb.com/customise/db/extension/mediaembed/
		'~<[/]?[r|s|e]>~usi', // no conversion, remove 
		'~<[/]?[color][^>]+?>~usi', // no conversion, remove
    '~\[img\].*?\[/img\]~usi', // image link remove // // REVIEW THIS
    '~(^(\r\n|\r|\n))|^\s*$~m', // replace an empty line with <br /> // REVIEW THIS
// start ul/ol lists
    '~\[list\](.*?)\[/list\]~usi', // ul unordered/list
    '~\[list=(1){1}\](.*?)\[/list\]~ums', // ol lists // decimal list
    '~\[list=(a){1}\](.*?)\[/list\]~ums', // ol lists // lower-alpha list
    '~\[list=(A){1}\](.*?)\[/list\]~ums', // ol lists // upper-alpha list
    '~\[list=(i){1}\](.*?)\[/list\]~ums', // ol lists // lower-roman list
    '~\[list=(I){1}\](.*?)\[/list\]~ums' // ol lists // upper-roman list

	);
// html BBcode replaces
	$replace = array(
		'<b>$1</b>',
		'<span style="font-style: italic;">$1</span>',
		'<span style="text-decoration:underline;">$1</span>',
		'<blockquote style="font-style: italic;">$1</blockquote>',
		'<span style="font-size:$1%;">$2</span>', // % here
		'<span style="color:$1;">$2</span>',
		'<a href="$1">$1</a>',
		'<a href="$1">$2</a>', // text url
		'<img src="$1" alt="" />',
		'[wpw3allmediaconvert]$1[wpw3allmediaconvert]',
		'',
		'',
		'',
		'<br />',
// start ul/ol lists
		'<ul>$1</ul>',
		'<ol style="list-style-type: decimal">$2</ol>',
		'<ol style="list-style-type: lower-alpha">$2</ol>',
		'<ol style="list-style-type: upper-alpha">$2</ol>',
		'<ol style="list-style-type: lower-roman">$2</ol>',
		'<ol style="list-style-type: upper-roman">$2</ol>'
	);


$text = preg_replace($find, $replace, $text, PREG_OFFSET_CAPTURE);

$text = preg_replace_callback(
            "~<ul>(.*?)</ul>|<ol(.*?)</ol>~sm",
            "self::w3_bbcode_rep0",
            $text);
 
$text = preg_replace_callback(
            "~\[wpw3allmediaconvert\](.*?)\[wpw3allmediaconvert\]~sm",
            "self::w3_bbcode_media",
            $text);

	return $text;
}

public static function w3_bbcode_media($vmatches)
{
	// seem to work in few lines ... of course can be improved or done even better
 $vmatches[0] = str_replace('[wpw3allmediaconvert]', '', $vmatches[0]);
 $pos = strpos($vmatches[0], '">');
 $vmatches[0] = substr($vmatches[0], $pos+2);
 $vmatches[0] = str_replace('</URL>', '', $vmatches[0]);
 $vmatches[0] = wp_oembed_get($vmatches[0]);
return $vmatches[0];
}

public static function w3_bbcode_rep0($matches)
{
  $matches[0] = preg_replace('~\[\*\]~', '<li>', $matches[0]); // to be improved ... but work as is
  return $matches[0];
}

//#######################
// END SHORTCODE for phpBB POSTS into WP 
//#######################

//#######################
// START ABOUT AVATARS
//#######################

// from 1.6.7 switch to search in phpBB as email hash that is Index Key into phpBB db users table

public static function wp_w3all_assoc_phpbb_wp_users() {
	 
	global $w3all_get_phpbb_avatar_yn, $w3all_last_t_avatar_yn, $w3all_lasttopic_avatar_num;
	$w3all_avatars_yn = $w3all_get_phpbb_avatar_yn == 1 ? true : false;
	
    $nposts = get_option( 'posts_per_page' );

 $post_list = get_posts( array(
    'user_id',
    'numberposts'    => $nposts,
    'sort_order' => 'desc',
    'post_status' => 'publish'
  ) );
  
    foreach ( $post_list as $post ) {
    	
     $uname = get_user_by('ID', $post->post_author);
     $p_unames[] = self::w3all_phpbb_email_hash($uname->user_email);
   
     $comments = get_comments( array( 'post_id' => $post->ID ) );
      foreach ( $comments as $comment ) :
       if ( $comment->user_id > 0 ):
        $p_unames[] = self::w3all_phpbb_email_hash($comment->comment_author_email);
       endif;
      endforeach;
   }
   
// add the current user
// if any other condition fail assigning avatars to users, add it here

   // add current user
   $current_user = wp_get_current_user();
   if ($current_user->ID > 0){
   $p_unames[] = self::w3all_phpbb_email_hash($current_user->user_email);
  }
 
  // add usernames for last topics widget, if needed
 if ( $w3all_avatars_yn ) : 
 if (defined("W3PHPBBLASTOPICS")){
   	$w3all_last_posts_users = unserialize(W3PHPBBLASTOPICS); // see also wp_w3all.php
  } else {
    $w3all_last_posts_users =	self::last_forums_topics($w3all_lasttopic_avatar_num);
    $t = is_array($w3all_last_posts_users) ? serialize($w3all_last_posts_users) : serialize(array());
   define( "W3PHPBBLASTOPICS", $t ); // avoid more calls if more than 1 widget
  }
 
   if(!empty($w3all_last_posts_users)):
      foreach ( $w3all_last_posts_users as $post_uname ) :
       //$pun = esc_sql($post_uname->user_email_hash);
       $p_unames[] = $post_uname->user_email_hash;
      endforeach;
   endif;
 endif;

  $w3_un_results = array_unique($p_unames); 

$query_un ='';
   foreach($w3_un_results as $w3_unames_ava)
   {
    	$query_un .= '\''.$w3_unames_ava.'\',';
   }

 $query_un = substr($query_un, 0, -1);
      
 $w3all_u_ava_urls = self::w3all_get_phpbb_avatars_url($query_un);
 
 if (!empty($w3all_u_ava_urls)){

    foreach( $w3all_u_ava_urls as $ava_set_x ){
   
  if($ava_set_x['puid'] == 2){ // switch if install admins (uid 1 WP - uid 2 phpBB) have different usernames
   	$usid = get_user_by('ID', 1);
   } else { $usid = get_user_by('login', $ava_set_x['uname']); }
    	
    	if($usid):
      	$wp_user_phpbb_avatar[] = array("wpuid" => $usid->ID, "phpbbavaurl" => $ava_set_x['uavaurl'], "phpbbuid" => 0);
      else:
      	$wp_user_phpbb_avatar[] = array("wpuid" => 0, "phpbbavaurl" => $ava_set_x['uavaurl'], "phpbbuid" => $ava_set_x['puid']);
      endif;
  }
} else { $w3all_u_ava_urls = array(); }

 $wp_userphpbbavatar = (isset($wp_user_phpbb_avatar)) ? $wp_user_phpbb_avatar : $w3all_u_ava_urls;
  $u_a = serialize($wp_userphpbbavatar);
   define("W3ALLPHPBBUAVA", $u_a);
  return $wp_userphpbbavatar;

}


public static function w3all_get_phpbb_avatars_url( $w3unames ) {
   global $w3all_config, $w3all_avatar_via_phpbb_file;
  $config = $w3all_config;
  $w3db_conn = self::w3all_db_connect();
	$phpbb_config = unserialize(W3PHPBBCONFIG);
  $uavatars = $w3db_conn->get_results( "SELECT user_id, username, user_avatar, user_avatar_type FROM ".$config["table_prefix"]."users WHERE user_email_hash IN(".$w3unames.") ORDER BY user_id DESC" );

  if(!empty($uavatars)){

   	foreach($uavatars as $user_ava) {
     	
     if(!empty($user_ava->user_avatar)){ // has been selected above by the way, check it need to be added
     	
     		if ( $user_ava->user_avatar_type == 'avatar.driver.local' ){
     			
     			$phpbb_avatar_url = get_option( 'w3all_url_to_cms' ) . '/' . $phpbb_config["avatar_gallery_path"] . '/' . $user_ava->user_avatar;
     			$u_a[] = array("puid" => $user_ava->user_id, "uname" => $user_ava->username, "uavaurl" => $phpbb_avatar_url);
     		
     		}  elseif ( $user_ava->user_avatar_type == 'avatar.driver.remote' ){
     			$phpbb_avatar_url = $user_ava->user_avatar;
     			$u_a[] = array("puid" => $user_ava->user_id, "uname" => $user_ava->username, "uavaurl" => $phpbb_avatar_url);
     		
     		} else {
 	         $avatar_entry = $user_ava->user_avatar;
            $ext = substr(strrchr($avatar_entry, '.'), 1);

           $avatar_entry = strtok($avatar_entry, '_');
           $phpbb_avatar_filename = $phpbb_config["avatar_salt"] . '_' . $avatar_entry . '.' . $ext; 
           if ( $w3all_avatar_via_phpbb_file == 0 ){ // by @Alexvrs
             $phpbb_avatar_url = get_option( 'w3all_url_to_cms' ).'/'.$phpbb_config["avatar_path"].'/'.$phpbb_avatar_filename;
            } else { 
          	 $phpbb_avatar_url = get_option( 'w3all_url_to_cms' ) . "/download/file.php?avatar=" . $avatar_entry . '.' . $ext;
    	     }
    	// in phpBB there is Gravatar as option available as profile image
    	// so if it is the case, the user at this point can have an email address, instead than an image url as value
      // $pemail = '/^.*@[-a-z0-9]+\.+[-a-z0-9]+[\.[a-z0-9]+]?/';
      // preg_match($pemail, $user_ava->user_avatar, $url_email);
      // $phpbb_avatar_url = (empty($url_email)) ? $phpbb_avatar_url : $user_ava->user_avatar;
       
        $phpbb_avatar_url = ( is_email( $user_ava->user_avatar ) !== false ) ? $user_ava->user_avatar : $phpbb_avatar_url;
        //$u_a[] = array("puid" => $user_ava->user_id, "uname" => $user_ava->username, "uavaurl" => $phpbb_avatar_url);
        $u_a[] = array("puid" => $user_ava->user_id, "uname" => $user_ava->username, "uavaurl" => $phpbb_avatar_url);
      
      } 
     } 
    } 
  } else { $u_a = ''; }
  	$u_a = (empty($u_a)) ? '' : $u_a;
  return $u_a;
}


public static function wp_w3all_phpbb_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

//$uids_urls = self::wp_w3all_assoc_phpbb_wp_users();
$uids_urls = unserialize(W3ALLPHPBBUAVA);

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );	
    }

 	if ( isset($user) && $user && is_object( $user ) ) {
     if (!empty($uids_urls)){

       foreach($uids_urls as $w3all_wupa) {
       	if(isset($w3all_wupa["phpbbavaurl"])){
         //could be an email, get so gravatar url if the case
          if( is_email( $w3all_wupa["phpbbavaurl"] ) !== false ) {
           $w3all_wupa["phpbbavaurl"] = get_avatar_url( $w3all_wupa["phpbbavaurl"] );
          } 

          if ( $user->data->ID == $w3all_wupa["wpuid"] ) {
           	  $avatar = $w3all_wupa["phpbbavaurl"];
              $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
         } 
        }
       }
      } 
 }
   
    return $avatar;
}

public static function init_w3all_avatars(){
	self::wp_w3all_assoc_phpbb_wp_users();
	// try to avoid avatars examples shown all the same, as it is the viewing admin avatar, in /wp-admin/options-discussion.php
	// in change this user when view the page /wp-admin/options-discussion.php, will not see his phpBB avatar on top admin bar ... that can be acceptable
	if ( ! empty($_SERVER['REQUEST_URI']) && ! strpos($_SERVER['REQUEST_URI'], 'options-discussion.php') ){
	 add_filter( 'get_avatar', array( 'WP_w3all_phpbb', 'wp_w3all_phpbb_custom_avatar' ), 10 , 5  );
  } elseif ( empty($_SERVER['REQUEST_URI']) ) {
  	add_filter( 'get_avatar', array( 'WP_w3all_phpbb', 'wp_w3all_phpbb_custom_avatar' ), 10 , 5  );
   }
}

//////////////////////////////////
// START avatars ONLY X BUDDYPRESS
//////////////////////////////////

// The call to this has been added into wp_w3all-php: add_filter( 'bp_core_fetch_avatar' ...

public static function w3all_bp_core_fetch_avatar( $bp_img_element, $params, $params_item_id, $params_avatar_dir, $html_css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir ) { 
    
    $uids_urls = unserialize(W3ALLPHPBBUAVA);
     return $bp_img_element;
 if ( !empty($uids_urls) ){
   	// assign phpBB avatar to this user, if there is one
    foreach($uids_urls as $w3all_wupa) {
    	if( is_email( $w3all_wupa["phpbbavaurl"] ) !== false ) {
           $w3all_wupa["phpbbavaurl"] = get_avatar_url( $w3all_wupa["phpbbavaurl"] );
          }
    	if ( $params_item_id == $w3all_wupa["wpuid"] ) {
           	$avatar_url = $w3all_wupa["phpbbavaurl"];
           $bp_img_element = '<img src="'.$avatar_url.'" class="'.$params["class"].'" width="'.$params["width"].'" height="'.$params["height"].'" alt="'.$params["alt"].'" />';
         } 
     }
     
 }  
  return $bp_img_element; // or let go with the one assigned by BP
         
}
/////////////////////////////////
// END avatars ONLY X BUDDYPRESS
/////////////////////////////////

//#######################
// END ABOUT AVATARS
//#######################

public static function w3all_get_phpbb_config_res() {
	
    $res = self::w3all_get_phpbb_config();
    return $res;
 } 

public static function create_phpBB_user_res($wpu) {
	
    $res = self::create_phpBB_user($wpu);
    return $res;
 }  

public static function phpBB_user_session_set_res($wp_user_data){
	
      $res = self::phpBB_user_session_set($wp_user_data);                                 
	   return $res; 
}


public static function phpbb_pass_update_res($user, $new_pass){
	
      $res = self::phpbb_pass_update($user, $new_pass);                                 
	   return $res; 
}


public static function last_forums_topics_res($ntopics){
	
      $topics_display = self::last_forums_topics($ntopics);                                 
	   return $topics_display; 
}

public static function wp_w3all_phpbb_conn_init() {
	
        	$w3db_conn = self::w3all_db_connect();	
        	return $w3db_conn;
	}

//############################################
// START PHPBB TO WP FUNCTIONS
//############################################

public static function phpBB_password_hash($pass){

 // phpBB 3.1> require bcrypt() with a min cost of 10
   require_once( WPW3ALL_PLUGIN_DIR . '/addons/bcrypt/bcrypt.php');
    $pass = htmlspecialchars(trim($pass));
    $hash = w3_Bcrypt::hashPassword($pass, 10);
   return $hash;
}

// wp_w3all custom -> phpBB get_unread_topics() ... this should fit any needs about users read/unread topics/posts.
// using it only for WP registered users, as option to be activated on w3all_config admin page. Retrieve read/unread posts in phpBB for WP current user
// $sql_limit = wp_w3all last topics numb to retrieve option
// original function in phpBB: get_unread_topics($username = false, $sql_extra = '', $sql_sort = '', $sql_limit = 1001, $sql_limit_offset = 0) // the phpBB function into functions.php file

public static function w3all_get_unread_topics($username = false, $sql_extra = '', $sql_sort = '', $sql_limit = 1001, $sql_limit_offset = 0)
{    
	// if passed var $username is true, then this call is done by phpbb_last_topics_single_multi_shortcode.php
	// $username is used here so, to switch, and not execute
	// define( "W3UNREADTOPICS", $unread_topics ); 
	// or get Notice: Constant W3UNREADTOPICS already defined
        global $w3all_config,$w3all_lasttopic_avatar_num;
        // NOTE this: guess an user have setup a value for 'Last Forums Topics number of users's avatars to retrieve' on wp_w3all config: if not, we'll search until 50 by default. If a widget need to display more than 50 posts and no avatar option is active, than this value need to be changed here directly, or setting up the option value for 'Last Forums Topics number of users's avatars to retrieve' even if avatars aren't used
        $sql_limit = empty($w3all_lasttopic_avatar_num) ? 50 : $w3all_lasttopic_avatar_num;

        $phpbb_config_file = $w3all_config;
	      $w3phpbb_conn = self::w3all_db_connect();
        $phpbb_config = unserialize(W3PHPBBCONFIG);
        $user = wp_get_current_user();
        if ($user->ID < 1){ return false; } // only for WP logged in users

if (defined('W3PHPBBUSESSION')) {
   $us = unserialize(W3PHPBBUSESSION);
   $user_id = $us[0]->user_id;
   $last_mark = $us[0]->user_lastmark; // when/if the user have mark all as read
  } else {
    $user_email_hash = self::w3all_phpbb_email_hash($user->user_email);
    $phpbb_u = $w3phpbb_conn->get_row("SELECT * FROM ".$phpbb_config_file["table_prefix"]."users WHERE user_email_hash = '$user_email_hash' OR username = '$user->user_login'") ;
    $user_id = $phpbb_u->user_id;
    $last_mark = $phpbb_u->user_lastmark; // when/if the user have mark all as read
  }

	// Data array we're going to return
	$unread_topics = array();

	if (empty($sql_sort))
	{
		$sql_sort = "ORDER BY ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_time DESC, ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_id DESC";
	}

	//if ($config['load_db_lastread'] && $user->data['is_registered']) // wp_w3all config active or not, and user logged or not. At moment all this is not necessary:
	if($user_id > 0)
	{     
		// Get list of the unread topics
		
	 $w3all_exec_sql_array = $w3phpbb_conn->get_results("SELECT ".$phpbb_config_file["table_prefix"]."topics.topic_id, ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_time, ".$phpbb_config_file["table_prefix"]."topics_track.mark_time as topic_mark_time, ".$phpbb_config_file["table_prefix"]."forums_track.mark_time as forum_mark_time 
     FROM ".$phpbb_config_file["table_prefix"]."topics 
      LEFT JOIN ".$phpbb_config_file["table_prefix"]."topics_track 
        ON ".$phpbb_config_file["table_prefix"]."topics_track.user_id = '".$user_id."' 
       AND ".$phpbb_config_file["table_prefix"]."topics.topic_id = ".$phpbb_config_file["table_prefix"]."topics_track.topic_id 
      LEFT JOIN ".$phpbb_config_file["table_prefix"]."forums_track 
        ON ".$phpbb_config_file["table_prefix"]."forums_track.user_id = '".$user_id."' AND ".$phpbb_config_file["table_prefix"]."topics.forum_id = ".$phpbb_config_file["table_prefix"]."forums_track.forum_id
     WHERE ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_time > '".$last_mark."' 
       AND (
				(".$phpbb_config_file["table_prefix"]."topics_track.mark_time IS NOT NULL AND ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_time > ".$phpbb_config_file["table_prefix"]."topics_track.mark_time) OR
				(".$phpbb_config_file["table_prefix"]."topics_track.mark_time IS NULL AND ".$phpbb_config_file["table_prefix"]."forums_track.mark_time IS NOT NULL AND ".$phpbb_config_file["table_prefix"]."topics.topic_last_post_time > ".$phpbb_config_file["table_prefix"]."forums_track.mark_time) OR
				(".$phpbb_config_file["table_prefix"]."topics_track.mark_time IS NULL AND ".$phpbb_config_file["table_prefix"]."forums_track.mark_time IS NULL)
				)
			$sql_sort LIMIT $sql_limit");

if(!empty($w3all_exec_sql_array)){
    foreach( $w3all_exec_sql_array as $k => $v ):
      $topic_id = $v->topic_id;
			$unread_topics[$topic_id] = ($v->topic_mark_time) ? (int) $v->topic_mark_time : (($v->forum_mark_time) ? (int) $v->forum_mark_time : $last_mark);
    endforeach;
  }
  
   if(empty($unread_topics) OR !is_array($unread_topics)){
    	$unread_topics = array();
    }
    
    $unread_topics = serialize($unread_topics); // to pass array into define, prior php7
    if($username == false){ // switch for the phpbb_last_topics_single_multi_shortcode.php: if true, not define
     define( "W3UNREADTOPICS", $unread_topics ); 
    }
    return $unread_topics;
	}

	return false;
}

public static function w3all_phpbb_email_hash($email)
{    
  $h = sprintf('%u', crc32(strtolower($email))) . strlen($email);
   return $h;
}
//############################################
// END PHPBB TO WP FUNCTIONS
//############################################

//############################################
// START X WP MS MU
//############################################

public static function create_phpBB_user_wpms_res($username, $user_email, $key, $meta){
	
      $r = self::create_phpBB_user_wpms($username, $user_email, $key, $meta);                                 
	   return $r; 
}

public static function w3all_db_connect_res(){
  return self::w3all_db_connect();
}

public static function wp_w3all_wp_after_pass_reset_msmu( $user ) { 
	
	 global $w3all_config,$wpdb;
	if(!$user){ return; }
	$w3db_conn = self::wp_w3all_phpbb_conn_init();
	$phpbb_config_file = $w3all_config;
 
		$user_email_hash = self::w3all_phpbb_email_hash($user->user_email);
    $res = $w3db_conn->query("UPDATE ".$phpbb_config_file["table_prefix"]."users SET user_type = '0', user_password = '".$user->user_pass."' WHERE user_email_hash = '".$user_email_hash."' OR username = '".$user->user_login."'");

}

private static function create_phpBB_user_wpms($username = '', $user_email = '', $key = '', $meta = ''){
	
	// $username can be ID and $key 'is_admin_action'

	global $w3all_config,$w3all_phpbb_lang_switch_yn,$w3all_add_into_spec_group;
	 $w3phpbb_conn = self::w3all_db_connect();
  if(!defined("W3PHPBBCONFIG")){
    $phpbb_config = self::w3all_get_phpbb_config();
   } else {
	    $phpbb_config = unserialize(W3PHPBBCONFIG);
	   }
   $default_dateformat = $phpbb_config["default_dateformat"];
   $default_lang = $phpbb_config["default_lang"];
   //$wp_lang = get_option('WPLANG');
   // temp fix added also on
   // private static function create_phpBB_user($username = '', $user_email = '', $key = '', $meta = ''){
   // get info about phpBB group where the user need to added: this should may be added on query where W3PHPBBCONFIG is defined
  
   /*$phpbb_group = $w3phpbb_conn->get_row("SELECT * FROM ".$w3all_config["table_prefix"]."groups WHERE group_id = '$w3all_add_into_spec_group'");
   	if( empty($phpbb_group) OR ! $phpbb_group ){
    $group_color = '';
   } else { $group_color = $phpbb_group->group_colour; }
   */
  $phpbb_group = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."ranks
   RIGHT JOIN ".$w3all_config["table_prefix"]."groups ON ".$w3all_config["table_prefix"]."groups.group_rank = ".$w3all_config["table_prefix"]."ranks.rank_id
   AND ".$w3all_config["table_prefix"]."ranks.rank_min = '0'
   AND ".$w3all_config["table_prefix"]."groups.group_id = '$w3all_add_into_spec_group'",ARRAY_A);  
 
 if(!empty($phpbb_group)){  
 	$urank_id_a = array();
   foreach($phpbb_group as $kv){
   	foreach($kv as $k => $v){
     if($k == 'group_id' && $v == $w3all_add_into_spec_group){
    	$urank_id_a = $kv;
     }
   }}
 if (empty($urank_id_a)){
   foreach($phpbb_group as $kv){
   	foreach($kv as $k => $v){
   	if($k == 'rank_special' && $v == 0){
    $urank_id_a = $kv;
    goto this1; // break to the first found ('it seem' to me the default phpBB behavior)??
    }
   }} 
 }
this1:
if ( empty($urank_id_a) ){ 
	$rankID = 0;
	$group_color = '';
 } else {
if ( empty($urank_id_a['rank_id']) ){ 
	$rankID = 0; $group_color = $urank_id_a['group_colour'];
	} else { 
	$rankID = $urank_id_a['rank_id']; $group_color = $urank_id_a['group_colour'];
}}

} // END if(!empty($phpbb_group) OR ! $phpbb_group){    
else { 	$rankID = 0; $group_color = ''; }
   
   if( $key == 'is_admin_action' ){ // see wp_w3all.php // add_action( 'init', 'w3all_network_admin_actions' );
   	$user = get_user_by( 'ID', $username ); // passed user ID in place of username - see wp_w3all.php mums // add_action( 'init', 'w3all_network_admin_actions' );
    $username = $user->user_login; 
  	$user_email = $user->user_email; 
  }

   // review this two checks maybe not necessary
   			  $username = sanitize_user($username, $strict = false ); 
  			  $email = sanitize_email($user_email);

   	/*	if(empty($wp_lang) OR $w3all_phpbb_lang_switch_yn == 0 ){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   $wp_lang_x_phpbb = 'en'; // no lang setting, assume en by default
   			} else { 
   				 $wp_lang_x_phpbb = strtolower(substr($wp_lang, 0, strpos($wp_lang, '_'))); // should extract Lang code ISO Code that is phpBB suitable for this lang
   				}*/
   				
   /* if($w3all_phpbb_lang_switch_yn == 1){
        if(strlen(get_locale()) == 0){ // wp lang for this user ISO 639-1 Code. en_EN // en = Lang code _ EN = Country code
   		   $wp_lang_x_phpbb = 'en'; // no lang setting, assume en by default
   			} else { 
   				if( strlen(get_locale()) == 2 ){ $wp_lang_x_phpbb = strtolower(get_locale()); 
   		  	} else {
   		      $wp_lang_x_phpbb = substr(get_locale(), 0, strpos(get_locale(), '_')); // should extract Lang code ISO Code phpBB suitable for this lang
   		     }
   				   //$wp_lang_x_phpbb = strtolower(substr($wp_lang, 0, strpos($wp_lang, '_'))); // should extract Lang code ISO Code that is phpBB suitable for this lang
   				}
   								// switch for different languages notations
   								// Persian
   		 if( $wp_lang_x_phpbb == 'ps' ){ $wp_lang_x_phpbb = 'fa'; }
      }
   				// and to be sure
          if(!isset($wp_lang_x_phpbb) OR empty($wp_lang_x_phpbb)){ $wp_lang_x_phpbb = 'en'; }
   */
		
		  if(!isset($default_lang) OR empty($default_lang)){ $wp_lang_x_phpbb = 'en'; }
       else { $wp_lang_x_phpbb = $default_lang; }

    if( empty($username) OR empty($user_email) OR !is_email($user_email) ){ return; }
    
     //maybe to be added as option
     // if you wish to setup gravatar by default into phpBB profile for the user when register in WP
     $uavatar = $avatype = ''; // this not will affect queries if the two here below are or not commented out 
     //$uavatar = get_option('show_avatars') == 1 ? $wpu->user_email : '';
     //$avatype = (empty($uavatar)) ? '' : 'avatar.driver.gravatar';
     
     $username = esc_sql($username);

            $u = $phpbb_config["cookie_name"].'_u';
            
            if ( preg_match('/[^0-9]/',$_COOKIE[$u]) ){
 	           	
                die( "Clean up cookie on your browser please!" );
 	            }
 	            
 	           $phpbb_u = $_COOKIE[$u];
 	        
 	    // only need to fire when user do not exist on phpBB already, and/or user is an admin that add an user manually 
   if ( $phpbb_u < 2 OR !empty($phpbb_u) && current_user_can( 'manage_options' ) === true ) {
      
      $phpbb_user_type = 1; //  set to 1 as deactivated on phpBB on WP MSMU except for admin action
      if( $key == 'is_admin_action' ){ 
      	$phpbb_user_type = 0; 
      }
      
	    $user_email_hash = self::w3all_phpbb_email_hash($email);
	     
      $wpur = time();
      $wpul = $username;
      $wpup = md5(microtime() . str_shuffle("ELAa0bc1AdeOf28P3ghEij4kRlm5nopqrD0Lst9uvwx9yzOISs" . microtime()) . mt_rand(10,20)); // a temp pass to be updated after signup finished
      $wpup = self::phpBB_password_hash($wpup); // a temp pass, even not necessary as the user is not active at this point for wp msmu
    if( $key == 'is_admin_action' ){ 
      	$wpup = $user->user_pass; // if admin action, add the pass of this user
      }
      $wpue = $email;
      $time = time();

      $wpunn = esc_sql(utf8_encode(strtolower($wpul)));
      $wpul  = esc_sql($wpul);
      //$w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."users (user_id, user_type, group_id, user_permissions, user_perm_from, user_ip, user_regdate, username, username_clean, user_password, user_passchg, user_email, user_email_hash, user_birthday, user_lastvisit, user_lastmark, user_lastpost_time, user_lastpage, user_last_confirm_key, user_last_search, user_warnings, user_last_warning, user_login_attempts, user_inactive_reason, user_inactive_time, user_posts, user_lang, user_timezone, user_dateformat, user_style, user_rank, user_colour, user_new_privmsg, user_unread_privmsg, user_last_privmsg, user_message_rules, user_full_folder, user_emailtime, user_topic_show_days, user_topic_sortby_type, user_topic_sortby_dir, user_post_show_days, user_post_sortby_type, user_post_sortby_dir, user_notify, user_notify_pm, user_notify_type, user_allow_pm, user_allow_viewonline, user_allow_viewemail, user_allow_massemail, user_options, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_sig, user_sig_bbcode_uid, user_sig_bbcode_bitfield, user_jabber, user_actkey, user_newpasswd, user_form_salt, user_new, user_reminded, user_reminded_time)
      //   VALUES ('','$phpbb_user_type','2','','0','', '$wpur', '$wpul', '$wpunn', '$wpup', '0', '$wpue', '$user_email_hash', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '$wp_lang_x_phpbb', 'Europe/Rome', 'D M d, Y g:i a', '1', '0', '', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', 0, 't', 'a', '0', '1', '0', '1', '1', '1', '1', '230271', '$uavatar', '$avatype', '0', '0', '', '', '', '', '', '', '', '0', '0', '0')");
       $w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."users (user_id, user_type, group_id, user_permissions, user_perm_from, user_ip, user_regdate, username, username_clean, user_password, user_passchg, user_email, user_email_hash, user_birthday, user_lastvisit, user_lastmark, user_lastpost_time, user_lastpage, user_last_confirm_key, user_last_search, user_warnings, user_last_warning, user_login_attempts, user_inactive_reason, user_inactive_time, user_posts, user_lang, user_timezone, user_dateformat, user_style, user_rank, user_colour, user_new_privmsg, user_unread_privmsg, user_last_privmsg, user_message_rules, user_full_folder, user_emailtime, user_topic_show_days, user_topic_sortby_type, user_topic_sortby_dir, user_post_show_days, user_post_sortby_type, user_post_sortby_dir, user_notify, user_notify_pm, user_notify_type, user_allow_pm, user_allow_viewonline, user_allow_viewemail, user_allow_massemail, user_options, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_sig, user_sig_bbcode_uid, user_sig_bbcode_bitfield, user_jabber, user_actkey, user_newpasswd, user_form_salt, user_new, user_reminded, user_reminded_time)
         VALUES ('','$phpbb_user_type','$w3all_add_into_spec_group','','0','', '$wpur', '$wpul', '$wpunn', '$wpup', '0', '$wpue', '$user_email_hash', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '$wp_lang_x_phpbb', 'Europe/Rome', '$default_dateformat', '1', '$rankID', '$group_color', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', 0, 't', 'a', '0', '1', '0', '1', '1', '1', '1', '230271', '$uavatar', '$avatype', '0', '0', '', '', '', '', '', '', '', '0', '0', '0')");
       $phpBBlid = $w3phpbb_conn->insert_id; // memo: pass only assigned vars on queries using this, or will return null
     // TODO: all in one all the follow
    // $w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."user_group (group_id, user_id, group_leader, user_pending) VALUES ('2','$phpBBlid','0','0')");
     $w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."user_group (group_id, user_id, group_leader, user_pending) VALUES ('$w3all_add_into_spec_group','$phpBBlid','0','0')");

     //$w3phpbb_conn->query("INSERT INTO ".$w3all_config["table_prefix"]."acl_users (user_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES ('$phpBBlid','0','0','6','0')");
    		
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = config_value + 1 WHERE config_name = 'num_users'");

       $newest_member = $w3phpbb_conn->get_results("SELECT * FROM ".$w3all_config["table_prefix"]."users WHERE user_id = (SELECT Max(user_id) FROM ".$w3all_config["table_prefix"]."users) AND group_id != '6'");
       $uname = $newest_member[0]->username;
       $uid   = $newest_member[0]->user_id;
     
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = '$wpul' WHERE config_name = 'newest_username'");
     $w3phpbb_conn->query("UPDATE ".$w3all_config["table_prefix"]."config SET config_value = '$uid' WHERE config_name = 'newest_user_id'");

 }
 
}

//############################################
// END X WP MS MU
//############################################

} // END class WP_w3all_phpbb
?>
