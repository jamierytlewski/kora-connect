<?php
echo '<h2>Add New KORA Object</h2>';
	//define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
	global $wpdb;
	//wp_enqueue_script('media.php');?>
	<form action="" method="post">
	Search for KORA object: <input type="text" name="kid" />
	<button type="submit" name="k_search">Search</button>
	</form>
	<?php
	$token = get_option('kordat_dbtoken');
	$pid = get_option('kordat_dbproj');
	$sid = get_option('kordat_dbscheme');
	$user = get_option('kordat_dbuser');  
	$pass = get_option('kordat_dbpass');

	$k = $_POST['kid'];
	if ($k!=''){
		$query = "Title,LIKE,".$k;
		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
		$fields = 'ALL';
		$display='plugin';
		/*This is the code for the KID search
		$qclause = $k;
		$query = "KID,=,".$qclause;
		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;  
			
		//advanced filter options
		$fields = 'ALL';
		$display='tn';
		*/	
		///build url
		$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
		
		///initialize post request to KORA API using curl
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

		///capture results and display
		$server_output = curl_exec($ch);
		echo $server_output;
		
		//gets url of image
		if($server_output == '')
			echo "<div class='noresults'>Search returned no results, try again</div>";

		else{
			$xpath = new DOMXPath(@DOMDocument::loadHTML($server_output));
			$thumb_src = $xpath->evaluate("string(//img/@src)");
			$src = str_replace("thumbs/", "", $thumb_src);
			
			//Get KID from HTML
			$kid = $xpath->evaluate("string(/html/body/div/div/div[2])");
			$title = $xpath->evaluate("string(/html/body/div/div[3]/div[2])");
			/*********************THIS CALL NEEDS TO BE IN ANOTHER PHP FILE INSIDE OF AN AJAX FOLDER*************/
			//insert into database
			/*if(isset($_GET['chk'])){
				$kid = $_GET['chk'];
				echo $kid;
				$wpdb->insert(
							'wp_koralibrary',
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
			}*/
			/**********************************************************/
		}
	}
?>
<button id="newobj">Insert New Object</button>
<script>
var kid = "<?php echo $kid;?>";
var pathbase = "<?php echo KORA_PLUGIN_PATHBASE;?>";
jQuery(document).ready(function($){
	//when insert button is clicked
	$('#newobj').click(function() {
		var checks = $('input[name="checked[]"]:checked');
		var c_true=0;
		var c_false=0;
		//case that you are not checking any checkbox
		
		if(checks.length == 0){
			window.alert("No Objects Selected!");
		}
		
		$(checks).each(function(){
			//TODO: What to do with the checked objects
			var chk = $(this).attr('id');
			
			$.ajax({
				type: "GET",
				url: pathbase+"/ajax/insert_library.php",
				data: {"chk" : chk },
				async: false,
				success: function(data){
					if(data=="true"){
						c_true++;
					}else if(data=="false"){
						c_false++;
					}
				}
			});
		});
		if (c_true>0 && c_false>0){
			alert("Some objects were in the library, others were inserted");
		}else if(c_true>0 && c_false==0){
			alert("Objects were inserted successfully");
		}else if(c_true==0 && c_false>0){
			alert("Objects are already in the library");
		}
	});
});
</script>