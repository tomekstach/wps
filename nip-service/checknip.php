<?php
// enable debug information
ini_set('display_errors', 0);
error_reporting(E_ALL);

// nip24 api
require_once 'NIP24/NIP24Client.php';

\NIP24\NIP24Client::registerAutoloader();

// Utworzenie obiektu klienta usługi serwisu produkcyjnego
// id – ciąg znaków reprezentujący identyfikator klucza API
// key – ciąg znaków reprezentujący klucz API
// $nip24 = new \NIP24\NIP24Client('id', 'key');

// Utworzenie obiektu klienta usługi serwisu testowego
$nip24 = new \NIP24\NIP24Client('wRocgSXQIItj', '2PEXnwYwCwVA');

//$nip = '7171642051';
$nip = preg_replace('/\s+/', '', str_replace('-', '', strip_tags($_GET['nip'])));
$nip_eu = 'PL' . $nip;

// Sprawdzenie stanu konta
$account = $nip24->getAccountStatus();

$json = new \stdClass;
$json->code = 200;
$json->content = 'OK';

if (!$account) {
  $json->code = 403;
  $json->content = $nip24->getLastError();
} else {
  // Wywołanie metody zwracającej szczegółowe dane firmy
  $all = $nip24->getAllDataExt(\NIP24\Number::NIP, $nip, false);

  if ($all) {
    $data = new \stdClass;
    $data->name     = $all->name;
    $data->address  = $all->street;

    if (empty($data->address)) {
      $data->address = $all->city;
    }

    $data->address .= ' ' . $all->streetNumber;

    if (!empty($all->houseNumber)) {
      $data->address .= '/' . $all->houseNumber;
    }

    $data->city       = $all->postCity;
    if ($all->postCode) {
      $data->postCode   = substr($all->postCode, 0, 2) . '-' . substr($all->postCode, -3);
    } else {
      $data->postCode = '';
    }
    $data->firstname  = $all->firstname;
    $data->lastname   = $all->lastname;
    $data->state      = $all->state;

    $json->code = 200;
    $json->content = $data;
  } else {
    $json->code = 403;
    $json->content = $nip24->getLastError();
  }
}

/*$url    = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
//$url    = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

$client = new SoapClient($url, array("trace" => 1, "exception" => 0));

$params   = array('ArrayDPAgreementGetData' => array('DPAgreementGetData' => array('NIPSameCyfry' => $nip)));
$json->DPAgreementGetData = $client->DPAgreementGet($params);*/

$out = html_entity_decode(stripslashes(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)));

header('Content-Type: application/json');
header('Content-type: text/html; charset=UTF-8');

echo $out;