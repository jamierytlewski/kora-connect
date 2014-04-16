<?php	
	$themeDir = $_POST['theme'];	
	$token = $_POST['token'];
	$pid = $_POST["pid"];
	$sid = $_POST['sid'];
	$user = $_POST['user'];  
	$pass = $_POST['pass'];
	$restful_url = $_POST['url'];
	$display = 'html';
	$k = $_GET["kid"];
	$query = "KID,=,".$k;
	$fields = 'ALL';
	$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
			
	///initialize post request to KORA API using curl
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
	$server_output = curl_exec($ch);
	echo $server_output;
	
	?>