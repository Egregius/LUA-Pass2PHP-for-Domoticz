<?php
if($status> -15){
	if(timestamp('telegramdiepvries')<time-1800){
		telegram('Te warm in diepvries! '.$status.' °C',false,2);
		sw('diepvries','On');
		settimestamp('telegramdiepvries');
	}
}
if(time>1496489000){
	$room='diepvries';
	$prev=status('diepvries_temp');
	$set=status('diepvries_set');
	$tdiepvries=timestamp('diepvries');
	if(     $status < $prev && $status <= $set && $tdiepvries < time-7200 )sw('diepvries','Off aaa',' prev='.$prev.', new='.$status);
	elseif( $status > $prev && $status >= $set && $tdiepvries < time-7200 )sw('diepvries','On bbb', ' prev='.$prev.', new='.$status);
}