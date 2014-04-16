<?php
   
	/**
	* Plugin Name: KORA Database Display
	* Plugin URI: TBD
	* Description: Plugin for displaying information from a KORA database.
	* Author: MATRIX: The Center for Digital Humanities and Social Sciences (Anthony D'Onofrio and Ryan Zahm)
	* Version: 1.0
	* Author URI: TBD
	 */
	define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
	define('KORA_PLUGIN_BLOCKUI_CDN', '//cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.66.0-2013.10.09/jquery.blockUI.min.js');
	define('KORA_PLUGIN_FLEXSLIDER_PATHBASE', KORA_PLUGIN_PATHBASE.'/flexslider/');
	define('KORA_PLUGIN_FLEXSLIDER_PATHJS', KORA_PLUGIN_FLEXSLIDER_PATHBASE.'jquery.flexslider-min.js');
	define('KORA_PLUGIN_FLEXSLIDER_PATHCSS', KORA_PLUGIN_FLEXSLIDER_PATHBASE.'flexslider.css');
	define('KORA_PLUGIN_FILES_SUBPATH', 'files/');
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
	
    //***********************************************
    // DATABASE INSTALL
    //***********************************************

	global $wpdb;
    global $kora_db_version;
	$kora_db_version = "1.0";

	function kora_install() {
   		global $wpdb;
  		global $kora_db_version;

   		$table_name = $wpdb->prefix . "koralibrary";
      
        //will need changes depending on what fields are needed
   		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		KID VARCHAR(45) NOT NULL,
		url VARCHAR(10000) DEFAULT '' NOT NULL,
		title VARCHAR(999) NOT NULL
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		 
		add_option( "kora_db_version", $kora_db_version );
	}

	register_activation_hook(__FILE__, 'kora_install');

	//***********************************************
	// ADMIN MENU STUFF
	//***********************************************
	///include import file
	function kordat_admin() {  
	    include('kora_database_admin.php');  
	}

	function kordat_gallery() {
		include('kora_gallery.php');
	}

	function kordat_library() {
		include('kora_library.php');
	}  

	function kordat_new_kora_obj() {
		include('kora_newobj.php');
	}

	//Adds KORA section in the sidebar along with subsections
	function kordat_admin_menu() {
		add_menu_page("Settings" , "Kora", 1, "KORA_Settings", "kordat_admin");
	   	add_submenu_page("KORA_Settings", 
	    	"Galleries", "Galleries", 1, "Galleries", "kordat_gallery" );
	   	add_submenu_page("KORA_Settings", 
	    	"Library", "Library", 1, "Library", "kordat_library" );
	   	add_submenu_page("KORA_Settings", 
	    	"Add New KORA Object", "Add New Object", 1, "Add_New_KORA_Object", "kordat_new_kora_obj");
	}

	add_action('admin_menu', 'kordat_admin_menu');
	add_action( 'admin_footer-post-new.php', 'wpse_78881_script' );

	// REGISTER THE JQUERY BLOCKUI FROM CDN
	wp_enqueue_script(
		'blockui',
		KORA_PLUGIN_BLOCKUI_CDN,
		array('jquery') );
	
	// REGISTER THE FLEXSLIDE PLUGIN
	wp_enqueue_script(
		'flexslider',
		KORA_PLUGIN_FLEXSLIDER_PATHJS,
		array('jquery') );
	
	wp_enqueue_style(
		'flexslider',
		KORA_PLUGIN_FLEXSLIDER_PATHCSS);
	
	// REGISTER THIS PLUGIN SCRIPTS

		
/*	wp_enqueue_script(
		'shortcode',
		KORA_PLUGIN_PATHBASE.'/kora_upload.php',
		array('jquery')	);*/
	wp_register_script( 'kora_script', ''.KORA_PLUGIN_PATHBASE.'kora.js', array(), null,true);
    wp_enqueue_script( 'kora_script');
   	$plugin = array( 'url' => get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH,
   					'pid' => get_option('kordat_dbproj'),
   					'sid'=>get_option('kordat_dbscheme'),
   					'token' => get_option('kordat_dbtoken'),
					'user' => get_option('kordat_dbuser'),  
					'pass' => get_option('kordat_dbpass'),
					'theme' => get_template_directory(),
					//'theme' =>get_template(),
					'restful'=> get_option('kordat_dbapi'));
					
    wp_localize_script( 'kora_script', 'plugin', $plugin );	

	
/*	wp_enqueue_script(
		'shortcode',
		KORA_PLUGIN_PATHBASE.'/kora_upload.php',
		array('jquery')	);*/
	wp_enqueue_style(
		'kora',
		KORA_PLUGIN_PATHBASE.'/kora.css');
/*************************************************************/


/******************************/
function mediabutton(){
	
	wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
	wp_register_script( 'mediabutton', ''.KORA_PLUGIN_PATHBASE.'addkoraobject.js', array(), null,true);
    wp_enqueue_script( 'mediabutton');
   $plugin = array( 'url' => KORA_PLUGIN_PATHBASE,
    					'pid' => get_option('kordat_dbproj'),
    					'sid'=>get_option('kordat_dbscheme'),
    					 'token' => get_option('kordat_dbtoken'),
						'user' => get_option('kordat_dbuser'),  
						'pass' => get_option('kordat_dbpass'),
						'restful'=> get_option('kordat_dbapi'));
    wp_localize_script( 'mediabutton', 'plugin', $plugin );
     
}

	add_action('admin_print_scripts','mediabutton'); 

	
	//**********************************************
	// MAIN PLUGIN
	//**********************************************
	
	///Tells wordpress to register the shortcode for grid formatting
	add_shortcode("KORAGRID", "kordat_handler");
	add_shortcode("koragrid", "kordat_handler");
	/***/
	$theme = wp_get_theme();
	//var_dump($theme);
	/***/
	function kordat_handler($incomingfrompost) {
		/// PROCESS INCOMING ATTS OR SET DEFAULTS

		$incomingfrompost = shortcode_atts(array(
			"pid" => get_option('kordat_dbproj'),
			"sid" => get_option('kordat_dbscheme'),
			"fields" => '',
			"query" => "KID,!=,\'\'",
			"kg_title" => "Kora Database Grid",
			"kg_perpage" => 10,
			"kg_theme" => "dot-luv",
			"kg_height" => 600, 
			"kg_width" => 800,           
			"kg_search" => "No",
			), $incomingfrompost);

		/// THIS DOES THE ACTUAL WORK
		$wpoutput = kordat_getrecords($incomingfrompost);
		
		/// SEND TEXT BACK
		return $wpoutput;
	}

	///Get records from the database based on user requests
	function kordat_getrecords($wpatts) { 
		///gather wordpress options 
  		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;  
		//$pid = get_option('kordat_dbproj');  
		//$sid = get_option('kordat_dbscheme');  
		$token = get_option('kordat_dbtoken');
		$user = get_option('kordat_dbuser');  
		$pass = get_option('kordat_dbpass');
		
		///gather formatting options
		$pid = $wpatts['pid'];
		$sid = $wpatts['sid'];
		$gftitle = $wpatts['kg_title'];
		$gfperpage = $wpatts['kg_perpage'];
		$gftheme = $wpatts['kg_theme'];
		$gfheight = $wpatts['kg_height'];
		$gfwidth = $wpatts['kg_width'];
		// HANDLE TRUE/FALSE/YES/NO FOR THIS PROPERTY PASSING IT THEN TO KORAGRID AS EXPECTED
		$gfsearch = (get_bool_setting($wpatts['kg_search'], false)) ? 'Yes' : 'No';
		
		//advanced filter options
		$query = $wpatts['query'];
		$fieldsarg = ($wpatts['fields'] != '') ? '&fields='.urlencode($wpatts['fields']) : '';
		$display = 'grid';
		///build url
		$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&gr_title='.urlencode($gftitle).'&gr_pagesize='.urlencode($gfperpage).'&gr_theme='.urlencode($gftheme).'&gr_height='.urlencode($gfheight).'&gr_width='.urlencode($gfwidth).'&gr_search='.urlencode($gfsearch).$fieldsarg.'&query='.urlencode($query);
		
		///initialize post request to KORA API using curl
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
		
		///capture results and display
		$server_output = curl_exec($ch);
		return $server_output;
	}
	
	///Tells wordpress to register the shortcode for gallery formatting
	add_shortcode("KORAGALLERY", "koragallery_handler");
	add_shortcode("koragallery", "koragallery_handler");
	
	function koragallery_handler($incomingfrompost) {
		/// PROCESS INCOMING ATTS OR SET DEFAULTS

		$incomingfrompost = shortcode_atts(array(
			"pid" => get_option('kordat_dbproj'),
			"sid" => get_option('kordat_dbscheme'),
			"fields" => "ALL",
			"query" => "KID,!=,\'\'",
			"kg_imagecontrol" => "",
			"kg_titlecontrol" => "",
			"kg_desccontrol" => "",
			"kg_linkbase" => KORA_PLUGIN_PATHBASE."detail.php",
			"kg_type" => 'flexslider', 
			"kg_imagesize" => 'small',
			"kgfs_animation" => 'slide',
			"kgfs_direction" => 'horizontal',
			"kgfs_reverse" => false,
			"kgfs_animationloop" => true,
			"kgfs_smoothheight" => false,
			"kgfs_startat"  => 0,
			"kgfs_slideshow" => true,
			"kgfs_slidshowspeed" => 7000,
			"kgfs_animationspeed" => 600,
			"kgfs_initdelay" => 0,
			"kgfs_randomize" => false,
			"kgfs_pauseonaction" => true,
			"kgfs_pauseonhover" => false,
			"kgfs_touch" => true,
			"kgfs_video" => false,
			"kgfs_itemwidth" => 150,
			"kgfs_itemmargin" => 5,
			"kgfs_minitems" => 0,
			"kgfs_maxitems" => 0,
			"kgfs_imageclip" => false,
			"kgfs_move" => 0,
			"kgis_pagesize" => 20,
			"kgis_loadimg" => KORA_PLUGIN_PATHBASE.'/loading.gif',
			), $incomingfrompost);

		/// THIS DOES THE ACTUAL WORK
		$wpoutput = korgallery_getrecords($incomingfrompost);
		if (!is_wp_error($wpoutput)) 
		{ return $wpoutput; }
		else 
		{ return $wpoutput->get_error_message(); }
	}
	
	function korgallery_getrecords($wpatts) { 
		///gather wordpress options 
  		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;  
		//$pid = get_option('kordat_dbproj');  
		//$sid = get_option('kordat_dbscheme');  
		$token = get_option('kordat_dbtoken');
		$user = get_option('kordat_dbuser');  
		$pass = get_option('kordat_dbpass');
		$files_url = get_option('kordat_dbapi').KORA_PLUGIN_FILES_SUBPATH."/$pid/$sid/";
		$thumbs_url = "${files_url}thumbs/";
		
		$display = 'html';
		$pid = $wpatts['pid'];
		$sid = $wpatts['sid'];
		$query = $wpatts['query'];
		$kg_type = $wpatts['kg_type'];
		$kg_ictrl = $wpatts['kg_imagecontrol'];
		$kg_tctrl = $wpatts['kg_titlecontrol'];
		$kg_dctrl = $wpatts['kg_desccontrol'];
		$kg_isize = $wpatts['kg_imagesize'];
		$kg_lbase = $wpatts['kg_linkbase'];
		$kgfs_imageclip = $wpatts['kgfs_imageclip'];
		
		$fields .= $kg_ictrl;
		if ($kg_tctrl != '') { $fields .= ','.$kg_tctrl; }
		if ($kg_dctrl != '') { $fields .= ','.$kg_dctrl; }
		
		// IF WE ARE MISSING REQUIRED ATTS FOR THIS CALL, JUST BAIL NOW
		if ($kg_ictrl == '') { return new WP_Error('kg_noictl', __('No kg_imagecontrol property was passed to KORAGALLERY shortcode')); }
		
		if ($kg_type == 'flexslider')
		{
			$display = 'json';
			$kg_divtag_opts = '';
			foreach ($wpatts as $k => $v)
			{
				// EACH WPATTT THAT STARTS W/ KGFS IS SENT AS PROPERTY...
				if (preg_match('/^kgfs_/', $k))
				{
					$kg_divtag_opts .= "$k='$v' ";
				}
			}
		}
		elseif ($kg_type == 'infscroll')
		{

			$display = 'json';
			$kg_divtag_opts = '';
			foreach ($wpatts as $k => $v)
			{
				// EACH WPATTT THAT STARTS W/ KGFS IS SENT AS PROPERTY...

				if (preg_match('/^kgis_/', $k))
				{
					$kg_divtag_opts .= "$k='$v' ";
				}
			}
		}
		
		// THESE ARE ALL THE OPTIONS TO CONFIGURE THE PLUGIN ITSELF (WELL THE ONES THAT MAKE SENSE TO CONFIGURE ANYWAY)
		// ALL OF THEM ARE PREFIXED WITH KGFS FOR Kora Gallery Flex Slide IN ANTICIPATION OTHER GALLERY FORMATS W/ OPTIONS MAY COME IN LATER
		// SEE THIS PAGE FOR POSSIBLE UPDATES TO THIS DOCUMENTATION http://www.woothemes.com/flexslider/
		//animation: "fade",              //String: Select your animation type, "fade" or "slide"
		//direction: "horizontal",        //String: Select the sliding direction, "horizontal" or "vertical"
		//reverse: false,                 //{NEW} Boolean: Reverse the animation direction
		//animationLoop: true,             //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
		//smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode  
		//startAt: 0,                     //Integer: The slide that the slider should start on. Array notation (0 = first slide)
		//slideshow: true,                //Boolean: Animate slider automatically
		//slideshowSpeed: 7000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
		//animationSpeed: 600,            //Integer: Set the speed of animations, in milliseconds
		//initDelay: 0,                   //{NEW} Integer: Set an initialization delay, in milliseconds
		//randomize: false,               //Boolean: Randomize slide order
		 
		// Usability features
		//pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
		//pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
		//touch: true,                    //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
		//video: false,                   //{NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
		 
		// Carousel Options
		//itemWidth: 0,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
		//itemMargin: 0,                  //{NEW} Integer: Margin between carousel items.
		//minItems: 0,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
		//maxItems: 0,                    //{NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
		//move: 0,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
		
		// THESE ARE FLEXSLIDE OPTIONS THAT DON'T MAKE SENSE TO CONFIGURE IN OUR ENV
		//namespace: "flex-",             //{NEW} String: Prefix string attached to the class of every element generated by the plugin
		//selector: ".slides > li",       //{NEW} Selector: Must match a simple pattern. '{container} > {slide}' -- Ignore pattern at your own peril
		//easing: "swing",                //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
		//useCSS: true,                   //{NEW} Boolean: Slider will use CSS3 transitions if available
		// PRIMARY NAV
		//controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
		//directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
		//prevText: "Previous",           //String: Set the text for the "previous" directionNav item
		//nextText: "Next",               //String: Set the text for the "next" directionNav item
		// SECONDARY NAV
		//keyboard: true,                 //Boolean: Allow slider navigating via keyboard left/right keys
		//multipleKeyboard: false,        //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
		//mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
		//pausePlay: false,               //Boolean: Create pause/play dynamic element
		//pauseText: 'Pause',             //String: Set the text for the "pause" pausePlay item
		//playText: 'Play',               //String: Set the text for the "play" pausePlay item
		// OTHER
		//controlsContainer: "",          //{UPDATED} Selector: USE CLASS SELECTOR. Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be ".flexslider-container". Property is ignored if given element is not found.
		//manualControls: "",             //Selector: Declare custom control navigation. Examples would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
		//sync: "",                       //{NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
		//asNavFor: "",                   //{NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
		
		//NOTE:  SETTING itemMargin ISN'T CURRENTLY WORKING, HAD TO OVERRIDE STANDARD FLEXSLIDER CSS W/ 'IMPORTANT' TO 
		//       GET DEFAULT OF 5PX TO EVEN DISPLAY PROPERLY; I SUSPECT THIS HAS SOMETHING TO DO W/ WHY IT'S NOT WORKING
		
		
		$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
		
		switch ($kg_type)
		{
		case 'flexslider':
			///initialize post request to KORA API using curl
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
			
			///capture results and display
			$server_output = curl_exec($ch);

			if($kgfs_imageclip == 'true'){
			return "<div class='kora_gallery_flexslide' kgictrl='$kg_ictrl' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$url' kgfbase='$files_url' $kg_divtag_opts>\n</div>\n
</div>";
			break;
			}
			else{
				return "<div class='kora_gallery_flexslide' kgictrl='$kg_ictrl' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$url' kgfbase='$thumbs_url' $kg_divtag_opts>\n</div>\n";
				break;
			}
		case 'infscroll':
			// EMPTY TAG TO BE FILLED IN BY JAVASCRIPT
		if($kgfs_imageclip == 'true'){
			return "<div class='kora_gallery_infscroll' kgictrl='$kg_ictrl' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$url' kgfbase='$thumbs_url' $kg_divtag_opts>\n
</div>";
			break;
		}
		else
			return "<div class='kora_gallery_infscroll' kgictrl='$kg_ictrl' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$url' kgfbase='$thumbs_url' $kg_divtag_opts>\n";
			break;
		}
	}
	
	///Tells wordpress to register the shortcode for kora search
	add_shortcode("KORASEARCH", "korasearch_handler");
	add_shortcode("korasearch", "korasearch_handler");
	
	function korasearch_handler($incomingfrompost) {
		/// PROCESS INCOMING ATTS OR SET DEFAULTS
		$incomingfrompost = shortcode_atts(array(
			"pid" => get_option('kordat_dbproj'),
			"sid" => get_option('kordat_dbscheme'),
			"display" => "html",
			"fields" => '',
			"query" => "KID,!=,\'\'",
			"first" => 0,
			"count" => 0,
			"showempty" => "NO",
			), $incomingfrompost);
		
		/// THIS DOES THE ACTUAL WORK
		$wpoutput = korasearch_getrecords($incomingfrompost);
		
		/// SEND TEXT BACK
		return $wpoutput;
	}
	
	function korasearch_getrecords($wpatts) { 
		///gather wordpress options 
  		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;  
		//$pid = get_option('kordat_dbproj');  
		//$sid = get_option('kordat_dbscheme');  
		$token = get_option('kordat_dbtoken');
		$user = get_option('kordat_dbuser');  
		$pass = get_option('kordat_dbpass');
		
		$display = $wpatts['display'];
		$query = $wpatts['query'];
		$pid = $wpatts['pid'];
		$sid = $wpatts['sid'];
		$fieldsarg = ($wpatts['fields'] != '') ? '&fields='.urlencode($wpatts['fields']) : '';
		$first = $wpatts['first'];
		$count = $wpatts['count'];
		$showempty = $wpatts['showempty'];
		
		$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&html_showempty='.urlencode($showempty).$fieldsarg.'&query='.urlencode($query).'&first='.urlencode($first).'&count='.urlencode($count);
		
		///initialize post request to KORA API using curl
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
		
		///capture results and display
		$server_output = curl_exec($ch);
		return "$server_output\n";
	}
	
	///Tells wordpress to register the shortcode for KORAVALUE
	add_shortcode("KORAVALUE", "koravalue_handler");
	add_shortcode("koravalue", "koravalue_handler");
	
	function koravalue_handler($incomingfrompost) {
		/// PROCESS INCOMING ATTS OR SET DEFAULTS
		$incomingfrompost = shortcode_atts(array(
			"pid" => get_option('kordat_dbproj'),
			"sid" => get_option('kordat_dbscheme'),
			"kid" => '',
			"field" => '',
			"kv_listdelimiter" => ',',
			"kv_aslist" => 'NO',
			"kv_asimgtag" => 'NO',
			"kv_ashreftag" => 'NO',
			"kv_urlonly" => 'NO',
			"kv_thumbnail" => 'NO',
			"kv_hreftext" => '',
			"kv_nostyle" => 'NO',
			"kv_asspan" => 'NO',
			), $incomingfrompost);
		
		/// SEND TEXT BACK
		$wpoutput = koravalue_getdata($incomingfrompost);
		if (!is_wp_error($wpoutput)) 
		{ return $wpoutput; }
		else 
		{ return $wpoutput->get_error_message(); }
	}
	
	function koravalue_getdata($wpatts) { 
		///gather wordpress options 
  		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;  
		//$pid = get_option('kordat_dbproj');  
		//$sid = get_option('kordat_dbscheme');  
		$token = get_option('kordat_dbtoken');
		$user = get_option('kordat_dbuser');  
		$pass = get_option('kordat_dbpass');
		
		$kid = $wpatts['kid'];
		$pid = $wpatts['pid'];
		$sid = $wpatts['sid'];
		$cname = $wpatts['field'];
		
		$display = 'xml';

		// IF WE ARE MISSING REQUIRED ATTS FOR THIS CALL, JUST BAIL NOW
		if ($kid == '') { return new WP_Error('kg_nokid', __('No KID property was passed to KORAVALUE shortcode, this is required')); }
		if ($cname == '') { return new WP_Error('kg_nocontrol', __('No FIELD property was passed to KORAVALUE shortcode, this is required')); }

		$url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display='.urlencode($display).'&fields='.urlencode($cname).'&query=kid,eq,'.urlencode($kid);
		
		//initialize post request to KORA API using curl
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
		
		///capture results and display
		$server_output = curl_exec($ch);
		$xml = simplexml_load_string($server_output);

		// AT THIS POINT, SUB IN THE '_' FOR SPACES SO IT MATCHES THE XML RETURN FROM RESTFUL
		$cname = preg_replace('/ /','_',$cname);
		
		// FIGURE OUT WHAT TAGS WE ARE GOING TO WRAP AROUND OUR VALUE OUTPUT
		$opentag = '';
		$closetag = '';
		$nostyle = get_bool_setting($wpatts['kv_nostyle'], false);
		$asspan = get_bool_setting($wpatts['kv_asspan'], false);
		if ($nostyle)
		{ $opentag = ''; $closetag = ''; }
		elseif ($asspan)
		{ $opentag = '<span class="kora_value">'; $closetag = '</span>'; }
		else
		{ $opentag = '<div class="kora_value">'; $closetag = '</div>'; }

		$str_value = '';
		// BLEH, HAVE TO DEAL WITH DIFFERENT KINDS OF CONTROL STRUCTURES HERE
		// .. RECORD+PROP NOT FOUND, NOTHING TO RETURN.. ERROR, I SAY NO, JUST RETURN EMPTY?
		if (!isset($xml->{'kid'.$kid}))
		{ $str_value = ''; } 
		// VALUE IS ARRAY LISTED AS ITEM0 TO ITEMX
		elseif (isset($xml->{'kid'.$kid}->{$cname}->item0))
		{
			// THIS SECTION HANDLES A COUPLE DIFFERENT WAYS OF DISPLAYING AN ARRAY/LIST
			$aslist = get_bool_setting($wpatts['kv_aslist'], false);
			if ($aslist) { $str_value .= $opentag.'<ul><li>'; }
			$str_value .= $xml->{'kid'.$kid}->{$cname}->item0; 
			if ($aslist) { $str_value .= '</li>'; }
			$delimiter = $wpatts['kv_listdelimiter'];
			$i = 1;
			while (isset($xml->{'kid'.$kid}->{$cname}->{'item'.$i}))
			{
				if ($aslist) { $str_value .= '<li>'; }
				else         { $str_value .= $delimiter; }
				$str_value .= (string)$xml->{'kid'.$kid}->{$cname}->{'item'.$i}; 
				if ($aslist) { $str_value .= '</li>'; }
				$i++;
			}
			if ($aslist) { $str_value .= '</ul>'.$closetag; }			
		}
		// VALUE IS A FILE OBJECT
		elseif (isset($xml->{'kid'.$kid}->{$cname}->originalName))
		{
			// AGAIN, A FEW DIFFERENT CUSTOM OPTIONS FOR FILE OUTPUT
			$imgtag = get_bool_setting($wpatts['kv_asimgtag'], false);
			$hreftag = get_bool_setting($wpatts['kv_ashreftag'], false);
			$urlonly = get_bool_setting($wpatts['kv_urlonly'], false);
			$thumb = get_bool_setting($wpatts['kv_thumbnail'], false);
			$hreftxt = $wpatts['kv_hreftext'] != '' ? $wpatts['kv_hreftext'] : (string)$xml->{'kid'.$kid}->{$cname}->originalName;
			$urlbase = get_option('kordat_dbapi').KORA_PLUGIN_FILES_SUBPATH."$pid/$sid/";
			if ($thumb) { $urlbase .= 'thumbs/'; }
			if ($imgtag)
			{ $str_value .= $opentag."<img src='".$urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName."' />".$closetag; }
			elseif ($hreftag)
			{ $str_value .= $opentag."<a href='".$urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName."'>".$hreftxt."</a>".$closetag; }
			elseif ($urlonly)
			{ $str_value .= $urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName; }
			else
			{
				$str_value .= $opentag;
				$str_value .= "Name: ".(string)$xml->{'kid'.$kid}->{$cname}->originalName;
				$str_value .= "&nbsp;Size: ".(string)$xml->{'kid'.$kid}->{$cname}->size;
				$str_value .= "&nbsp;Type: ".(string)$xml->{'kid'.$kid}->{$cname}->type;
				$str_value .= $closetag;
			}
		}
		// SO.. THIS DOESN'T WORK, HENCE THE ELSE BELOW
		//elseif (is_string($xml->{'kid'.$kid}->{$cname}))
		//{ $str_value = $xml->{'kid'.$kid}->{$cname}; }
		// ELSE WE HAVE TO ASSUME THE {$cname} LEVEL IS A STRING BECAUSE YOU CAN'T CALL
		// is_string ON $xml->{'kid'.$kid}->{$cname}, READ ABOUT SimpleXML TO FIND OUT
		// WHY IT WILL ALWAYS RETURN IT is_object, SO WE DO ALL OTHER CHECKS, DEFAULTING TO THIS...
		else
		{
			$str_value = $opentag.(string)$xml->{'kid'.$kid}->{$cname}.$closetag;
		}

		return $str_value;
	}
	
	function get_bool_setting($opt_, $def_)
	{
		// IF DEFAULT IS TRUE, CHECK FOR FALSE SETTING
		if ($def_)
		{ return (preg_match('/^no|false$/i', $opt_)) ? false : true; }
		// ELSE CHECK FOR TRUE SETTING
		else
		{ return (preg_match('/^yes|true$/i', $opt_)) ? true : false; }
		
	}
?>