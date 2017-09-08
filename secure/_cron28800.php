<?php
$db=new mysqli('server','user','password','database');
if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');
$sql="SELECT id,date,value FROM battery t1 WHERE t1.date = (SELECT t2.date FROM battery t2 WHERE t2.id = t1.id ORDER BY t2.date DESC LIMIT 1);";
if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.' - '.$db->error.']');}
while($row = $result->fetch_assoc()){
	$batterydevices[]=$row['id'];
	$items[$row['id']]=$row;
}
$result->free();
$date=strftime("%F",time);
$xml=json_decode(json_encode(simplexml_load_string(file_get_contents('/var/log/zwcfg_0xe9238f6e.xml'),"SimpleXMLElement",LIBXML_NOCDATA)),TRUE);
foreach($xml['Node'] as $node){
	foreach($node['CommandClasses']['CommandClass'] as $cmd){
		if(isset($cmd['Value']['@attributes']['label'])){
			if($cmd['Value']['@attributes']['label']=='Battery Level'){
				$id=$node['@attributes']['id'];
				$name=$node['@attributes']['name'];
				$value=$cmd['Value']['@attributes']['value'];
				if(!in_array($id,$batterydevices)){
					$query="INSERT INTO `batterydevices` (`id`,`name`) VALUES ('$id','$name') ON DUPLICATE KEY UPDATE `name`='$name';";
					if(!$result=$db->query($query))die('There was an error running the query ['.$query.' - '.$db->error.']');
				}
				if($items[$id]['value']!=$value){
					telegram('Batterij '.$name.' '.$value.'%');
					$query="INSERT INTO `battery` (`date`,`id`,`value`) VALUES ('$date','$id','$value') ON DUPLICATE KEY UPDATE `value`='$value';";
					if(!$result=$db->query($query))die('There was an error running the query ['.$query.' - '.$db->error.']');
				}
			}
		}

	}
}
unset($xml);
$db->close();
?>
