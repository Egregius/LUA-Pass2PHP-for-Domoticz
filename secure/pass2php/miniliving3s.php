<?php
denon('MVDOWN');
denon('MVDOWN');
denon('MVDOWN');
denon('MVDOWN');
denon('MVDOWN');
denon('MVDOWN');

function denon($cmd){for($x=1;$x<=10;$x++)if(denontcp($cmd,$x))break;}
function denontcp($cmd,$x){
	$sleep=102000*$x;
	$socket=fsockopen("192.168.2.4","23",$errno,$errstr,2);
	if($socket){fputs($socket, "$cmd\r\n");fclose($socket);usleep($sleep);return true;}
	else{usleep($sleep);echo 'sleeping '.$sleep.'<br>';return false;}
}
if($Weg!=0)setstatus('Weg',0);
?>