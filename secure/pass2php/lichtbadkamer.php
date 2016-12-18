<?php
include('pass2php/badkamer_temp.php');
if(strtotime($t['lichtbadkamer1'])<time-5&&strtotime($t['lichtbadkamer1'])<time-5)
{
	sleep(5);
	RefreshZwave(11);
}