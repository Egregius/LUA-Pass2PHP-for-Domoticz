<?php
if(apcu_fetch('twerkblad')<time-5&&apcu_fetch('twerkblad2')<time-5)RefreshZwave(7);