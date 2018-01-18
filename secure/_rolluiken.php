<?php $kwartier=870;$tweeuur=7170;$msg='Rolluiken__';

$items=array('Rliving','Rbureel','RkeukenL','RkeukenR','Rtobi','Ralex','RkamerL','RkamerR','luifel','raamliving','raamtobi','raamalex','raamkamer','zonOP','buiten_temp','living_temp','tobi_temp','alex_temp','kamer_temp','heatingauto','modeRliving','modeRbureel','modeRkeukenL','modeRkeukenR','modeRtobi','modeRalex','modeRkamerL','modeRkamerR','alexslaapt','tobislaapt','wijslapen');foreach($items as $i)${$i}=apcu_fetch($i);
$items=array('Rliving','Rbureel','RkeukenL','RkeukenR','Rtobi','Ralex','RkamerL','RkamerR');foreach($items as $i)${'T'.$i}=past($i);
if(time<strtotime('7:00')||time>=strtotime('22:00'))$dag='nacht';
if(time>=strtotime('7:00')&&time<strtotime('8:30'))$dag='ochtend';
if(time>=strtotime('8:30')&&time<strtotime('12:30'))$dag='AM';
if(time>=strtotime('12:30')&&time<strtotime('16:30'))$dag='PM';
if(time>=strtotime('16:30')&&time<strtotime('22:00'))$dag='avond';
$boven=array('Rtobi','Ralex','RkamerL','RkamerR');$beneden=array('Rbureel','RkeukenL','RkeukenR');$benedena=array('Rliving','Rbureel','RkeukenL','RkeukenR');

$buiensince=strftime('%F %X',time-1800);
$buienhist=mysqli_fetch_assoc(mysqli_query($db, "SELECT sum(buien) as buienhist FROM `regen` WHERE `stamp` > '$buiensince'"));
$buienhist=$buienhist['buienhist'];
$hetregent=apcu_fetch('hetregent');
if($buienhist>=500&&$hetregent!=2){apcu_store('hetregent',2);$hetregent=2;}
elseif($buienhist>=100&&$hetregent<1){apcu_store('hetregent',1);$hetregent=1;}
elseif($buienhist<20&&$hetregent>0){apcu_store('hetregent',0);$hetregent=0;}
if($hetregent>=2)$open=25;else $open=0;
if($hetregent>=1)$openb=40;else $openb=0;
if($heatingauto=='On'){$msg.='Winter__';
	$msg.=$dag.'__';
	if($Weg==0){$msg.='Thuis__';
		if($dag=='nacht'){
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
				foreach($boven as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
		}elseif($dag=='ochtend'){
			if($zonOP&&$zon==0){$msg.='ZonOP && Zon = 0__';
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
			}elseif($zonOP){$msg.='ZonOP && Zon = '.$zon.'__';
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
				foreach($boven as $i){
					if($i=='Rtobi')$slaap=$tobislaapt;elseif($i=='Ralex')$slaap=$alexslaapt;else$slaap=$wijslapen;
					if(${'mode'.$i} && ${$i}!=$openb && ${'T'.$i}>$kwartier && !$slaap){
						sl($i,$openb);$msg.=$i.' '.$openb.'__';
					}
				}
			}
		}elseif($dag=='AM'){
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
				foreach($boven as $i){
					if($i=='Rtobi')$slaap=$tobislaapt;elseif($i=='Ralex')$slaap=$alexslaapt;else$slaap=$wijslapen;
					if(${'mode'.$i} && ${$i}>$openb && ${'T'.$i}>$kwartier && !$slaap){
						sl($i,$open);$msg.=$i.' '.$openb.'__';
					}elseif(${'mode'.$i} && ${$i}<$openb && ${'T'.$i}>$kwartier){
						sl($i,$openb);$msg.=$i.' '.$openb.'__';
					}
				}
		}elseif($dag=='PM'){
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
				foreach($boven as $i){
					if($i=='Rtobi')$slaap=$tobislaapt;elseif($i=='Ralex')$slaap=$alexslaapt;else$slaap=$wijslapen;
					if(${'mode'.$i} && ${$i}>$openb && ${'T'.$i}>$kwartier && !$slaap){
						sl($i,$open);$msg.=$i.' '.$openb.'__';
					}elseif(${'mode'.$i} && ${$i}<$openb && ${'T'.$i}>$kwartier){
						sl($i,$openb);$msg.=$i.' '.$openb.'__';
					}
				}
		}elseif($dag=='avond'){
			if($zonOP&&$zon<50){$msg.='zonOP, zon < 50 : '.$zon.'__';
				foreach($boven as $i){
					if(${'mode'.$i} && ${$i}<75 && ${'T'.$i}>$kwartier){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
				if($zon==0){
					foreach($beneden as $i){
						if(${'mode'.$i} && ${$i}<$open && ${'T'.$i}>$kwartier){
							sl($i,$open);$msg.=$i.' '.$open.'__';
						}
					}
				}
			}elseif($zonOP){$msg.='zonOP, zon = '.$zon.'__';
			}else{$msg.='Zononder __';
				foreach($beneden as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
				foreach($boven as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
			}
		}
	}elseif($Weg==1){$msg.='Slapen__';
		foreach($boven as $i)	{if(${'mode'.$i} && ${$i}<75){sl($i,100);$msg.=$i.' Dicht__';}}
		foreach($benedena as $i){if(${'mode'.$i} && ${$i}<75){sl($i,100);$msg.=$i.' Dicht__';}}
	}elseif($Weg==2){$msg.='Weg__';
		if($dag=='nacht'){
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
				foreach($boven as $i){
					if(${'mode'.$i} && ${$i}<75){
						sl($i,100);$msg.=$i.' Dicht__';
					}
				}
		}elseif($dag=='ochtend'){
			if($zonOP&&$zon==0){$msg.='ZonOP && Zon = 0__';
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
			}elseif($zonOP){$msg.='ZonOP && Zon = '.$zon.'__';
				foreach($benedena as $i){
					if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){
						sl($i,$open);$msg.=$i.' '.$open.'__';
					}
				}
				foreach($boven as $i){
					if(${'mode'.$i} && ${$i}!=$openb && ${'T'.$i}>$kwartier){
						sl($i,$openb);$msg.=$i.' '.$openb.'__';
					}
				}
			}
		}elseif($dag=='AM'){
				foreach($benedena as $i){if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){sl($i,$open);$msg.=$i.' '.$open.'__';}}
				foreach($boven as $i){if(${'mode'.$i} && ${$i}>$openb && ${'T'.$i}>$kwartier){sl($i,$openb);$msg.=$i.' '.$openb.'__';}}
		}elseif($dag=='PM'){
				foreach($benedena as $i){if(${'mode'.$i} && ${$i}!=$open && ${'T'.$i}>$kwartier){sl($i,$open);$msg.=$i.' '.$open.'__';}}
				foreach($boven as $i){if(${'mode'.$i} && ${$i}>$openb && ${'T'.$i}>$kwartier){sl($i,$openb);$msg.=$i.' '.$openb.'__';}}
		}elseif($dag=='avond'){
			if($zonOP&&$zon==0){$msg.='zonOP && Zon = 0__';
				foreach($benedena as $i){if(${'mode'.$i} && ${$i}<$open && ${'T'.$i}>$kwartier){sl($i,$open);$msg.=$i.' '.$open.'__';}}
			}elseif($zonOP&&$zon<50){$msg.='zonOP && Zon < 50 : '.$zon.'__';
				foreach($boven as $i){if(${'mode'.$i} && ${$i}<$openb && ${'T'.$i}>$kwartier){sl($i,100);$msg.=$i.' Dicht__';}}
			}elseif($zonOP){$msg.='zonOP, zon = '.$zon.'__';
			}else{$msg.='Zononder __';
				foreach($benedena as $i){if(${'mode'.$i} && ${$i}<75){sl($i,100);$msg.=$i.' Dicht__';}}
				foreach($boven as $i){if(${'mode'.$i} && ${$i}<75){sl($i,100);$msg.=$i.' Dicht__';}}
			}
		}
	}
}
$msg.='Buienhist='.$buienhist;
lg(str_replace('__',' | ',$msg));
//if(strlen($msg)>60)telegram(str_replace('__',PHP_EOL,$msg));
?>
