<?php
$domoticz=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&used=true'),true);
if($domoticz){
	foreach($domoticz['result'] as $dom){
		$name=$dom['Name'];
		if(isset($dom['SwitchType']))$switchtype=$dom['SwitchType'];else $switchtype='none';
		apcu_store('t'.$name,strtotime($dom['LastUpdate']));
		apcu_store('i'.$name,$dom['idx']);
		if($dom['Type']=='Temp')apcu_store('s'.$name,str_replace(' C','',$dom['Data']));
		elseif($dom['TypeImg']=='current')apcu_store('s'.$name,str_replace(' Watt','',$dom['Usage']));
		elseif($switchtype=='Dimmer'){
			if($dom['Data']=='Off')apcu_store('s'.$name,'Off');
			else apcu_store('s'.$name,filter_var($dom['Data'],FILTER_SANITIZE_NUMBER_INT));
		}
		else apcu_store('s'.$name,$dom['Data']);
	}
}
if(!apcu_exists('Weg')){apcu_store('Weg',0);$Weg=0;}
$item='meldingen';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
$item='lichten_auto';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
$item='heating';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
$item='heatingmanual';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
$item='living';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',17);apcu_store('t'.$item.'_set',1);}
$item='badkamer';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',12);apcu_store('t'.$item.'_set',1);}
$item='kamer';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',12);apcu_store('t'.$item.'_set',1);}
$item='tobi';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',12);apcu_store('t'.$item.'_set',1);}
$item='alex';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',12);apcu_store('t'.$item.'_set',1);}
$item='diepvries';if(!apcu_exists('s'.$item.'_set')){apcu_store('s'.$item.'_set',-18);}
