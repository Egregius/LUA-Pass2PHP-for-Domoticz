<?php
//Basically nothing to do with apcu, just a page to show the file cache
require('/var/www/html/secure/settings.php');
error_reporting(E_ALL);
ini_set("display_errors","on");
define('time',$_SERVER['REQUEST_TIME']);
$time=time();
$start=microtime(true);
/*-------------------------------------------------*/
echo '<table><thead><tr><th>IDX</th><th>Name</th><th>Value</th><th>Updated</th><th>Ago</th></tr></thead><tbody>';
foreach (glob('/var/log/cache/*.cache') as $file){
	$name=str_replace('/var/log/cache/','',str_replace('.cache','',$file));
	$ftime=timestamp(substr($name,1));
	$ago=$time-$ftime;
	if(startsWith($name,'s')){
		$device=substr($name,1);
		echo '<tr><td>'.idx($device).'</td><td>'.$device.'</td><td>'.status($device).'</td><td>'.strftime("%Y-%m-%d %H:%M:%S",$ftime).'</td><td align="right">&nbsp;'.convertToHours($ago).'</td></tr>';
	}
}
echo '</tbody></table>';
/*-------------------------------------------------*/

$total=microtime(true)-$start;
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000),6);
?>
