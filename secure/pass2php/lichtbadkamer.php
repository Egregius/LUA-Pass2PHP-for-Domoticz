<?php
include('__verwarmingbadkamer.php');
if(apcu_fetch('tlichtbadkamer1')<time-5&&apcu_fetch('tlichtbadkamer1')<time-5){
	sleep(5);
	RefreshZwave(11);
}
