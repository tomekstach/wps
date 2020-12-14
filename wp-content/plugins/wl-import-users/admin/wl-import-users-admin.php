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

function wl_import_numbers_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()); ?></h1>
      <form action="options.php" method="post">
        <?php settings_fields('wl_import_numbers_options_group'); ?>
        <?php do_settings_sections('wl_import_numbers_page_html'); ?>
        <?php
        submit_button('Import Numbers');
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

function wl_import_numbers_page()
{
    add_submenu_page(
        'tools.php',
        'WL Import Numbers',
        'WL Import Numbers',
        'manage_options',
        'wl-import-numbers',
        'wl_import_numbers_page_html'
    );
}
add_action('admin_menu', 'wl_import_numbers_page');

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init() {
     register_setting( 'wl_import_users_options_group', 'option_field_name', 'wl_import_users_validation_callback' );
     add_settings_section('wl_import_users_main_id', 'Import user from old CMS', 'wl_import_users_main_settings_cb', 'wl_import_users_page_html');
     register_setting( 'wl_import_numbers_options_group', 'option_field_name', 'wl_import_numbers_validation_callback' );
     add_settings_section('wl_import_numbers_main_id', 'Import numbers to dealers', 'wl_import_numbers_main_settings_cb', 'wl_import_numbers_page_html');
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

function wl_import_numbers_main_settings_cb(){
  global $wpdb;
  ?>
  <h3>Struktura CSV rozdzielanego przecinkami:</h3>
  <p>
    <strong>ID posta z lokalizacją partnera,Alias partnera uzywany do linkow,Liczba certyfikatów,Liczba referencji,Liczba wdrozeń,Liczba modułów</strong>
  </p>
  <p style="vertical-align: top;display: inline;">
    CSV <textarea name="option_field_name[csv]" style="vertical-align: bottom;" rows="20" cols="100"></textarea>
  </p>
  <?php
}

function wl_import_users_validation_callback( $data ) {
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

      $wapro_id = get_blog_id_from_url("wapro.pl");
      if ($wapro_id) {
        add_user_to_blog($wapro_id, $user_id, 'brak');
      }

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

      if (!$user_id and email_exists($item->email) == false and !empty($item->email)) {
        $user_id = wp_create_user($item->username, $item->password, $item->email);
        update_user_meta($user_id, "first_name",  $item->first_name);
        update_user_meta($user_id, "last_name",  $item->last_name);

        // update password
        $pmquery = "UPDATE `".$wpdb->base_prefix."users` SET `user_pass` = '".$item->password."'  WHERE `ID` = '$user_id'";
        $wpdb->query( $pmquery );

        $user = new \WP_User($user_id);
        $user->set_role('brak');

        $wapro_id = get_blog_id_from_url("wapro.pl");
        if ($wapro_id) {
          add_user_to_blog($wapro_id, $user_id, 'brak');
        }

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
      
      // Get lokalizations by ID biura
      if ($item->ID > 0) {
        // args
        $args = array(
          'numberposts'   => -1,
          'post_status '  => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
          'meta_key'      => 'br_id_biura',
          'meta_value'    => $item->ID
        );

        // query
        $posts = get_posts($args);
        $lokalizacje = [];

        foreach ($posts as $post) {
          $lokalizacje[] = $post->ID;

          if (!$user_id) {
            // Get users
            // query args
            $args = array(
              'meta_query'	  => array(
                'relation' => 'OR',
                array(
                  'key'		    => 'br_lokalizacje',
                  'value'		  => $post->ID,
                  'compare'	  => 'LIKE'
                )
              )
            );
            // query
            $user_query         = new WP_User_Query($args);

            $users = $user_query->get_results();

            foreach ($users as $user) {
              $user_id = $user->ID;
            break;
            }
          }
        }

        update_field('br_lokalizacje', $lokalizacje, 'user_' . $user_id);
      }

      // If $user_id exists
      if ($user_id > 0) {
        // Check if dane firmy exists
        $dane_firmy   = get_field('dane_firmy', 'user_' . $user_id);
        $lokalizacje  = get_field('br_lokalizacje', 'user_' . $user_id);

        if (!is_object($dane_firmy) || intval($dane_firmy->ID) == 0) {
          // Add new dane firmy
          $args = [
            'post_status'    => 'private',
            'post_title'     => $user_id.'. '.$item->nazwa_firmy,
            'post_type'      => 'dane_firmy',
            'ping_status'    => 'closed'
          ];

          // Create new post
          $dane_firmy_id = wp_insert_post($args);
          update_field('dane_firmy', $dane_firmy_id, 'user_' . $user_id);
          unset($args);
        } else {
          $dane_firmy_id = $dane_firmy->ID;
        }

        // Update dane firmy
        if ($item->konto_testowe == 'T') {
          $testoweKontoNew = 'TAK';
        } else {
          $testoweKontoNew = 'NIE';
        }

        update_field('df_biuro_id', $item->ID, 'post_' . $dane_firmy_id);
        update_field('df_lokalizacje', $lokalizacje, 'post_' . $dane_firmy_id);
        update_field('df_nazwa', $item->nazwa_firmy, 'post_' . $dane_firmy_id);
        update_field('df_nip', $item->nip, 'post_' . $dane_firmy_id);
        update_field('df_adres', $item->ulica.' '.$item->numer, 'post_' . $dane_firmy_id);
        update_field('df_kod_pocztowy', $item->kod_pocztowy, 'post_' . $dane_firmy_id);
        update_field('df_miasto', $item->miasto, 'post_' . $dane_firmy_id);
        update_field('df_wojewodztwo', $item->wojewodztwo, 'post_' . $dane_firmy_id);
        update_field('df_email', $item->dk_email, 'post_' . $dane_firmy_id);
        if ($testoweKontoNew == 'TAK') {
          update_field('df_testowe_konto_parnera', 1, 'post_' . $dane_firmy_id);
        } else {
          update_field('df_testowe_konto_parnera', 0, 'post_' . $dane_firmy_id);
        }
        update_field('df_imie', $item->first_name, 'post_' . $dane_firmy_id);
        update_field('df_nazwisko', $item->last_name, 'post_' . $dane_firmy_id);
        update_field('df_telefon', $item->telefon, 'post_' . $dane_firmy_id);
        update_field('df_fax', $item->fax, 'post_' . $dane_firmy_id);
        update_field('df_www', $item->www, 'post_' . $dane_firmy_id);
        update_field('df_k_adres', $item->dk_ulica.' '.$item->dk_numer, 'post_' . $dane_firmy_id);
        update_field('df_k_kod_pocztowy', $item->dk_kod_pocztowy, 'post_' . $dane_firmy_id);
        update_field('df_k_miasto', $item->dk_miasto, 'post_' . $dane_firmy_id);
        update_field('df_k_telefon', $item->dk_telefon, 'post_' . $dane_firmy_id);

        $user = new WP_User( $user_id , '' , $blog_id );

        // Add subscriber role
        if (!in_array('subscriber', $user->roles)) {
          $user->set_role('subscriber');
        }
      } else {
        // Add new dane firmy
        $args = [
          'post_status'    => 'private',
          'post_title'     => $item->ID.'. '.$item->nazwa_firmy,
          'post_type'      => 'dane_firmy',
          'ping_status'    => 'closed'
        ];

        // Create new post
        $dane_firmy_id = wp_insert_post($args);
        unset($args);

        // Update dane firmy
        if ($item->konto_testowe == 'T') {
          $testoweKontoNew = 'TAK';
        } else {
          $testoweKontoNew = 'NIE';
        }

        update_field('df_biuro_id', $item->ID, 'post_' . $dane_firmy_id);
        update_field('df_lokalizacje', $lokalizacje, 'post_' . $dane_firmy_id);
        update_field('df_nazwa', $item->nazwa_firmy, 'post_' . $dane_firmy_id);
        update_field('df_nip', $item->nip, 'post_' . $dane_firmy_id);
        update_field('df_adres', $item->ulica.' '.$item->numer, 'post_' . $dane_firmy_id);
        update_field('df_kod_pocztowy', $item->kod_pocztowy, 'post_' . $dane_firmy_id);
        update_field('df_miasto', $item->miasto, 'post_' . $dane_firmy_id);
        update_field('df_wojewodztwo', $item->wojewodztwo, 'post_' . $dane_firmy_id);
        update_field('df_email', $item->dk_email, 'post_' . $dane_firmy_id);
        if ($testoweKontoNew == 'TAK') {
          update_field('df_testowe_konto_parnera', 1, 'post_' . $dane_firmy_id);
        } else {
          update_field('df_testowe_konto_parnera', 0, 'post_' . $dane_firmy_id);
        }
        update_field('df_imie', $item->first_name, 'post_' . $dane_firmy_id);
        update_field('df_nazwisko', $item->last_name, 'post_' . $dane_firmy_id);
        update_field('df_telefon', $item->telefon, 'post_' . $dane_firmy_id);
        update_field('df_fax', $item->fax, 'post_' . $dane_firmy_id);
        update_field('df_www', $item->www, 'post_' . $dane_firmy_id);
        update_field('df_k_adres', $item->dk_ulica.' '.$item->dk_numer, 'post_' . $dane_firmy_id);
        update_field('df_k_kod_pocztowy', $item->dk_kod_pocztowy, 'post_' . $dane_firmy_id);
        update_field('df_k_miasto', $item->dk_miasto, 'post_' . $dane_firmy_id);
        update_field('df_k_telefon', $item->dk_telefon, 'post_' . $dane_firmy_id);
      }

      // update imported
      $pmquery = "UPDATE `".$wpdb->base_prefix."users_import` SET `imported` = '1'  WHERE `ID` = '".$item->ID."'";
      $wpdb->query( $pmquery );
    }
    unset($user_id);
  }

  return $data;
}

function wl_import_numbers_validation_callback( $data ) {
  global $wpdb;

  $rows  = str_getcsv($data['csv'], "\n");

  foreach ($rows as $row) {
    $item = str_getcsv(trim($row));

    $post = get_post($item[0]);

    if (isset($post) && $post != null) {
      update_field('dl_alias', intval($item[1]), 'post_' . $item[0]);
      update_field('dl_liczba_szkolen', intval($item[2]), 'post_' . $item[0]);
      update_field('dl_liczba_certyfikatow', intval($item[3]), 'post_' . $item[0]);
      update_field('dl_liczba_referencji', intval($item[4]), 'post_' . $item[0]);
      update_field('dl_liczba_wdrozen', intval($item[5]), 'post_' . $item[0]);
      update_field('dl_liczba_modulow', intval($item[6]), 'post_' . $item[0]);
    }
  }

  return $data;
}
