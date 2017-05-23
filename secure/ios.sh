#!/bin/bash
MSG="$1"
curl -s --data-urlencode "text=$MSG" "http://127.0.0.1/secure/ios.php"
