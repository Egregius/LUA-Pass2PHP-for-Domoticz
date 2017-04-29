c = ''
for k,v in pairs(devicechanged) do c = c..k.."|"..v.."#" end
--os.execute('/volume1/web/secure/pass2php.php "'..c..'" &')
os.execute('curl -s -d "c='..c..'" http://127.0.0.1/secure/pass2php.php &')
return {}
