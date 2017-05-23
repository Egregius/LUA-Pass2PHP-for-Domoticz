<?PHP
if(isset($_REQUEST['text'])){
	$appledevice='AcxBGrcbnf39ozym/jmrnC3Pt3DtbF8mE5iaqF+2fGHrppIjayKLsOHYVNSUzmWV';
	$appleid='you@me.com';
	$applepass='yourpassword';
	require_once('findmyiphone.php');
	$fmi=new FindMyiPhone($appleid,$applepass);
	echo $fmi->playSound($appledevice,$_REQUEST['text']);
}
if(apcu_fetch('tpoort')<$_SERVER['REQUEST_TIME']-86400){
	for($x=1;$x<=20;$x++){
		$data = array("text" => $x.':'.$_REQUEST['text'], "to" => array("32479878695"));
		$data_string = json_encode($data);
		$ch = curl_init('https://api.clickatell.com/rest/message');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string),
			'X-Version: 1',
			'Accept: application/json',
			'Authorization: Bearer AmdgB1oC1DocEF8qGrb4tHylhIzJ81K17yLMNoOrPQjbRSTHnUqqVuWXqfJhYZ5n99vyG1ZqW8')
		);
		$result = json_decode(curl_exec($ch),true);
		if($result['data']['message'][0]['accepted']===true)break;
	}
}
