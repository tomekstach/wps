<?php
// enable debug information
ini_set('display_errors', 0);
error_reporting(E_ALL);

$nip = preg_replace('/\s+/', '', str_replace('-', '', strip_tags($_GET['nip'])));
if (key_exists('check', $_GET)) {
  $check = intval($_GET['check']);
} else {
  $check = 0;
}

if ($check == 1) {

  $json = new \stdClass;
  $json->code = 200;

  //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  $url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  $params   = array('ArrayCustomerGetData' => array('CustomerGetData' => array('NIPSameCyfry' => $nip)));
  $json->content = $client->CUSTOMERGET($params);

  $out = html_entity_decode(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  header('Content-Type: application/json');
  header('Content-type: text/html; charset=UTF-8');

  echo $out;

} elseif ($check == 2) {

  $json = new \stdClass;
  $json->code = 200;

  //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  $url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  $paramsAgreement   = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $nip]]];
  $params   = array('ArrayCustomerGetData' => array('CustomerGetData' => array('NIPSameCyfry' => $nip)));
  $json->content = $client->CUSTOMERGET($params);
  $json->responseAgreement = $client->DPAgreementGet($paramsAgreement);

  $out = html_entity_decode(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  header('Content-Type: application/json');
  header('Content-type: text/html; charset=UTF-8');

  echo $out;

} elseif ($check == 3) {

  //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  /*$url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  $params = ['ArrayAgreementCreateData' => ['AgreementCreateData' => [
        'NrZewn' => '1584000928',
        'Zrodlo' => 'Wapro',
        'NIPSameCyfry' => '6551690363',
        'WersjaUmowy' => '20190624',
        'DataPodpisania' => '2020-03-12T09:15:28',
        'RealizacjaOd' => '2020-03-12T09:15:28',
        'OpisUmowy' => 'Umowa powierzenia przetwarzania danych osobowych',
        'EDOK' => '1',
        'Aneks' => '0',
        'RodzajUmocowania' => 'Przedsiębiorca jednoosobowy',
        'ImieNazwisko' => 'Paweł Sokołowski',
        'DataDo' => '2050-12-31T00:00:00',
        'RealizacjaDo' => '2050-12-31T00:00:00',
        'Konserwacja' => '0',
        'Outsourcing' => '0',
        'DaneKontaktoweDPO' => 'brak',
        'MailDoZglaszaniaNaruszen' => 'p.sokolowski@trowal.pl',
        'UruchTestProg' => '0',
        'Hosting' => '1'
        ]]];
  $response = $client->AgreementCreate($params);

  print_r($response);*/

} elseif ($check == 4) {

  //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  /*$url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  // Create user in ERP
  $paramsCreateCustomer = ['ArrayHostingCustomerCreateData' => ['HostingCustomerCreateData' => [
    'Id' => '1583744989',
    'DataDodania' => '2020-03-09T10:09:49',
    'KlientImie' => 'Romana',
    'KlientNazwisko' => 'Świderska',
    'KlientNazwaFirmy' => 'Romana Świderska',
    'KlientKodP' => '04-359',
    'KlientMiasto' => 'Warszawa',
    'KlientUlica' => 'ul. Kobielska 17/25',
    'KlientNIP' => '5222854188',
    'KlientNipSameCyfry' => '5222854188',
    'KlientTelefon' => '223439473',
    'KlientEmail' => 'biuro@newhome24.eu',
    'KontaktImie' => 'Romana',
    'KontaktNazwisko' => 'Świderska',
    'KontaktTelefon' => '609547497',
    'KontaktEmail' => 'biuro@newhome24.eu',
    'KontakUwagi' => '',
    'ProgramOnline' => 'N',
    'ProgramMag' => 0,
    'ProgramMagBiznes' => 0,
    'ProgramAukcje' => 0,
    'ProgramFakir' => 0,
    'ProgramKaper' => 2,
    'ProgramGang' => 0,
    'ProgramBest' => 0,
    'ProgramAnalizy' => 0,
    'ProgramMagMobileAndroid' => 0,
    'ProgramMagMobileWindows' => 0,
    'ProgramMagMobilePDA' => 0,
    'ProgramJPK' => 0,
    'ProgramJPKBiuro' => 0,
    'PartnerNipSameCyfry' => '',
    'PartnerEmail' => '',
    'BiuroNipSameCyfry' => '',
    'BiuroEmail' => '',
    'ZgodaRegulamin' => 'T',
    'ZgodaEFaktury' => 'T',
    'ZgodaEmailEFaktury' => 'biuro@newhome24.eu',
    'ZgodaInfHandlowe' => 'T',
    'ZgodaEmail' => 'N',
    'ZgodaSMS' => 'N',
    'ZgodaKontaktKonsult' => 'N'
  ]]];
  $response = $client->HostingCustomerCreate($paramsCreateCustomer);

  print_r($response);*/

} elseif ($check == 5) {

  $json = new \stdClass;
  $json->code = 200;

  //$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  $url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  $paramsAgreement   = ['ArrayDPAgreementGetData' => ['DPAgreementGetData' => ['NIPSameCyfry' => $nip]]];
  $params   = array('ArrayCustomerGetData' => array('CustomerGetData' => array('NIPSameCyfry' => $nip)));
  $json->content = $client->CUSTOMERGET($params);
  $json->responseAgreement = $client->DPAgreementGet($paramsAgreement);

  $out = html_entity_decode(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  header('Content-Type: application/json');
  header('Content-type: text/html; charset=UTF-8');

  echo $out;

} else {

  $json = new \stdClass;
  $json->code = 200;

  //$url = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
  $url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

  $client = new SoapClient($url, array("trace" => 1, "exception" => 0));

  if ($nip != '') {
    $params = array('ArrayDPAgreementGetData' => array('DPAgreementGetData' => array('NIPSameCyfry' => $nip)));
    $json->content = $client->DPAgreementGet($params);
  }

  $out = html_entity_decode(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  header('Content-Type: application/json');
  header('Content-type: text/html; charset=UTF-8');

  echo $out;
}