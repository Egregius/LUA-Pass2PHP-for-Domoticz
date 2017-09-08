<?php
$domoticz=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&rid=617'),true);
if(isset($domoticz['result'][0]['Level'])){
	setstatus('luifel',$domoticz['result'][0]['Level']);
	//telegram('Status: '.$status.PHP_EOL.'Level: '.$domoticz['result'][0]['Level']);
}
?>