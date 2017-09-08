#!/bin/bash
#exit
DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
	sleep 3.29
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	sleep 4.99
	curl -s "http://127.0.0.1/secure/pass2php.php" > /dev/null 2>/dev/null &
	exit
else
	sleep 10
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
	STATUS2=`echo $DOMOTICZ | jq -r '.status'`
	if [ "$STATUS2" == "OK" ] ; then
		exit
	else
		sleep 10
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
		STATUS3=`echo $DOMOTICZ | jq -r '.status'`
		if [ "$STATUS3" == "OK" ] ; then
			exit
		else
			sleep 10
			DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
			STATUS4=`echo $DOMOTICZ | jq -r '.status'`
			if [ "$STATUS4" == "OK" ] ; then
				exit
			else
				curl -s --connect-timeout 2 --max-time 5 --data-urlencode "text=Domoticz Bad - Restarting" --data "silent=false" http://127.0.0.1/secure/telegram.php
				NOW=$(date +"%Y-%m-%d_%H%M%S")
				cp /var/log/domoticz.txt /home/pi/domlogs/domoticz-$NOW.txt
				sleep 1
				sudo reboot
				exit
			fi
		fi
	fi
fi
exit
