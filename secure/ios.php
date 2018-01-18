<?PHP
if(isset($_REQUEST['text'])){
	$appledevice='AcxBGrcbnf39ozym/jmrnC3Pt3DtbF8mE5iaqF+2fGHrppIjayKLsOHYVNSUzmWV';
	$appleid='you@me.com';
	$applepass='yourpassword';
	require_once('findmyiphone.php');
	$fmi=new FindMyiPhone($appleid,$applepass);
	echo $fmi->playSound($appledevice,$_REQUEST['text']);
}
