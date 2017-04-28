<?php
if($status=='Open'){
	if(($weg||$slapen)&&$meldingen){
		sw('sirene','On');
		telegram('Raam living open om '.strftime("%k:%M:%S",time),false,3);
	}
}