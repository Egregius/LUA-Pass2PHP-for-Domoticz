<?php
if($s['achterdeur']!="Open"){if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'){sw($i['sirene'],'On');$msg='Achterdeur open om '.$t['achterdeur'];telegram($msg,false);ios($msg);}}
