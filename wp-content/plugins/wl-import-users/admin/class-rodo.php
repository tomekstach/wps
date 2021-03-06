<?php

function wl_rodo_page_html()
{
  global $wpdb;

    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()); ?></h1>
      <?php settings_fields('wl_rodo_options_group'); ?>
      <?php do_settings_sections('wl_rodo_page_html'); ?>
      <form method="post" action="tools.php?page=wl-rodo">
        <input type="hidden" name="wl_rodo_type" value="check">
        <?php
        submit_button('Check Contracts');
        ?>
      </form>
      <form method="post" action="tools.php?page=wl-rodo">
    <?php

    if (array_key_exists('wl_rodo_type', $_POST)) {
      if ($_POST['wl_rodo_type'] == 'check') {
        //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
        $url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

        $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

        // TODO: Write standard SQL query to get all umowa_rodo which dos not have zapisana_w_erp
        $args = array(
          'post_type'=> 'umowa_rodo'
        );     
        
        $posts = $wpdb->get_results( "SELECT ID FROM {$wpdb->prefix}posts WHERE ID NOT IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'zapisana_w_erp' AND meta_value = 'tak') AND post_parent = '0' AND post_type = 'umowa_rodo' LIMIT 0, 10", OBJECT );
        //$posts = $wpdb->get_results( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = '0' AND post_type = 'umowa_rodo' LIMIT 0, 10", OBJECT );
        //$posts = $wpdb->get_results( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'zapisana_w_erp' AND meta_value = 'tak' LIMIT 0, 10", OBJECT );

        //print_r($posts);

        // 55951
        //$contract = get_post('55992');
        //print_r($contract);
        ?>
        <table>
          <thead>
            <th></th>
            <th>ID</th>
            <th>NIP</th>
            <th>Imię i nazwisko</th>
            <th>Data zgłoszenia</th>
            <th>Data podpisania umowy</th>
            <th>Rodzaj umowy</th>
            <th>Wysłana do ERP</th>
            <th>Umowy w ERPie</th>
          </thead>
          <tbody>
        <?php
        foreach ($posts as $contract) {
          $umowa = new \stdClass;
          $umowa->id                  = $contract->ID;
          $umowa->NIP                 = get_field('nip_klienta', $umowa->id);
          $umowa->imie_i_nazwisko     = get_field('imie_i_nazwisko', $umowa->id);
          $umowa->data_zgloszenia     = get_the_date('d.m.Y H:i', $umowa->id);
          $umowa->data_podpisania_umowy = get_field("data_podpisania_umowy", $umowa->id);
          $umowa->rodzaj_umowy        = $rodzaj_umowy = get_field("rodzaj_umowy", $umowa->id);
          $umowa->zapisana_w_erp      = get_field("zapisana_w_erp", $umowa->id);

          if (is_array($umowa->zapisana_w_erp)) {
            if (count($umowa->zapisana_w_erp) > 0) {
              $umowa->zapisana_w_erp = $umowa->zapisana_w_erp[0];
            } else {
              $umowa->zapisana_w_erp = false;
            }
          } else {
            $umowa->zapisana_w_erp = false;
          }

          $umowa->ERP = new \stdClass;
          $umowa->ERP->DataPodpisania = '';

          if ($umowa->zapisana_w_erp != 'tak') {
            $paramsAgreement   = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $umowa->NIP]]];
            $responseAgreement = $client->DPAgreementGet($paramsAgreement);

            if (is_array($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult)) {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult[count($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult) - 1];
            } else {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult;
            }

            $umowa->ERP->DataPodpisania = $DPAgreementGetResult->DataPodpisania;

            if ($DPAgreementGetResult->Hosting == '1') {
              if (isset($umowa->ERP->rodzaje)) {
                $umowa->ERP->rodzaje .= ', ';
              }
              $umowa->ERP->rodzaje .= 'hosting';
            }

            if ($DPAgreementGetResult->Konserwacja == '1') {
              if (isset($umowa->ERP->rodzaje)) {
                $umowa->ERP->rodzaje .= ', ';
              }
              $umowa->ERP->rodzaje .= 'konserwacja';
            }

            if ($DPAgreementGetResult->Outsourcing == '1') {
              if (isset($umowa->ERP->rodzaje)) {
                $umowa->ERP->rodzaje .= ', ';
              }
              $umowa->ERP->rodzaje .= 'outsourcing';
            }

            if ($DPAgreementGetResult->UruchTestProg == '1') {
              if (isset($umowa->ERP->rodzaje)) {
                $umowa->ERP->rodzaje .= ', ';
              }
              $umowa->ERP->rodzaje .= 'uruchTestProg';
            }

            if ($DPAgreementGetResult->$rodzaj_umowy == '1') {
              update_field('zapisana_w_erp', 'tak', 'post_' . $umowa->id);
              $umowa->zapisana_w_erp = 'tak';
            }
          }

          if (!isset($umowa->ERP->rodzaje)) {
            $umowa->ERP->rodzaje = '';
          }
          ?>
          <tr>
            <td><?php if ($umowa->data_podpisania_umowy != '' and $umowa->zapisana_w_erp != 'tak'):?><input type="checkbox" name="rodo[]" value="<?php echo $umowa->id;?>"><?php endif;?></td>
            <td><?php echo $umowa->id;?></td>
            <td><?php echo $umowa->NIP;?></td>
            <td><?php echo $umowa->imie_i_nazwisko;?></td>
            <td><?php echo $umowa->data_zgloszenia;?></td>
            <td><?php echo $umowa->data_podpisania_umowy;?></td>
            <td><?php echo $umowa->rodzaj_umowy;?></td>
            <td><?php echo $umowa->zapisana_w_erp;?></td>
            <td>Data podpisania: <?php echo $umowa->ERP->DataPodpisania;?><br/>Rodzaj umowy: <?php echo $umowa->ERP->rodzaje;?></td>
          </tr>
          <?php
        }?>
          </tbody>
        </table>
        <?php
        /* Send rodo contacts to the ERP */
      } elseif ($_POST['wl_rodo_type'] == 'add') {
        echo 'rodo: ';
        print_r($_POST['rodo']);

        //$url = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
        $url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

        $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

        foreach ($_POST['rodo'] as $contract) {
          $umowa = new \stdClass;
          $umowa->id                  = $contract;
          $umowa->NIP                 = get_field('nip_klienta', $umowa->id);
          $umowa->imie_i_nazwisko     = get_field('imie_i_nazwisko', $umowa->id);
          $umowa->data_zgloszenia     = get_the_date('d.m.Y H:i', $umowa->id);
          $umowa->data_podpisania_umowy = get_field("data_podpisania_umowy", $umowa->id);
          $umowa->rodzaj_umowy        = $rodzaj_umowy = get_field("rodzaj_umowy", $umowa->id);
          $umowa->zapisana_w_erp      = get_field("zapisana_w_erp", $umowa->id);

          $paramsCustomer     = array('ArrayCustomerGetData' => array('CustomerGetData' => array('NIPSameCyfry' => $nip)));
          $responseCustomer   = $client->CUSTOMERGET($paramsCustomer);

          $paramsAgreement   = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $nip]]];
          $responseAgreement = $client->DPAgreementGet($paramsAgreement);

          if ($responseAgreement->ArrayDPAgreementGetResult->Status != '0') {
            if (is_array($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult)) {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult[count($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult) - 1];
            } else {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult;
            }
          } else {
            $DPAgreementGetResult = new \stdClass;
            $DPAgreementGetResult->Konserwacja    = '0';
            $DPAgreementGetResult->Outsourcing    = '0';
            $DPAgreementGetResult->Hosting        = '0';
            $DPAgreementGetResult->UruchTestProg  = '0';
          }

          if ($data['rodzaj_umowy'] == 'Hosting') {
            $Hosting = 1;
          } else {
            $Hosting = $DPAgreementGetResult->Hosting;
          }

          if (strpos($data['rodzaj_umowy'], 'Uruchomienie') !== false) {
            $UruchTestProg = 1;
          } else {
            $UruchTestProg = $DPAgreementGetResult->UruchTestProg;
          }
        }
      }
    }
    ?>
        <input type="hidden" name="wl_rodo_type" value="add">
        <?php
        submit_button('Add Contracts to ERP');
        ?>
      </form>
    </div>
    <?php
}

function wl_rodo_main_settings_cb(){
  global $wpdb;
}