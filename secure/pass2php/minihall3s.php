<?php
if($s['slapen']=='On')sw(apcu_fetch('islapen'),'Off','slapen');if($s['hall']=='Off')sw(apcu_fetch('ihall'),'On','hall');