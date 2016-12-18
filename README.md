# LUA-Pass2PHP-for-Domoticz
Advanced event system for Domoticz

# Information
Take a look at https://egregius.be/tag/domoticz/ for more examples and explanation

# Installation
- Download the repository and unpack it
- Place the files of the scripts folder in your Domoticz scripts folder
- If you're planning on using pass2php with curl in a PHP enabled webserver place the complete secure folder in your html documents root (ex /var/www).
- Update secure/pass2php with your settings.

# Update
- With this new repository I guess that only pass2php.php will receive important updates. Other files are quit static or my scripts shared as examples.

# How it works - Usage
Because script_device_pass2php.lua is a basic Domoticz event script it get's triggered upon every device update. Result is that for each device update the pass2php.php script is called. The LUA script encodes the Domoticz lua tables devicechanged, otherdevices, otherdevices_idx and otherdevices_lastupdate to a base64 encoded json table. That string is passed to pass2php.php where it gets decoded in arrays:<br>
devicechanged = $c[]<br>
otherdevices = $s[]<br>
otherdevices_idx = $i[]<br>
otherdevices_lastupdate = $t[]<br>
After decoding everything there's a check to see if there's a file in the pass2php folder that has exactly the same name as the changed device. So if you want to do something with a device called 'PIRhall' you need to create a file pass2php/PIRhall.php. Keep in mind that everything is case sensitive.<br>
Having everything in arrays means that all device states are available like $s['PIRHall']. The IDX of a device is available in $i['kitchenlight'] and the lastupdate time in $t['PIRhall']. The lastupdate time is provided as a formatted string. We need to convert to unix timestamp in order to do calculations with it: strtotime($t['PIRhall'])<br>
As there is a constant 'time' defined now it's easy to do calculations:<br>
if($s['PIRhall']=='Off'&&strtotime($t['PIRhall']<time-120))sw($i['lighthall'],'Off');<br>



# Functions
## sw($idx,$action='',$info='')
## sl($idx,$level,$info='')
## ud($idx,$nvalue,$svalue,$info="")
## setradiator($name,$dif,$koudst=false,$set)
## double($idx,$action,$comment='',$wait=1500000)
## telegram($msg,$silent=true,$to=1)
## lg($msg)
## ios($msg)
## sms($msg)
## checkport($ip,$port)
## pingport($ip,$port)
## RefreshZwave($node)
## Zwavecancelaction()
## ZwaveCommand($node,$command)
## ControllerBusy($retries)
## convertToHours($time)
## curl($url)
## cset($key,$value)
## cget($key)

