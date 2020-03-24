<?php

function wl_import_users_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()); ?></h1>
      <form action="options.php" method="post">
        <?php settings_fields('wl_import_users_options_group'); ?>
        <?php do_settings_sections('wl_import_users_page_html'); ?>
        <?php
        submit_button('Import Users');
        ?>
      </form>
    </div>
    <?php
}

function wl_import_users_page()
{
    add_submenu_page(
        'tools.php',
        'WL Import Users',
        'WL Import Users',
        'manage_options',
        'wl-import-users',
        'wl_import_users_page_html'
    );
}
add_action('admin_menu', 'wl_import_users_page');

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init() {
     register_setting( 'wl_import_users_options_group', 'option_field_name', 'wl_import_users_validation_callback' );
     add_settings_section('wl_import_users_main_id', 'Import user from old CMS', 'wl_import_users_main_settings_cb', 'wl_import_users_page_html');
}

function wl_import_users_main_settings_cb(){
  global $wpdb;

  $count = $wpdb->get_var( "SELECT COUNT(*) FROM `".$wpdb->base_prefix."users_import` WHERE `imported` = '0'" );
  ?>
  <p><u>Users to import: </u><?php echo $count; ?></p>
  <?php
  $my_options = get_option('wl_import_users_options_group',array());
  $limit  = isset($my_options['limit']) ? intval($my_options['limit']) : 100;
  $type   = isset($my_options['type']) ? intval($my_options['type']) : 'dealer';
  ?>
       Limit <input type="text" value="<?php echo $limit; ?>" name="option_field_name[limit]"><br />
       Type <input type="text" value="<?php echo $type; ?>" name="option_field_name[type]"><br />
  <?php
}

function wl_import_users_validation_callback( $data ){
  global $wpdb;

  $limit  = intval($data['limit']);
  $type   = $data['type'];

  if ($type != 'biuro') {
    $type = 'dealer';
  }

  $pmquery = "SELECT * FROM `".$wpdb->base_prefix."users_import` WHERE `imported` = '0' AND `user_type` = '$type' ORDER BY `ID`  LIMIT 0, $limit";
  $items = $wpdb->get_results( $pmquery );

  foreach ($items as $item) {
    $user_id = username_exists($item->username);

    if (!$user_id and email_exists($item->email) == false and $item->user_type == 'dealer') {
      $user_id = wp_create_user($item->username, $item->password, $item->email);
      update_user_meta($user_id, "first_name",  $item->first_name);
      update_user_meta($user_id, "last_name",  $item->last_name);

      // update password
      $pmquery = "UPDATE `".$wpdb->base_prefix."users` SET `user_pass` = '".$item->password."'  WHERE `ID` = '$user_id'";
      $wpdb->query( $pmquery );

      $user = new \WP_User($user_id);
      $user->set_role('brak');

      $partnerzy_id = get_blog_id_from_url("partnerzy.wapro.pl");
      if ($partnerzy_id) {
        add_user_to_blog($partnerzy_id, $user_id, 'brak');
      }

      $biura_id = get_blog_id_from_url("biura.wapro.pl");
      if ($biura_id) {
        add_user_to_blog($biura_id, $user_id, 'brakbiuro');
      }

      $pomoc_id = get_blog_id_from_url("pomoc.wapro.pl");
      if ($pomoc_id) {
        add_user_to_blog($pomoc_id, $user_id, 'subscriber');
      }

      unset($partnerzy_id);
      unset($biura_id);
      unset($pomoc_id);
    }
    
    if ($user_id and $item->user_type == 'dealer') {
      // PARTNERZY
      $blog_id = get_blog_id_from_url("partnerzy.wapro.pl");

      // By the email
      $pmquery = "SELECT * FROM `".$wpdb->base_prefix.$blog_id."_postmeta` WHERE `meta_value` = '".$item->email."' AND `meta_key` = 'dl_email'";
      $cards_e = $wpdb->get_results( $pmquery );

      if (count($cards_e)) {
        foreach ($cards_e as $card) {
          $card_id = $card->post_id;
          $dealer_id = get_metadata('user', $user_id, 'dl_siedziba_glowna', true);

          if ($dealer_id == 0) {
            update_user_meta($user_id, 'dl_siedziba_glowna', $card_id);
          } else {
            $loks = get_metadata('user', $user_id, 'dl_lokalizacje', true);

            if (is_array($loks)) {
              if (!in_array($card_id, $loks)) {
                $loks[] = $card_id;
                update_user_meta($user_id, 'dl_lokalizacje', $loks);
              }
            } elseif (!empty($loks) and $loks != $card_id) {
              $new_loks = [$loks, $card_id];
              update_user_meta($user_id, 'dl_lokalizacje', $new_loks);
            }
          }
        }
      }

      // By the NIP
      $pmquery = "SELECT * FROM `".$wpdb->base_prefix.$blog_id."_postmeta` WHERE `meta_value` = '".$item->nip."' AND `meta_key` = 'dl_nip'";
      $cards_n = $wpdb->get_results( $pmquery );

      if (count($cards_n)) {
        foreach ($cards_n as $card) {
          $card_id = $card->post_id;
          $dealer_id = get_metadata('user', $user_id, 'dl_siedziba_glowna', true);

          if ($dealer_id == 0) {
            update_user_meta($user_id, 'dl_siedziba_glowna', $card_id);
          } else {
            $loks = get_metadata('user', $user_id, 'dl_lokalizacje', true);

            if (is_array($loks)) {
              if (!in_array($card_id, $loks)) {
                $loks[] = $card_id;
                update_user_meta($user_id, 'dl_lokalizacje', $loks);
              }
            } elseif (!empty($loks) and $loks != $card_id) {
              $new_loks = [$loks, $card_id];
              update_user_meta($user_id, 'dl_lokalizacje', $new_loks);
            }
          }
        }
      }

      // By the ID
      $args = array(
        'post_name' => '25165',
        'post_type' => 'post'
      );
      $cards_s = get_posts($args);

      if (count($cards_s)) {
        foreach ($cards_s as $card) {
          $card_id = $card->ID;
          $dealer_id = get_metadata('user', $user_id, 'dl_siedziba_glowna', true);

          if ($dealer_id == 0) {
            update_user_meta($user_id, 'dl_siedziba_glowna', $card_id);
          } else {
            $loks = get_metadata('user', $user_id, 'dl_lokalizacje', true);

            if (is_array($loks)) {
              if (!in_array($card_id, $loks)) {
                $loks[] = $card_id;
                update_user_meta($user_id, 'dl_lokalizacje', $loks);
              }
            } elseif (!empty($loks) and $loks != $card_id) {
              $new_loks = [$loks, $card_id];
              update_user_meta($user_id, 'dl_lokalizacje', $new_loks);
            }
          }
        }
      }

      //if ($item->wapro == '1' || count($cards_e) || count($cards_n) || count($cards_s)) {
        $user = new WP_User( $user_id , '' , $blog_id );

        if (!in_array('subscriber', $user->roles)) {
          $user->set_role('subscriber');
        }

        // update imported
        $pmquery = "UPDATE `".$wpdb->base_prefix."users_import` SET `imported` = '1'  WHERE `ID` = '".$item->ID."'";
        $wpdb->query( $pmquery );
      //}
      unset($cards_e);
      unset($cards_n);
    }

    if ($item->user_type == 'biuro') {
      // BIURA
      $blog_id = get_blog_id_from_url("biura.wapro.pl");

      // By the ID Biura
      $pmquery = "SELECT * FROM `".$wpdb->base_prefix.$blog_id."_postmeta` WHERE `meta_value` = '".$item->office_id."' AND `meta_key` = 'br_id_biura'";
      $cards = $wpdb->get_results( $pmquery );

      if (!$user_id and email_exists($item->email) == false) {
        $user_id = wp_create_user($item->username, $item->password, $item->email);
        update_user_meta($user_id, "first_name",  $item->first_name);
        update_user_meta($user_id, "last_name",  $item->last_name);

        // update password
        $pmquery = "UPDATE `".$wpdb->base_prefix."users` SET `user_pass` = '".$item->password."'  WHERE `ID` = '$user_id'";
        $wpdb->query( $pmquery );

        $user = new \WP_User($user_id);
        $user->set_role('brak');

        $partnerzy_id = get_blog_id_from_url("partnerzy.wapro.pl");
        if ($partnerzy_id) {
          add_user_to_blog($partnerzy_id, $user_id, 'brak');
        }

        $biura_id = get_blog_id_from_url("biura.wapro.pl");
        if ($biura_id) {
          add_user_to_blog($biura_id, $user_id, 'brakbiuro');
        }

        $pomoc_id = get_blog_id_from_url("pomoc.wapro.pl");
        if ($pomoc_id) {
          add_user_to_blog($pomoc_id, $user_id, 'subscriber');
        }

        unset($partnerzy_id);
        unset($biura_id);
        unset($pomoc_id);
      }

      if (count($cards)) {

        if ($user_id > 0) {
          foreach ($cards as $card) {
            $card_id = $card->post_id;
            $biuro_id = get_metadata('user', $user_id, 'br_siedziba_glowna', true);

            if ($biuro_id == 0) {
              update_user_meta($user_id, 'br_siedziba_glowna', $card_id);
            } else {
              $loks = get_metadata('user', $user_id, 'br_lokalizacje', true);

            if (is_array($loks)) {
              if (!in_array($card_id, $loks)) {
                $loks[] = $card_id;
                update_user_meta($user_id, 'br_lokalizacje', $loks);
              }
            } elseif (!empty($loks) and $loks != $card_id) {
              $new_loks = [$loks, $card_id];
              update_user_meta($user_id, 'br_lokalizacje', $new_loks);
            }
            }
          }

          $user = new WP_User( $user_id , '' , $blog_id );

          if (!in_array('subscriber', $user->roles)) {
            $user->set_role('subscriber');
          }
        }
      }
      unset($cards);

      // update imported
      $pmquery = "UPDATE `".$wpdb->base_prefix."users_import` SET `imported` = '1'  WHERE `ID` = '".$item->ID."'";
      $wpdb->query( $pmquery );
    }
    unset($user_id);
  }

  return $data;
} 
