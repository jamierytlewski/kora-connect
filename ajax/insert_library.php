<?php
	require_once("../../../../wp-blog-header.php");
	global $wpdb;

	if(isset($_GET['chk'])){
		//first we check that object is not already at the library

		$library= $wpdb->prefix . 'koralibrary';

		$kid = $_GET['chk'];
		$query="SELECT * FROM  $library where KID=%d";
		//check first that the object is not in the library.
		if(empty($wpdb->get_results($wpdb->prepare($query), $kid))){

			$token = get_option('kordat_dbtoken');
			$pid = get_option('kordat_dbproj');
			$sid = get_option('kordat_dbscheme');
			$user = get_option('kordat_dbuser');
			$pass = get_option('kordat_dbpass');
			$query = "KID,=,".$kid;
			$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
			$fields = 'ALL';
			$display='plugin';

			///build url
			$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);

			///initialize post request to KORA API using curl
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

			///capture results and display
			$server_output = curl_exec($ch);


			$xpath = new DOMXPath(@DOMDocument::loadHTML($server_output));
			$thumb_src = $xpath->evaluate("string(//img/@src)");
			$src = str_replace("thumbs/", "", $thumb_src);
			$title = $xpath->evaluate("string(/html/body/div/div[3]/div[2])");

			$wpdb->insert(
				$wpdb->prefix . "koralibrary",
				array(
					'KID' => $kid,
					'url' => "$src",
					'title' => $title
				),
				array(
					'%s',
					'%s',
					'%s'
				)
			);
			echo json_encode (true);
		}else{
			echo json_encode(false);

		}
	}
?>
