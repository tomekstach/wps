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
    <?php

    if (array_key_exists('wl_rodo_type', $_POST)) {
      if ($_POST['wl_rodo_type'] == 'check') {
        $url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
        //$url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

        $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

        // args
        $args = array(
          'numberposts'   => -1,
          'post_status '  => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
          'post_type'     => 'umowa_rodo'
        );

        // query
        $posts = get_posts($args);
        echo count($posts);
        ?>
        <table>
          <thead>
            <th>ID</th>
            <th>NIP</th>
            <th>Imię i nazwisko</th>
            <th>Data zgłoszenia</th>
            <th>Data podpisania umowy</th>
            <th>Rodzaj umowy</th>
            <th>Wysłana do ERP</th>
          </thead>
          <tbody>
        <?php
        foreach ($posts as $post) {
          $umowa = new \stdClass;
          $umowa->id                  = $post->ID;
          $umowa->NIP                 = get_field('nip_klienta', $umowa->id);
          $umowa->imie_i_nazwisko     = get_field('imie_i_nazwisko', $umowa->id);
          $umowa->data_zgloszenia     = get_the_date('d.m.Y H:i', $umowa->id);
          $umowa->data_podpisania_umowy = get_field("data_podpisania_umowy", $umowa->id);
          $umowa->rodzaj_umowy        = get_field("rodzaj_umowy", $umowa->id);
          $umowa->zapisana_w_erp      = get_field("zapisana_w_erp", $umowa->id);

          if ($umowa->zapisana_w_erp != 'tak') {
            $paramsAgreement   = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $umowa->NIP]]];
            $responseAgreement = $client->DPAgreementGet($paramsAgreement);

            if (is_array($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult)) {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult[count($responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult) - 1];
            } else {
              $DPAgreementGetResult = $responseAgreement->ArrayDPAgreementGetResult->DPAgreementGetResult;
            }
          }
          ?>
          <tr>
            <td><?php echo $umowa->id;?></td>
            <td><?php echo $umowa->NIP;?></td>
            <td><?php echo $umowa->imie_i_nazwisko;?></td>
            <td><?php echo $umowa->data_zgloszenia;?></td>
            <td><?php echo $umowa->data_podpisania_umowy;?></td>
            <td><?php echo print_r($umowa->rodzaj_umowy, true);?></td>
            <td><?php echo $umowa->zapisana_w_erp;?></td>
          </tr>
          <?php
        }?>
          </tbody>
        </table>
        <?php
        /**/
      } elseif ($_POST['wl_rodo_type'] == 'add') {

      }
    }
    ?>
      <form method="post" action="tools.php?page=wl-rodo">
        <input type="hidden" name="wl_rodo_type" value="check">
        <?php
        submit_button('Check Contracts');
        ?>
      </form>
      <form action="options.php" method="post">
        <?php settings_fields('wl_rodo_options_group'); ?>
        <?php do_settings_sections('wl_rodo_page_html'); ?>
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