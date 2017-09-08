<?php
if(status('deurbadkamer')=='Open'&&$status=='Off'&&status('badkamervuur')=='On')sw('badkamervuur','Off');
if(timestamp('lichtbadkamer1')<time-5&&timestamp('lichtbadkamer1')<time-5){
	sleep(5);
	RefreshZwave(11);
}
