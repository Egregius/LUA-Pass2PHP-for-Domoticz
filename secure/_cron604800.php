<?php
$domoticz=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&used=true'),true);
if($domoticz){
	foreach($domoticz['result'] as $dom){
		$name=$dom['Name'];
		if(isset($dom['SwitchType']))$switchtype=$dom['SwitchType'];else $switchtype='none';
		settimestamp($name,strtotime($dom['LastUpdate']));
		setidx($name,$dom['idx']);
		if($dom['Type']=='Temp')setstatus($name,str_replace(' C','',$dom['Data']));
		elseif($dom['TypeImg']=='current')setstatus($name,str_replace(' Watt','',$dom['Data']));
		elseif($name=='luifel')setstatus($name,str_replace('%','',$dom['Level']));
		elseif($switchtype=='Dimmer'){
			if($dom['Data']=='Off')setstatus($name,'Off');
			else setstatus($name,filter_var($dom['Data'],FILTER_SANITIZE_NUMBER_INT));
		}
		else setstatus($name,$dom['Data']);
	}
}
if(!file_exists('/var/log/cache-Weg')){setstatus('Weg',0);$Weg=0;}
$item='meldingen';if(status($item)==0)setstatus($item,'On');
$item='lichten_auto';if(status($item)==0)setstatus($item,'On');
$item='living';if(status($item)==0)setstatus($item.'_set',17);
$item='badkamer';if(status($item)==0)setstatus($item.'_set',12);
$item='kamer';if(status($item)==0)setstatus($item.'_set',12);
$item='tobi';if(status($item)==0)setstatus($item.'_set',12);
$item='alex';if(status($item)==0)setstatus($item.'_set',12);
$item='diepvries';if(status($item)==0)setstatus($item.'_set',-18);
if(in_array(date('n'),array(5,6,7,8,9))){
	$item='heating';if(status($item)==0)setstatus('s'.$item,'Off');
	$item='heatingmanual';if(status($item)==0)setstatus('s'.$item,'Off');
}else{
	$item='heating';if(status($item)==0)setstatus('s'.$item,'On');
	$item='heatingmanual';if(status($item)==0)setstatus('s'.$item,'On');
}
?>
