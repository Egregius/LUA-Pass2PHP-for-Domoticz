<?php
if(apcu_fetch('twasbak')<time-5&&apcu_fetch('tkookplaat')<time-5)RefreshZwave(6);