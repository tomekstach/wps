<?php

/**
 * Plugin name: Custom API
 * Description: Endpoints for wpdev
 * Version: 1.0
 * Author: AstoSoft Joanna Stach
 * Author URI: https://astosoft.pl
 */

/**
 * WP Custom REST API method to get user data for Umowa Serwisowa Form
 *
 * @return object
 */
function wl_current_user()
{
  $current_user = wp_get_current_user();
  $user_meta = get_user_meta($current_user->ID);

  $user = new \stdClass;
  $user->id         = $current_user->ID;
  $user->first_name = $user_meta['first_name'][0];
  $user->last_name  = $user_meta['last_name'][0];
  $user->user_nip   = $user_meta['user_nip'][0];
  $user->user_tel   = $user_meta['user_tel'][0];
  $user->user_firm  = $user_meta['user_firm'][0];
  $user->user_email = $current_user->user_email;

  return $user;
}

/**
 * WP Custom REST API method to add new Umowa Serwisowa
 *
 * @return integer
 */
function wl_add_contract()
{
  $current_user = wp_get_current_user();
  $nip          = addslashes(stripslashes(strip_tags($_POST['nip'])));
  $firm         = addslashes(stripslashes(strip_tags($_POST['firm'])));
  $email        = addslashes(stripslashes(strip_tags($_POST['email'])));
  $firstname    = addslashes(stripslashes(strip_tags($_POST['firstname'])));
  $lastname     = addslashes(stripslashes(strip_tags($_POST['lastname'])));
  $phone        = addslashes(stripslashes(strip_tags($_POST['phone'])));
  $rodoRodaj    = addslashes(stripslashes(strip_tags($_POST['rodoRodaj'])));
  $umowaPodpisana = intval($_POST['umowaPodpisana']);
  $date         = date('Y-m-d H:i:s');
  $date_end     = date('Y-m-d', strtotime("+7 day", time()));

  $args = array(
    'comment_status' => 'closed',
    'post_status'    => 'publish',
    'post_title'     => 'Umowa serwisowa z ' . $firm . ' ' . $date,
    'post_type'      => 'umowa_serwisowa'
  );

  // Create new post
  $new_post_id = wp_insert_post($args);

  // Klient
  update_field('field_5de2ce1ccc694', $current_user->ID, 'post_' . $new_post_id);
  // Data wygasniecia
  update_field('field_5de2ce8379eb9', $date_end, 'post_' . $new_post_id);
  // NIP
  update_field('field_5de2cead79eba', $nip, 'post_' . $new_post_id);
  // Nazwa firmy
  update_field('field_5de2cec879ebb', $firm, 'post_' . $new_post_id);
  // E-mail
  update_field('field_5de2ced479ebc', $email, 'post_' . $new_post_id);
  // Imie
  update_field('field_5df9b3cfead85', $firstname, 'post_' . $new_post_id);
  // Nazwisko
  update_field('field_5df9b3e1ead86', $lastname, 'post_' . $new_post_id);
  // Telefon
  update_field('field_5df9b3f1ead87', $phone, 'post_' . $new_post_id);

  $to = $email;
  $subject = 'Informacja o dodaniu nowej umowy serwisowej';
  $headers[] = 'From: WAPRO ERP <kontakt@wapro.pl>';
  $headers[] = 'Reply-To: sprzedaz.wapro@assecobs.pl';
  $headers[] = 'Content-Type: text/html; charset=UTF-8';
  $message = '<body bgcolor="#f7f5f5" style="background-color:#f7f5f5;">
        <table border="0" cellspacing="0" cellpadding="0" align="center" width="600" bgcolor="#fff" style="width:600px; background-color:#fff;">
          <tbody width="600" style="width:600px;">
            <tr width="600" style="width:600px;">
              <td colspan="3">
                <table>
                  <tr>
                    <td width="200" style="width:200px;"><img BORDER="0" style="display:block; padding:0; margin:0;" src="http://www.assecobs.pl/storage/mail/stat/logo.png" alt="WAPRO ERP by Asseco" title="WAPRO ERP by Asseco" /></td>
                    <td width="400" style="width:400px;">
                      <table>
                        <tr>
                          <td width="360" align="right" style="width:360px; text-align:right; font-family:arial; font-size:14px; color:#000; text-decoration:none;">
                            <a style="font-family:arial; font-size:14px; color:#000; text-decoration:none;" href="http://www.wapro.pl">WAPRO ERP</a> 
                          </td>
                          <td width="40" style="width:40px;"></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr width="600" style="width:600px;">
              <td colspan="3"><img BORDER="0" style="display:block; padding:0; margin:0;" src="http://www.assecobs.pl/storage/mail/stat/header-line.png" alt="" title="" /></td>
            </tr>
            <tr width="600" style="width:600px;">
              <td width="40" style="width:40px;"></td>
              <td width="520" style="width:580px;">
                        
      
      <h2 style="font-family:Arial, Helvetica, Verdana, sans-serif;"> Szanowni Państwo! </h2>
      
      <p style="font-size:12px; text-align:justify; font-family:Arial, Helvetica, Verdana, sans-serif;">
      Dziękujemy za zarejestrowanie zgłoszenia.
      </p>
      <p style="font-size:12px; text-align:justify; font-family:Arial, Helvetica, Verdana, sans-serif;">
      To jest <a href="' . get_site_url() . '/umowa-serwisowa-krok-3/?contract=' . $new_post_id . '" target="_blank">link do Twojego zgłoszenia w systemie serwisowym WAPRO ERP</a>.<br/>Skorzystaj z niego aby przesłać informację o wysłanych plikach.
      </p>
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td style="height: 118px">
                      <strong style="font-family:arial; font-size:14px;">
                      <br>
                      Pozdrawiamy</strong><br />
                      <span style="font-family:arial; color:#da0d14; font-size:14px;">Zespół WAPRO ERP</span>
      
                      <p style="font-family:arial; font-size:14px;margin-bottom:20px;">
                        Asseco Business Solutions S.A.<br />
                        Oddział w Warszawie<br />
                        ul. Adama Branickiego 13<br />
                        <a style="font-family:arial; color:#da0d14; font-size:14px; text-decoration:underline;" href="http://wapro.pl">wapro.pl</a>
      
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
              <td width="40" style="width:40px;"></td>
            </tr>
          </tbody>
        </table>
      </body>';

  wp_mail($to, $subject, $message, $headers);

  $message = "NIP: $nip <br/>
  Nazwa firmy: $firm <br/>
  Kontakt.Imię: " . $firstname . " <br/>
  Kontakt.Nazwisko: " . $lastname . " <br/>
  Kontakt.Telefon: " . $phone . " <br/>
  Kontakt.Email: $email <br/>
  identyfikator: $new_post_id <br/>
  Zgłoszenie rozwiązane: nie <br/>\n\n";

  $to = 'tomasz.stach@astosoft.pl';
  wp_mail($to, $subject, $message, $headers);
  $to = 'boguslaw.tober@assecobs.pl';
  wp_mail($to, $subject, $message, $headers);
  $to = 'Agnieszka.Palyz@assecobs.pl';
  wp_mail($to, $subject, $message, $headers);

  if ($umowaPodpisana === 0) {
    $to = $email;
    $subject = 'Potwierdzenie zawarcia umowy przetwarzania danych osobowych';
    $attachments = array(WP_CONTENT_DIR . '/uploads/2019/10/Umowa_powierzenia_przetwarzania_danych_osobowych.pdf');
    $message = '<body bgcolor="#f7f5f5" style="background-color:#f7f5f5;">
        <table border="0" cellspacing="0" cellpadding="0" align="center" width="600" bgcolor="#fff" style="width:600px; background-color:#fff;">
          <tbody width="600" style="width:600px;">
            <tr width="600" style="width:600px;">
              <td colspan="3">
                <table>
                  <tr>
                    <td width="200" style="width:200px;"><img BORDER="0" style="display:block; padding:0; margin:0;" src="http://www.assecobs.pl/storage/mail/stat/logo.png" alt="WAPRO ERP by Asseco" title="WAPRO ERP by Asseco" /></td>
                    <td width="400" style="width:400px;">
                      <table>
                        <tr>
                          <td width="360" align="right" style="width:360px; text-align:right; font-family:arial; font-size:14px; color:#000; text-decoration:none;">
                            <a style="font-family:arial; font-size:14px; color:#000; text-decoration:none;" href="http://www.wapro.pl">WAPRO ERP</a> 
                          </td>
                          <td width="40" style="width:40px;"></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr width="600" style="width:600px;">
              <td colspan="3"><img BORDER="0" style="display:block; padding:0; margin:0;" src="http://www.assecobs.pl/storage/mail/stat/header-line.png" alt="" title="" /></td>
            </tr>
            <tr width="600" style="width:600px;">
              <td width="40" style="width:40px;"></td>
              <td width="520" style="width:580px;">
                        
      
      <h2 style="font-family:Arial, Helvetica, Verdana, sans-serif;"> Szanowni Państwo! </h2>
      
      <p style="font-size:12px; text-align:justify; font-family:Arial, Helvetica, Verdana, sans-serif;">
      Potwierdzamy zawarcie z nami umowy powierzenia przetwarzania danych osobowych w treści jak w załączeniu, przy czym: 
      <br><br>
      Osoba zawierająca umowę: ' . $firstname . ' ' . $lastname . ' (' . $rodoRodaj . ') 
      <br><br>
      Data zawarcia: ' . $date . ' 
      <br><br>
      Adres e-mail do zgłaszania naruszeń: ' . $email . ' 
      <br><br>
      Zakres obowiązywania umowy: uruchomienie lub testowanie programu na danych rzeczywistych 
      <br><br>
      Umowa została zawarta zdalnie przez kliknięcie w przycisk i akceptację danych w formularzu przystąpienia do umowy. 
      <br><br>
      Przypominamy, że nie jest wymagane drukowanie i przesyłanie papierowego egzemplarza podpisanej umowy.
      </p>
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td style="height: 118px">
                      <strong style="font-family:arial; font-size:14px;">
                      <br>
                      Pozdrawiamy</strong><br />
                      <span style="font-family:arial; color:#da0d14; font-size:14px;">Zespół WAPRO ERP</span>
      
                      <p style="font-family:arial; font-size:14px;margin-bottom:20px;">
                        Asseco Business Solutions S.A.<br />
                        Oddział w Warszawie<br />
                        ul. Adama Branickiego 13<br />
                        <a style="font-family:arial; color:#da0d14; font-size:14px; text-decoration:underline;" href="http://wapro.pl">wapro.pl</a>
      
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
              <td width="40" style="width:40px;"></td>
            </tr>
          </tbody>
        </table>
      </body>';

    wp_mail($to, $subject, $message, $headers, $attachments);
  }

  return $new_post_id;
}

/**
 * WP Custom REST API method to get Umowa Serwisowa data
 *
 * @return object
 */
function wl_get_contract()
{
  $post_id  = intval($_POST['contract']);
  $post     = get_post($post_id);
  $umowa    = new \stdClass;
  $current_user = wp_get_current_user();

  if (isset($post) && $post !== null && $post->post_type == 'umowa_serwisowa') {
    $user     = get_field("klient", $post_id);
    $data_do  = get_field("data_wygasniecia", $post_id);

    if ($user->ID !== $current_user->ID) {
      $umowa->umowa_id  = 0;
      $umowa->message   = 'Nie masz uprawnień do wybranej umowy!';
    } elseif ($data_do < date('Y-m-d H:i:s')) {
      $umowa->umowa_id  = 0;
      $umowa->message   = 'Wybrana umowa serwisowa wygasła!';
    } else {
      $umowa->umowa_id    = $post->ID;
      $umowa->nip         = get_field("nip_klienta", $post_id);
      $umowa->nazwa_firmy = get_field("nazwa_firmy_klienta", $post_id);
      $umowa->email       = get_field("e-mail_klienta", $post_id);
      $umowa->first_name  = get_field("imie_klienta", $post_id);
      $umowa->last_name   = get_field("nazwisko_klienta", $post_id);
      $umowa->user_tel    = get_field("telefon_klienta", $post_id);
    }
  } else {
    $umowa->umowa_id  = 0;
    $umowa->message   = 'Brak umowy o podanym indentyfikatorze!';
  }

  return $umowa;
}

/**
 * WP Custom REST API method to get list of Umowa Serwisowa
 *
 * @return object
 */
function wl_get_contracts()
{
  $current_user = wp_get_current_user();
  $user_meta = get_user_meta($current_user->ID);

  $list = [];

  if ($current_user->ID) {
    // args
    $args = array(
      'numberposts'   => -1,
      'post_status '  => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
      'post_type'     => 'umowa_serwisowa',
      'meta_key'      => 'klient',
      'meta_value'    => $current_user->ID
    );

    // query
    $posts = get_posts($args);

    foreach ($posts as $post) {

      if (get_field('data_wygasniecia', $post->ID) >= date('Y-m-d')) {
        $umowa = new \stdClass;
        $umowa->id                  = $post->ID;
        $umowa->data_wygasniecia    = get_field('data_wygasniecia', $umowa->id);
        $umowa->NIP                 = get_field('nip_klienta', $umowa->id);
        $umowa->nazwa_firmy_klienta = get_field('nazwa_firmy_klienta', $umowa->id);
        $umowa->e_mail_klienta      = get_field('e-mail_klienta', $umowa->id);
        $umowa->tel_klienta         = get_field("telefon_klienta", $umowa->id);
        $umowa->first_name          = get_field("imie_klienta", $umowa->id);
        $umowa->last_name           = get_field("nazwisko_klienta", $umowa->id);
        $umowa->data_zgloszenia     = get_the_date('d.m.Y H:i', $umowa->id);

        // args - files
        $args_files = array(
          'numberposts'   => -1,
          'post_status '  => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
          'post_type'     => 'plik_umowy',
          'meta_key'      => 'umowa_serwisowa',
          'meta_value'    => $umowa->id
        );

        // query - files
        $post_files   = get_posts($args_files);
        $umowa->files = [];

        foreach ($post_files as $post_file) {
          $file = new \stdClass;
          $file->id             = $post_file->ID;
          $file->sciezka        = get_field('sciezka', $file->id);
          $file->haslo          = get_field('haslo', $file->id);
          $file->program        = get_field('program', $file->id);
          $file->wersja         = get_field('wersja', $file->id);
          $file->wersja_sql     = get_field('wersja_sql', $file->id);
          $file->opis_problemu  = get_field('opis_problemu', $file->id);
          $file->zalacznik      = get_field('zalacznik', $file->id);

          $umowa->files[] = $file;
        }

        $list[] = $umowa;
      }
    }
  }

  return $list;
}

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'user', [
    'methods' => 'POST',
    'callback' => 'wl_current_user'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'addContract', [
    'methods' => 'POST',
    'callback' => 'wl_add_contract'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getContract', [
    'methods' => 'POST',
    'callback' => 'wl_get_contract'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getContracts', [
    'methods' => 'POST',
    'callback' => 'wl_get_contracts'
  ]);
});