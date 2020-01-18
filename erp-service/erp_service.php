<?php
// enable debug information
ini_set('display_errors', 0);
error_reporting(E_ALL);

$nip = preg_replace('/\s+/', '', str_replace('-', '', strip_tags($_GET['nip'])));

$json = new \stdClass;
$json->code = 200;

$url = 'https://mcl.assecobs.pl/ERP_Service/services_integration_api/ApiWebService.ashx?wsdl&DBC=ABS_TEST';
//$url = 'https://mcl.assecobs.pl/ERP_Service_Prod/services_integration_api/ApiWebService.ashx?wsdl&dbc=ABS_PROD';

$client = new SoapClient($url, array("trace" => 1, "exception" => 0));

if ($nip != '') {
  $params = array('ArrayDPAgreementGetData' => array('DPAgreementGetData' => array('NIPSameCyfry' => $nip)));
  $json->content = $client->DPAgreementGet($params);
}

$out = html_entity_decode(stripslashes(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)));

header('Content-Type: application/json');
header('Content-type: text/html; charset=UTF-8');

echo $out;