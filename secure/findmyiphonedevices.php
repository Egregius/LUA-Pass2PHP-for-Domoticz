<?PHP
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
require_once "settings.php";
require_once "functions.php";
require_once "findmyiphone.php";
$appledevice='AcxBCrcbnf39ozym/jmrnD3Et3FtbG8mH5iaqI+2fWLrppKjayZWsOHYVNSUzmWV';
$appleid='you@me.com';
$applepass='yourpassword';
try {
	$fmi = new FindMyiPhone($appleid, $applepass);
} catch (Exception $e) {
	print "Error: ".$e->getMessage();
	exit;
}
$fmi->printDevices();
