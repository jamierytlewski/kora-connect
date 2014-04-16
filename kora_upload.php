<link rel="stylesheet" type="text/css" href="kora.css">

<?php
	
	global $wpdb;
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
	
	?>
	<div class="form_upload">
	<form action="" method="post">
	
	<label> Title Control Name: </label>
	<input type="text" id="t_control" name="title" required/>
	<br>
	<label> Image Control Name: </label>
	<input type="text" id="img_control" name="img_c" required/>
	<br>
	<label> Description Control Name: </label>
	<input type="text" id="desc_control" name="description"/>
	<br>
	<label> Choose an option: </label>
	<input type="radio" class=radio name="type" value="infscroll" checked>Infinite Scroll &nbsp;
	<input type="radio" class=radio name="type" value="flexslider">Flexslider
	<br>
	
Search for KORA object: <input type="text" name="kid" />
	<button type="submit" name="k_search">Search</button>
	</form>
	</div>
	<?php
	$token = $_GET['token'];
	$pid = $_GET['pid'];
	$sid = $_GET['sid'];
	$user = $_GET['user']; 
	$pass = $_GET['pass'];
	$restful=$_GET['restful'];
	$url_plugin=$_GET['url'];?>
	
	<?php	
	if(isset($_POST['kid'])){
		$title_form=$_POST['title'];
		$k = $_POST['kid'];
		$image_control=$_POST['img_c'];
		$type=$_POST['type'];
		$desc= $_POST['description'];
	
		
		if ($k!=''){
			$query = $title_form.",LIKE,".$k;
			$restful_url =$restful . KORA_PLUGIN_RESTFUL_SUBPATH;
			
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
			echo $server_output;

			//gets url of image
			if($server_output == '')
				echo "Search Returned No Results, Try Again";
				
			else{
				$xpath = new DOMXPath(@DOMDocument::loadHTML($server_output));
				$thumb_src = $xpath->evaluate("string(//img/@src)");
				$src = str_replace("thumbs/", "", $thumb_src);
				
				//Get KID from HTML
				$kid = $xpath->evaluate("string(/html/body/div/div/div[2])");
				$title = $xpath->evaluate("string(/html/body/div/div[3]/div[2])");
				
			}
		}
	}
	
?>

<br/><button id="shortcode">Insert Shortcode</button>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
 
var url_plugin = "<?php echo $url_plugin;?>";

jQuery(document).ready(function() {
	//when insert button is clicked
	var chk; 
	var query='';
	var title='<?php echo $title_form;?>';
	var img_c='<?php echo $image_control;?>';
	var desc='<?php echo $desc;?>';
	var type='<?php echo $type;?>';
	$('#shortcode').click(function() {
		
		var i=0;
		c_true=0;
		c_false=0;
		var checks = $('input[name="checked[]"]:checked');
		$(checks).each(function(){
			//TODO: What to do with the checked objects
			chk = $(this).attr('id');
			
			$.ajax({
				type: "GET",
				async: false,
				url: url_plugin+"/ajax/insert_library.php",
				data: {"chk" : chk },
				success: function(data){	
					if(data=="true"){
						c_true++;
					}else if(data=="false"){
						c_false++;
					}
				}
			});
			
			if(i==0 && checks.length==1){
				query="kid,=,"+chk;
			}else if (checks.length>1 && i==0){
				query="(kid,=,"+chk+")";
				i++;
			}else if (checks.length >1 && i< checks.length){
				query+= ",or,(kid,=,"+chk+")";
				i++;
			}
		});
		
		var shortcode="[KORAGALLERY KG_TYPE= '"+type+"' KG_IMAGECONTROL= '"+img_c+"' KG_TITLECONTROL= '"+title+"' KG_DESCCONTROL= '"+desc+"' KGIS_PAGESIZE='20' QUERY= '"+query+"']  [/KORAGALLERY]";
		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor(shortcode);
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

