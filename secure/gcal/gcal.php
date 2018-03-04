<?php
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
$timeMin=strftime("%Y-%m-%d",time()).'T'.strftime("%H:%M:%S",time-7200).'+0000';//Winteruur
$msg='GCAL time: '.$timeMin.PHP_EOL;
$optParams=array('maxResults'=>10,'orderBy'=>'startTime','singleEvents'=>TRUE,'timeMin'=>$timeMin);
$results=$service->events->listEvents($calendarId, $optParams);
if(count($results->getItems())>0){
	foreach($results->getItems() as $event){
    	if(isset($event->start->dateTime))$start=strtotime($event->start->dateTime);
    	if(isset($event->end->dateTime))$end=strtotime($event->end->dateTime);
    	if(empty($start))$start=strtotime($event->start->date);
    	//$msg.=strftime("%Y-%m-%d %H:%M:%S",$start).' '.$event->summary.PHP_EOL;
		if(time>$start&&time<$end){
			$user='GCal';
			$item=explode(" ", $event->summary);
			$action=strtolower($item[0]);
			if($action=="licht")$action="schakel";
			elseif($action=="dim")$action="dimmer";
			elseif($action=="opstaan")$action="wake";
			elseif($action=="slaap")$action="sleep";
			elseif($action=="set")$action="setpoint";
			$place=strtolower($item[1]);
			if(isset($item[2])){
				$detail=strtolower($item[2]);
				if($detail=="on")$detail="On";
				elseif($detail=="off")$detail="Off";
				elseif($detail=="aan")$detail="On";
				elseif($detail=="uit")$detail="Off";
			}
			$msg.=strftime("%Y-%m-%d %H:%M:%S",time()).'  => GCAL: '.$event->summary.' from '.$event->start->dateTime.' till '.$event->end->dateTime.PHP_EOL;
			if($action=="wake"){
				$stat=apcu_fetch($place);
				if($stat=='Off')$stat=0;
				if($place=='kamer'){
					if($stat<30&&apcu_fetch('dimaction'.$place)!=2&&apcu_fetch('raamalex')!='Open'&&apcu_fetch('T'.$place)<time()-10800){
						apcu_store('dimaction'.$place,2);
						sl($place,$stat+2);
					}
				}else{
					if($stat<30&&apcu_fetch('dimaction'.$place)!=2&&apcu_fetch('T'.$place)<time()-10800){
						apcu_store('dimaction'.$place,2);
						sl($place,$stat+2);
					}
				}
				$msg.='gcal: '.$place.' '.$action.', status='.$stat.PHP_EOL;
			}elseif($action=="sleep"){
				$stat=apcu_fetch($place);if($stat=='Off')$stat=0;
				if($stat>0&&apcu_fetch('dimaction'.$place)!=1){
					apcu_store('dimaction'.$place,1);
					sl($place,$stat-1);
				}
				$msg.='gcal: '.$place.' '.$action.', status='.$stat.PHP_EOL;
			}elseif($action=="dimmer"){
				$stat=apcu_fetch($place);if($stat=='Off')$stat=0;
				if($stat!=$detail&&apcu_fetch('T'.$place)<time()-600)sl($place,$detail,"GCAL: ".$place);
				$msg.='gcal: '.$place.' '.$action.', status='.$stat.PHP_EOL;
			}elseif($action=="schakel"){
				if(apcu_fetch($place)!=$detail&&apcu_fetch('T'.$place)<time()-600){
					sw($place,$detail,'GCAL: '.$place);
					apcu_store('T'.$place,$end);
				}
				$msg.='gcal: '.$place.' '.$action.', status='.$stat.PHP_EOL;
			}elseif($action=="setpoint"){
				apcu_store('setpoint'.$place,2);
				if(apcu_fetch($place)!=$detail&&apcu_fetch('T'.$place)<time()-600)ud($place.'_set',0,$detail,"GCAL: ".$place);
				$msg.='gcal: '.$place.' '.$action.', status='.$stat.PHP_EOL;
			}
		}
  	}
  	//if(strlen($msg)>40)telegram($msg);
}