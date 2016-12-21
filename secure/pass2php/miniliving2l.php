<?php
if($s['kodi']=='Off')sw($i['kodi'],'On','Kodi');
if($s['denon']=='Off')sw($i['denon'],'On','Denon');
if($s['tv']=='Off')sw($i['tv'],'On','TV');
if($s['zon']<100&&$s['tvled']=='Off')sw($i['tvled'],'On','tvled');
elseif($s['tv']=='On'&&$s['tvled']=='Off')sw($i['tvled'],'On','tvled');
file_get_contents(denon.'MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD',false,$ctx);usleep(800000);
file_get_contents(denon.'MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-40.0',false,$ctx);