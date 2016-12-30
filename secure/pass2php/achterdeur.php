<?php
if($status!="Open"){
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'){
		sw(apcu_fetch('isirene'),'On');
		telegram('Achterdeur open om '.strftime("%k:%M:%S",time),false,3);
	}
}