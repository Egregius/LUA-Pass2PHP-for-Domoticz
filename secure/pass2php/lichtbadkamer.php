<?php
if(apcu_fetch('tlichtbadkamer1')<time-5&&apcu_fetch('tlichtbadkamer1')<time-5){
	sleep(5);
	RefreshZwave(11);
}
include('__verwarmingbadkamer.php');