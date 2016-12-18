JSON=loadfile('/volume1/@appstore/domoticz/var/scripts/JSON.lua')()
base64=loadfile('/volume1/@appstore/domoticz/var/scripts/base64.lua')()
c=base64.encode(JSON:encode(devicechanged))
s=base64.encode(JSON:encode(otherdevices))
i=base64.encode(JSON:encode(otherdevices_idx))
t=base64.encode(JSON:encode(otherdevices_lastupdate))
--os.execute('/volume1/web/secure/pass2php.php "'..c..'" "'..s..'" "'..i..'" "'..t..'" &')
os.execute('curl -s --data "c='..c..'&s='..s..'&i='..i..'&t='..t..'" http://127.0.0.1/secure/pass2php.php &')
commandArray={}
return commandArray