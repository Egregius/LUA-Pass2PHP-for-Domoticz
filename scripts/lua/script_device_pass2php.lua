c = ''
s = ''
i = ''
t = ''
for k,v in pairs(devicechanged) do c = c..k.."|"..v.."#" end
for k,v in pairs(otherdevices) do s = s..k.."|"..v.."#" end
for k,v in pairs(otherdevices_idx) do i = i..k.."|"..v.."#" end
for k,v in pairs(otherdevices_lastupdate) do t = t..k.."|"..v.."#" end
--os.execute('/volume1/web/secure/pass2php.php "'..c..'" "'..s..'" "'..i..'" "'..t..'" &')
os.execute('curl -s --data "c='..c..'&s='..s..'&i='..i..'&t='..t..'" http://127.0.0.1/secure/pass2php.php &')
commandArray={}
return commandArray
