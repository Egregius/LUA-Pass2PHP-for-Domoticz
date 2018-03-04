<?php
error_reporting(E_ALL);ini_set("display_errors","on");
define('time',$_SERVER['REQUEST_TIME']);
date_default_timezone_set('Europe/Brussels');

//require_once '/var/www/html/secure/gcal/google-api-php-client-2.1.0/vendor/autoload.php';
require_once '/var/www/html/secure/gcal/google-api-php-client/vendor/autoload.php';
function getClient(){
	$client=new Google_Client();
	$client->setAuthConfig('/var/www/html/secure/gcal/service-account-credentials.json');
	$client->setApplicationName("homeegregius");
	$client->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
	return $client;
}
$client=getClient();
$service=new Google_Service_Calendar($client);
$calendarId='uue9pqcqnqhmdc2u1aeais16p8@group.calendar.google.com';
$timeMin=strftime("%Y-%m-%d",time()).'T'.strftime("%H:%M:%S",time()-7200).'+0000';//Winteruur
$optParams = array('maxResults' => 10,'orderBy' => 'startTime','singleEvents' => TRUE,'timeMin' => $timeMin);
$results = $service->events->listEvents($calendarId, $optParams);
echo 'timeMin = '.$timeMin.'<hr>';
echo '<pre>';
if(count($results->getItems())>0){
	foreach($results->getItems() as $event){
		//print_r($event);
    	if(isset($event->start->dateTime))$start=strtotime($event->start->dateTime);
    	if(isset($event->end->dateTime))$end=strtotime($event->end->dateTime);
    	if(empty($start))$start=strtotime($event->start->date);
    	$item=explode(" ", $event->summary);
		$action=strtolower($item[0]);
		$place=strtolower($item[1]);
		if(isset($item[2]))$detail=strtolower($item[2]);
    	echo $action.' '.$place.' '.$event->start->dateTime.' '.$event->end->dateTime;
//    	echo '<hr>';print_r($event);
    	echo '<hr>';
  	}
}
echo '</pre>';