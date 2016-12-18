# LUA-Pass2PHP-for-Domoticz
Advanced event system for Domoticz

# Information
Take a look at https://egregius.be/tag/domoticz/ for more examples and explanation

# Installation
- Download the repository and unpack it
- Place the files of the scripts folder in your Domoticz scripts folder
- If you're planning on using pass2php with curl in a PHP enabled webserver place the complete secure folder in your html documents root (ex /var/www).

# Update
- With this new repository I guess that only pass2php.php will receive important updates. Other files are quit static or my scripts shared as examples.

# How it works - Usage
Because script_device_pass2php.lua is a basic Domoticz event script it get's triggered upon every device update. Result is that for each device update the pass2php.php script is called. The LUA script encodes the Domoticz lua tables devicechanged, otherdevices, otherdevices_idx and otherdevices_lastupdate to a base64 encoded json table. That string is passed to pass2php.php where it gets decoded in arrays:
devicechanged = $c[]
otherdevices = $s[]
otherdevices_idx = $i[]
otherdevices_lastupdate = $t[]


# Functions
