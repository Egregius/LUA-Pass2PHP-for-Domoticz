<?php
if(strtotime($t['inkom'])<time-5&&strtotime($t['voordeur'])<time-5)RefreshZwave(8);