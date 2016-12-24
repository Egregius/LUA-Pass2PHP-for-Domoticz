<?php
if(apcu_fetch('tkeuken')<time-5&&apcu_fetch('tzolderg')<time-5)RefreshZwave(5);