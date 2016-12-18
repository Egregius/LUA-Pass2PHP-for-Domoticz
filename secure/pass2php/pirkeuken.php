<?php
if($s['pirkeuken']=="On")
{
	if($s['keuken']=='Off'&&$s['wasbak']=='Off'&&$s['werkblad']=='Off'&&$s['kookplaat']=='Off'&&$s['zon']<500)sw($i['wasbak'],'On','wasbak');
	alarm('keuken');
}