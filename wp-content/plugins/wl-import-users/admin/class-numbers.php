<?php

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