<?php
if($Weg!=0){apcu_store('Weg',0);apcu_store('tWeg',time);}
if(apcu_fetch('shall')=='Off')sw('hall','On');
