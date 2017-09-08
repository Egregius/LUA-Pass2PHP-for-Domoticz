<?php
if($Weg!=0)setstatus('Weg',0);
if(status('hall')=='Off')sw('hall','On');
if(status('sirene')!='Group Off')sw('sirene','Off');