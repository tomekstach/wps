<?php

/**
 * Plugin name: Custom API
 * Description: Endpoints for wpdev
 * Version: 1.1
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

  $to = 'serwis.wapro@assecobs.pl';
  wp_mail($to, $subject, $message, $headers);
  $to = 'boguslaw.tober@assecobs.pl';
  wp_mail($to, $subject, $message, $headers);
  $to = 'Agnieszka.Palyz@assecobs.pl';
  wp_mail($to, $subject, $message, $headers);
  $to = 'wapro_naprawa_bazy@aganik.abs.assecobs.pl';
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
    $to = 'boguslaw.tober@assecobs.pl';
    wp_mail($to, $subject, $message, $headers, $attachments);
    $to = 'Agnieszka.Palyz@assecobs.pl';
    wp_mail($to, $subject, $message, $headers, $attachments);

    // SEND RODO CONTRACT TO ERP
    //$url = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
    $url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

    $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

    $paramsCustomer     = array('ArrayCustomerGetData' => array('CustomerGetData' => array('NIPSameCyfry' => $nip)));
    $responseCustomer   = $client->CUSTOMERGET($paramsCustomer);

    if ($responseCustomer->ArrayCustomerGetResult->Status != '0') {

      $paramsAgreement    = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $nip]]];
      $responseAgreement  = $client->DPAgreementGet($paramsAgreement);

      if ($responseAgreement->ArrayDPAgreementGetResult->Status != '0') {
        if (is_array($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult)) {
          $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult[count($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult)-1];
        } else {
          $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult;
        }
      } else {
        $DPAgreementGetResult = new \stdClass;
        $DPAgreementGetResult->Konserwacja = '0';
        $DPAgreementGetResult->Outsourcing = '0';
        $DPAgreementGetResult->Hosting = '0';
      }

      $params = ['ArrayAgreementCreateData' => ['AgreementCreateData' => [
            'NrZewn' => $new_post_id,
            'Zrodlo' => 'Wapro',
            'NIPSameCyfry' => $nip,
            'WersjaUmowy' => '20190624',
            'DataPodpisania' => date(DATE_ATOM),
            'RealizacjaOd' => date(DATE_ATOM),
            'OpisUmowy' => 'Umowa powierzenia przetwarzania danych osobowych',
            'EDOK' => '1',
            'Aneks' => '0',
            'RodzajUmocowania' => $rodoRodaj,
            'ImieNazwisko' => $firstname . ' ' . $lastname,
            'DataDo' => '2050-12-31T00:00:00',
            'RealizacjaDo' => '2050-12-31T00:00:00',
            'Konserwacja' => $DPAgreementGetResult->Konserwacja,
            'Outsourcing' => $DPAgreementGetResult->Outsourcing,
            'DaneKontaktoweDPO' => 'brak',
            'MailDoZglaszaniaNaruszen' => $email,
            'UruchTestProg' => '1',
            'Hosting' => $DPAgreementGetResult->Hosting
            ]]];
      $response = $client->AgreementCreate($params);

      //print_r($response);
    }
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
 * WP Custom REST API method to get RODO data
 *
 * @return object
 */
function wl_get_rodo()
{
  require_once 'NIP24/NIP24Client.php';
  \NIP24\NIP24Client::registerAutoloader();

  $nip24 = new \NIP24\NIP24Client('wRocgSXQIItj', '2PEXnwYwCwVA');

  $post_id  = intval($_POST['contract']);
  $nip      = preg_replace('/\s+/', '', str_replace('-', '', strip_tags($_POST['nip'])));
  $post     = get_post($post_id);
  $umowa    = new \stdClass;
  $umowa->error = '';
  $current_user = wp_get_current_user();

  // Sprawdzenie stanu konta
  $account = $nip24->getAccountStatus();

  if (!$account) {
    $umowa->error = $nip24->getLastError();
  } else {
    // Wywołanie metody zwracającej szczegółowe dane firmy
    $all = $nip24->getAllDataExt(\NIP24\Number::NIP, $nip, false);

    if ($all) {
      $umowa->name     = addslashes($all->name);
      $umowa->address  = $all->street;

      if (empty($umowa->address)) {
        $umowa->address = $all->city;
      }

      $umowa->address .= ' ' . $all->streetNumber;

      if (!empty($all->houseNumber)) {
        $umowa->address .= '/' . $all->houseNumber;
      }

      $umowa->city       = $all->postCity != '' ? $all->postCity : $all->city;
      if ($all->postCode) {
        $umowa->postCode   = substr($all->postCode, 0, 2) . '-' . substr($all->postCode, -3);
      } else {
        $umowa->postCode = '';
      }
    } else {
      $umowa->error = $nip24->getLastError();
    }
  }

  if (isset($post) && $post !== null && $post->post_type == 'umowa_rodo') {

    if ($nip != get_field("nip_klienta", $post_id)) {
      $umowa->umowa_id  = 0;
      $umowa->error   = 'Nie masz uprawnień do wybranej umowy!';
    } elseif (get_field("data_podpisania_umowy", $post_id) != '') {
      $umowa->umowa_id  = 0;
      $umowa->error   = 'Umowa została juz podpisana!';
    } else {
      $umowa->umowa_id      = $post->ID;
      $umowa->rodzaj_umowy  = get_field("rodzaj_umowy", $post_id);
      $umowa->email         = get_field("adres_e_mail", $post_id);
      $umowa->serwis_email  = get_field("serwis_e-mail", $post_id);
    }
  } else {
    $umowa->umowa_id  = 0;
    $umowa->error   = 'Brak umowy o podanym indentyfikatorze!';
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

/**
 * WP Custom REST API method to get list of Umowa Serwisowa
 *
 * @return object
 */
function wl_get_biuro_firm()
{
  $current_user = wp_get_current_user();

  $dane_firmy   = get_field('dane_firmy', 'user_' . $current_user->ID);

  if (is_object($dane_firmy)) {
    $dane_firmy->admin = '1';
  } else {
    $lokalizacje   = get_field('br_lokalizacje', 'user_' . $current_user->ID);
    if (is_array($lokalizacje)) {
      $lokalizacja_id = intval($lokalizacje[0]);
    } else {
      $lokalizacja_id = intval($lokalizacje);
    }
      
    // query args
    $args = array(
      'numberposts'   => -1,
      'post_status'   => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'), 
      'post_type'     => 'dane_firmy',
      'meta_query'	  => array(
        array(
          'key'		    => 'df_lokalizacje',
          'value'		  => $lokalizacja_id,
          'compare'	  => 'LIKE'
        )
      )
    );

    // query
    $firma   = get_posts($args);
    
    if (is_object($firma[0])) {
      $dane_firmy = $firma[0];
      $dane_firmy->admin = '0';
    }
  }

  if (is_object($dane_firmy)) {
    // Get all data
    $dane_firmy->nazwa_firmy    = get_field('df_nazwa', 'post_' . $dane_firmy->ID);
    $dane_firmy->NIP            = get_field('df_nip', 'post_' . $dane_firmy->ID);
    $dane_firmy->adres          = get_field('df_adres', 'post_' . $dane_firmy->ID);
    $dane_firmy->kod_pocztowy   = get_field('df_kod_pocztowy', 'post_' . $dane_firmy->ID);
    $dane_firmy->miasto         = get_field('df_miasto', 'post_' . $dane_firmy->ID);
    $dane_firmy->wojewodztwo    = get_field('df_wojewodztwo', 'post_' . $dane_firmy->ID);
    $dane_firmy->email          = get_field('df_email', 'post_' . $dane_firmy->ID);
    $dane_firmy->testowe_konto  = get_field('df_testowe_konto_parnera', 'post_' . $dane_firmy->ID);
    $dane_firmy->imie           = get_field('df_imie', 'post_' . $dane_firmy->ID);
    $dane_firmy->nazwisko       = get_field('df_nazwisko', 'post_' . $dane_firmy->ID);
    $dane_firmy->telefon        = get_field('df_telefon', 'post_' . $dane_firmy->ID);
    $dane_firmy->fax            = get_field('df_fax', 'post_' . $dane_firmy->ID);
    $dane_firmy->www            = get_field('df_www', 'post_' . $dane_firmy->ID);
    $dane_firmy->k_adres        = get_field('df_k_adres', 'post_' . $dane_firmy->ID);
    $dane_firmy->k_kod_pocztowy = get_field('df_k_kod_pocztowy', 'post_' . $dane_firmy->ID);
    $dane_firmy->k_miasto       = get_field('df_k_miasto', 'post_' . $dane_firmy->ID);
    $dane_firmy->k_telefon      = get_field('df_k_telefon', 'post_' . $dane_firmy->ID);
  } else {
    $dane_firmy = new \stdClass;
    $dane_firmy->admin = '0';
  }

  $dane_firmy->user_id        = $current_user->ID;

  return $dane_firmy;
}

/**
 * WP Custom REST API method to get list of locations
 *
 * @return object
 */
function wl_get_locations()
{
  $current_user = wp_get_current_user();

  $list = [];

  if ($current_user->ID) {
    // get locations
    $lokalizacje = get_field('br_lokalizacje', 'user_'.$current_user->ID);

    if (is_array($lokalizacje)) {
        $lokalizacja_id = intval($lokalizacje[0]);
    } elseif (intval($lokalizacje) > 0) {
        $lokalizacja_id = intval($lokalizacje);
    }

    if ($lokalizacja_id > 0) {
      // query args
      $args = array(
        'numberposts'   => -1,
        'post_status'   => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
        'post_type'     => 'dane_firmy',
        'meta_query'	  => array(
          array(
            'key'		    => 'df_lokalizacje',
            'value'		  => $lokalizacja_id,
            'compare'	  => 'LIKE'
          )
        )
      );

      // query
      $firma   = get_posts($args);

      if (is_object($firma[0])) {
        // get locations
        $loks = get_field('df_lokalizacje', 'post_'.$firma[0]->ID);

        foreach ($loks as $lok) {
          $location = get_post($lok);
          $location->adres        = get_field('br_adres', 'post_'.$lok);
          $location->kod_pocztowy = get_field('br_kod_pocztowy', 'post_'.$lok);
          $location->miejscowosc  = get_field('br_miasto', 'post_'.$lok);
          $location->wojewodztwo  = get_field('br_wojewodztwo', 'post_'.$lok);
          $location->biuro_online = get_field('br_online', 'post_'.$lok) ? 'TAK' : 'NIE';
          $list[] = $location;
        }
      }
    }
  }

  return $list;
}

/**
 * WP Custom REST API method to get data of location
 *
 * @return object
 */
function wl_get_location()
{
  $current_user = wp_get_current_user();
  $lokalizacja_id  = intval($_POST['loc']);

  if ($current_user->ID) {
    // get locations
    $lokalizacje = get_field('br_lokalizacje', 'user_'.$current_user->ID);
    $admin_lokalizacje = get_field('br_admin_lokalizacje', 'user_'.$current_user->ID);
    $biuro_user = get_field('dane_firmy', 'user_'.$current_user->ID);

    if (is_array($lokalizacje)) {
      if (!in_array($lokalizacja_id, $lokalizacje)) {
        return false;
      }
    } else {
      if ($lokalizacja_id != $lokalizacje) {
        return false;
      }
    }
      
    $lokalizacja = get_post($lokalizacja_id);

    $lokalizacja->admin = '0';

    if (is_array($admin_lokalizacje)) {
      if (in_array($lokalizacja_id, $admin_lokalizacje)) {
        $lokalizacja->admin = '1';
      }
    } else {
      if ($lokalizacja_id == $admin_lokalizacje) {
        $lokalizacja->admin = '1';
      }
    }

    if (is_object($biuro_user)) {
      $biuro_lokalizacje = get_field('df_lokalizacje', 'post_'.$biuro_user->ID);

      if (is_array($biuro_lokalizacje)) {
        if (in_array($lokalizacja_id, $biuro_lokalizacje)) {
          $lokalizacja->admin = '1';
        }
      } else {
        if ($lokalizacja_id == $biuro_lokalizacje) {
          $lokalizacja->admin = '1';
        }
      }
    }

    if ($lokalizacja != null) {

      // Get all data
      $lokalizacja->obszar_dzialania = get_field('br_obszar_dzialania', 'post_' . $lokalizacja_id);
      $lokalizacja->adres          = get_field('br_adres', 'post_' . $lokalizacja_id);
      $lokalizacja->kod_pocztowy   = get_field('br_kod_pocztowy', 'post_' . $lokalizacja_id);
      $lokalizacja->miasto         = get_field('br_miasto', 'post_' . $lokalizacja_id);
      $lokalizacja->wojewodztwo    = get_field('br_wojewodztwo', 'post_' . $lokalizacja_id);
      $lokalizacja->email          = get_field('br_email', 'post_' . $lokalizacja_id);
      $lokalizacja->telefon        = get_field('br_telefon', 'post_' . $lokalizacja_id);
      $lokalizacja->fax            = get_field('br_fax', 'post_' . $lokalizacja_id);
      $lokalizacja->www            = get_field('br_www', 'post_' . $lokalizacja_id);
      $lokalizacja->online         = get_field('br_online', 'post_' . $lokalizacja_id);
      $lokalizacja->rodzaj_biura   = get_field('br_rodzaj_biura', 'post_' . $lokalizacja_id);
      $lokalizacja->zakres_uslug   = get_field('br_zakres_uslug', 'post_' . $lokalizacja_id);
      $lokalizacja->zakres_uslug_online = get_field('br_zakres_uslug_online', 'post_' . $lokalizacja_id);
      $lokalizacja->dodatkowy_opis = get_field('br_dodatkowy_opis', 'post_' . $lokalizacja_id);

      // Get location's staff
      // query args
      $args = array(
        'meta_query'	  => array(
          'relation' => 'OR',
          array(
            'key'		    => 'br_lokalizacje',
            'value'		  => $lokalizacja_id,
            'compare'	  => 'LIKE'
          )
        )
      );

      // query
      $user_query         = new WP_User_Query($args);

      $workers = [];
      $staff = $user_query->get_results();

      foreach ($staff as $item) {
        $worker = new \stdClass;
        $worker->ID         = $item->ID;
        $worker->first_name = get_user_meta($item->ID, 'first_name')[0];
        $worker->last_name  = get_user_meta($item->ID, 'last_name')[0];
        $worker->username   = $item->data->user_login;
        $worker->email      = $item->data->user_email;
        $workers[] = $worker;
      }

      $lokalizacja->workers = $workers;

      // Get location's clients
      $clients = get_field('br_klienci', 'post_'.$lokalizacja_id);

      if (!is_array($clients)) {
        if ($clients != null) {
          $clients = [$clients];
        } else {
          $clients = [];
        }
      }

      foreach ($clients as $key => $item) {
        $client = new \stdClass;
        $client->ID = $item;
        $client->nazwa_firmy  = get_field('cli_nazwa_firmy', 'post_'.$item);
        $client->nip          = get_field('cli_nip', 'post_'.$item);
        $client->email        = get_field('cli_email', 'post_'.$item);
        $client->adres        = get_field('cli_adres', 'post_'.$item);
        $client->kod_pocztowy = get_field('cli_kod_pocztowy', 'post_'.$item);
        $client->miasto       = get_field('cli_miasto', 'post_'.$item);
        $client->wojewodztwo  = get_field('cli_wojewodztwo', 'post_'.$item);
        $clients[$key] = $client;
      }

      $lokalizacja->clients = $clients;
      $lokalizacja->user_id = $current_user->ID;
    }
  } else {
    $lokalizacja = null;
  }

  return $lokalizacja;
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

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getRodoContract', [
    'methods' => 'POST',
    'callback' => 'wl_get_rodo'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getBiuroFirm', [
    'methods' => 'POST',
    'callback' => 'wl_get_biuro_firm'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getLocations', [
    'methods' => 'POST',
    'callback' => 'wl_get_locations'
  ]);
});

add_action('rest_api_init', function () {
  register_rest_route('wl/v1', 'getLocation', [
    'methods' => 'POST',
    'callback' => 'wl_get_location'
  ]);
});