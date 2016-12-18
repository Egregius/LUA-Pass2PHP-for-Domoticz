<?php
if($s['poort']=='Open'){if($s['zon']<1000&&$s['garage']=='Off')sw($i['garage'],'On','garage');alarm('poort');}