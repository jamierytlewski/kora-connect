<!-- hide script from old browsers

jQuery(document).ready(function($)
{	
	// HAVE TO LOOP HERE TO HANDLE POTENTIAL MULTIPLE GALLERIES
	$('.kora_gallery_flexslide').each(function () {		
		var kg_imagectl = $(this).attr('kgictrl');
		var kg_titlectl = $(this).attr('kgtctrl');
		var kg_descctl  = $(this).attr('kgdctrl');
		var kg_linkbase = $(this).attr('kglbase');
		var kg_filebase = $(this).attr('kgfbase');
		var kg_imagesize = $(this).attr('kgisize');
		var kg_loadimg = ''; // THIS IS ONLY VALID FOR KGIS
		var kg_fspropsobj = $(this);
		var kg_imageclip = $(this).attr('kgfs_imageclip');
		var tarresturl = kg_fspropsobj.attr('kgresturl');
		var kg_imageclip = $(this).attr('kgfs_imageclip');
		var kg_baseresturl = tarresturl.match(/.*&display=/); kg_baseresturl = kg_baseresturl[0];

		$.ajaxSetup({ async: false });
		kg_fspropsobj.append("<div class='flexslider'><ul class='slides' /></div>");
		$.getJSON(
		    tarresturl,
		    function(data) {
			$.each( data, function( key, val ) {
			    var htmlobj = KoraGalleryObjJSONToHtml(val, kg_fspropsobj, kg_imagectl, kg_titlectl, kg_descctl, kg_linkbase, kg_filebase, kg_imagesize, kg_loadimg, kg_baseresturl, kg_imageclip);			    kg_fspropsobj.children('div.flexslider:first').children('ul.slides:first').append("<li>"+htmlobj+"</li>");
			});
		    }
		).fail(function() {
		    console.log( "error" );
		})
		$.ajaxSetup({ async: true });
	
		// CALL THE FLEXSLIDER FUNCTION WITH OPTIONAL ARGUMENTS
		kg_fspropsobj.children('.flexslider').flexslider({
			animation: "kg_fspropsobj.attr('kgfs_animation')",             //String: Select your animation type, "fade" or "slide"
			direction: "kg_fspropsobj.attr('kgfs_direction')",             //String: Select the sliding direction, "horizontal" or "vertical"
			reverse:  GetTagAttBool(kg_fspropsobj.attr('kgfs_reverse')), //Boolean: Reverse the animation direction
			animationLoop:  GetTagAttBool(kg_fspropsobj.attr('kgfs_animationloop')),    //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
			smoothHeight:  GetTagAttBool(kg_fspropsobj.attr('kgfs_smoothheight')),      //Boolean: Allow height of the slider to animate smoothly in horizontal mode  
			startAt:  parseInt(kg_fspropsobj.attr('kgfs_startat')),                //Integer: The slide that the slider should start on. Array notation (0 = first slide)
			slideshow:  GetTagAttBool(kg_fspropsobj.attr('kgfs_slideshow')),            //Boolean: Animate slider automatically
			slideshowSpeed:  parseInt(kg_fspropsobj.attr('kgfs_slidshowspeed')),   //Integer: Set the speed of the slideshow cycling, in milliseconds
			animationSpeed:  parseInt(kg_fspropsobj.attr('kgfs_animationspeed')),  //Integer: Set the speed of animations, in milliseconds
			initDelay:  GetTagAttBool(kg_fspropsobj.attr('kgfs_initdelay')),            //Integer: Set an initialization delay, in milliseconds
			randomize:  GetTagAttBool(kg_fspropsobj.attr('kgfs_randomize')),            //Boolean: Randomize slide order
			 
			// Usability features
			pauseOnAction:  GetTagAttBool(kg_fspropsobj.attr('kgfs_pauseonaction')),    //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
			pauseOnHover:  GetTagAttBool(kg_fspropsobj.attr('kgfs_pauseonhover')),      //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
			touch:  GetTagAttBool(kg_fspropsobj.attr('kgfs_touch')),     //Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
			video:  GetTagAttBool(kg_fspropsobj.attr('kgfs_video')),     //Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
			 
			// Carousel Options
			itemWidth:  parseInt(kg_fspropsobj.attr('kgfs_itemwidth')),           //Integer: Box-model width of individual carousel items, including horizontal borders and padding.
			itemMargin:  parseInt(kg_fspropsobj.attr('kgfs_itemmargin')),         //Integer: Margin between carousel items.
			minItems:  parseInt(kg_fspropsobj.attr('kgfs_minitems')),             //Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
			maxItems:  parseInt(kg_fspropsobj.attr('kgfs_maxitems')),             //Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
			move:  parseInt(kg_fspropsobj.attr('kgfs_move'))                      //Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
		});

			$('.kgfs_object').css('width','75%');
	  
		// THIS ALMOST WORKS W/ NEW OPTION TO DEFINE 'SELECTOR' W/OUT NEED TO RESTRUCTURE CODE AS ABOVE
		// BUT IN INITIAL TESTING SHOWS NAV BUTTONS ALL OUT OF PLACE
		//$('.kora_gallery_flexslide').flexslider({
			//selector: ".slides > div",
			//animation: "slide",
			//animationLoop: false,
			//itemWidth: 150,
			//itemMargin: 5
		//});
	});
	
	// NOT REALLY SURE HOW MULTIPLE INF SCROLL GALLERIES ON ONE PAGE WOULD WORK... BUT I'M TRYING TO CODE THUSLY
	var kgif_lastgal = 0;
	var kgif_currgal = 0;
	var kgif_currpage = new Array();
	var kgif_isloading = false;
	$('.kora_gallery_infscroll').each(function () {
		kgif_currpage[kgif_lastgal] = 0;
		$(this).attr('id','kora_gallery_infscroll_'+kgif_lastgal);
		// ADD THESE TAGS ONLY ONCE
		if (kgif_lastgal == 0)
		{
			$('body').prepend("<div id='nomore'>No more content</div>");
			$('#nomore').hide();
		}		

		// LOAD THE 1ST PAGE OF EACH GALLERY HERE, NO NEED TO WAIT FOR TRIGGER FOR FIRST PAGE
		if (!kgif_isloading) { LoadGalleryPage(kgif_lastgal, 0); }

		kgif_lastgal++;
	});	
	
	$(window).scroll(function () {
		if (kgif_lastgal > 0)
		{
			// IF CURR GALL FLAGGED -1 (END OF GALLERY) ON LAST TRIGGER, MOVE TO NEXT GALLERY
			if (kgif_currpage[kgif_currgal] == -1) 
			{ kgif_currgal++; }
			
				
			var targal = $("#kora_gallery_infscroll_"+kgif_currgal);
			var tarresturl = targal.attr('kgresturl');

			if($(window).scrollTop() + $(window).height() > $(document).height() - 200) {
				// IF CURR GALL NOW == LAST GAL, WE ARE AT END OF RESULTS, RETURN FALSE AND SHOW DIV
				if (kgif_currgal == kgif_lastgal) 
				{ ShowNoMore(); return false; } 
				else if (!kgif_isloading) 
				{ LoadGalleryPage(kgif_currgal, kgif_currpage[kgif_currgal]); }
			}
		}
	});
	
	// THIS GETS TAG ATTS THAT ARE SUPPOSED TO BE BOOLEAN COMING IN AS 1 OR 0, OR JUST PROPERY W/ NO VALUE AND RETURNS TRUE/FALSE
	function GetTagAttBool(val_)
	{
		if (val_ == '1') { return true; }
		else             { return false; }
	}
	
	function LoadGalleryPage(id_, os_)
	{
		kgif_isloading = true;
		
		var targal = $("#kora_gallery_infscroll_"+id_);
		var tarresturl = targal.attr('kgresturl');
		var tarpgsz = targal.attr('kgis_pagesize');
		var kg_loadimg = targal.attr('kgis_loadimg');
		
		var kg_imagectl = targal.attr('kgictrl');
		var kg_titlectl = targal.attr('kgtctrl');
		var kg_descctl  = targal.attr('kgdctrl');
		var kg_linkbase = targal.attr('kglbase');
		var kg_filebase = targal.attr('kgfbase');
		var kg_imagesize = targal.attr('kgisize');
		var kg_imageclip = targal.attr('kgfs_imageclip');

		var kg_baseresturl = tarresturl.match(/.*&display=/); kg_baseresturl = kg_baseresturl[0];
		
		var retval = true;
		$.getJSON(
		    tarresturl+'&first='+(kgif_currpage[id_]*tarpgsz)+'&count='+tarpgsz,
		    function(data) {
		    	if (data.length == 0) { kgif_currpage[kgif_currgal] = -1; kgif_isloading = false; return; }
			$.each( data, function( key, val ) {
				var htmlobj = KoraGalleryObjJSONToHtml(val, $("#kora_gallery_infscroll_"+id_), kg_imagectl, kg_titlectl, kg_descctl, kg_linkbase, kg_filebase, kg_imagesize, kg_loadimg, kg_baseresturl, kg_imageclip);
				$("#kora_gallery_infscroll_"+id_).append(htmlobj);
			});
			kgif_isloading = false;
			kgif_currpage[kgif_currgal]++;
		    }
		).fail(function() {
		    console.log( "error" );
		})
		
		return retval;
	}
	function changeObjWidth() {
		var obj = document.getElementsByClassName('kgfs_object');
		for(i=0; i<obj.length; i++) {
			obj[i].style.width = '%75';
		}
	}
	function KoraGalleryObjJSONToHtml(obj_, kgifobj_, ictrl_, tctrl_, dctrl_, lbase_, fbase_, isize_, kg_loadimg_, restbaseurl_, imageclip_)
	{
		var retval = '';
		retval += "<div class='kgfs_object' kid='"+obj_.kid+"' >";
		// IF WE CAN FIND AN IMAGE
		if ((typeof obj_[ictrl_].localName !== 'undefined') && (obj_[ictrl_].localName != ''))
		{ 
			var imgsrc = "";
			if (isize_ == 'full ') { imgsrc = "<img src='" + fbase_ + obj_[ictrl_].localName + "' />"; }
			else if (isize_ == 'large') 
			{
				// THIS URL WILL START WITH PID/SID/TOKEN AND display= SO WE START APPENDING THERE
				imgresturl = restbaseurl_+'tn&fields='+ictrl_+'&query='+escape('KID,=,'+obj_.kid)+'&tn_large=yes'+'&tn_imageclip='+imageclip_;
				//kgifobj_.find('.kgfs_object[kid='+obj_.kid+']').block({ message: '<strong>Loading</strong>' });
				// THIS IS FOR KGIS, KGFS WILL OVERWRITE THIS BELOW DUE TO SYNC
				imgsrc = "<img class='kgis_loading' src='"+kg_loadimg_+"' />";
				$.get(imgresturl, function(data) {
						// this part works for flexslider since it's async blocked
						imgsrc = data;
						// this part works for kgis in async unblocked
						kgifobj_.find('.kgfs_object[kid='+obj_.kid+'] > .kgfs_img > a').html(data);
						//kgifobj_.find('.kgfs_object[kid='+obj_.kid+']').unblock();
				});
			}
			else 
			{
				// THIS URL WILL START WITH PID/SID/TOKEN AND display= SO WE START APPENDING THERE
				imgresturl = restbaseurl_+'tn&fields='+ictrl_+'&query='+escape('KID,=,'+obj_.kid)+'&tn_large=no'+'&tn_imageclip='+imageclip_;
				//kgifobj_.find('.kgfs_object[kid='+obj_.kid+']').block({ message: '<strong>Loading</strong>' });
				// THIS IS FOR KGIS, KGFS WILL OVERWRITE THIS BELOW DUE TO SYNC
				imgsrc = "<img class='kgis_loading' src='"+kg_loadimg_+"' />";
				$.get(imgresturl, function(data) {
						// this part works for flexslider since it's async blocked
						imgsrc = data;
						// this part works for kgis in async unblocked
						kgifobj_.find('.kgfs_object[kid='+obj_.kid+'] > .kgfs_img > a').html(data);
						//kgifobj_.find('.kgfs_object[kid='+obj_.kid+']').unblock();
						
						//var kg_object = $("kgfs_object");
						//kg_object.style.width='75%';
				});
				changeObjWidth();
			}
			retval += "<div class='kgfs_img'>";
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "<form name='detail"+obj_.kid+"' action='"+lbase_+"?kid="+obj_.kid+"' method='post' enctype='multipart/form-data'>"+
				"<input type=hidden name='pid' value='"+plugin.pid+"'><input type=hidden name='token' value='"+ plugin.token +"'>"+
				"<input type=hidden name='sid' value='"+plugin.sid+"'><input type=hidden name='user' value='"+ plugin.user +"'>"+
				"<input type=hidden name='pass' value='"+plugin.pass+"'><input type=hidden name='url' value='"+ plugin.url +"'>"+
				"<input type=hidden name='theme' value='"+ plugin.theme +"'>"+
				"</form>"
				+"<a href='#' onclick='document.forms["+'"detail'+obj_.kid+'"'+"].submit(); return false;'>"; }
			retval += imgsrc;
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "</a>"; }
			retval += "</div>"; 
		}
		// TITLE GOES ABOVE
		if ((typeof tctrl_ !== 'undefined') && (tctrl_ != ''))
		{ 
			retval += "<div class='kgfs_title'>";
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "<form name='detail"+obj_.kid+"' action='"+lbase_+"?kid="+obj_.kid+"' method='post' enctype='multipart/form-data'>"+
				"<input type=hidden name='pid' value='"+plugin.pid+"'><input type=hidden name='token' value='"+ plugin.token +"'>"+
				"<input type=hidden name='sid' value='"+plugin.sid+"'><input type=hidden name='user' value='"+ plugin.user +"'>"+
				"<input type=hidden name='pass' value='"+plugin.pass+"'><input type=hidden name='url' value='"+ plugin.url +"'>"+
				"<input type=hidden name='theme' value='"+ plugin.theme +"'>"+
				"</form>"
				+"<a href='#' onclick='document.forms["+'"detail'+obj_.kid+'"'+"].submit(); return false;'>"; }
			retval += obj_[tctrl_];
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "</a>"; }
			retval += "</div>"; 
		}
		// DESCRIPTION GOES BELOW
		if ((typeof dctrl_ !== 'undefined') && (dctrl_ != ''))
		{ 
			retval += "<div class='kgfs_desc'>";
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "<form name='detail"+obj_.kid+"' action='"+lbase_+"?kid="+obj_.kid+"' method='post' enctype='multipart/form-data'>"+
				"<input type=hidden name='pid' value='"+plugin.pid+"'><input type=hidden name='token' value='"+ plugin.token +"'>"+
				"<input type=hidden name='sid' value='"+plugin.sid+"'><input type=hidden name='user' value='"+ plugin.user +"'>"+
				"<input type=hidden name='pass' value='"+plugin.pass+"'><input type=hidden name='url' value='"+ plugin.url +"'>"+
				"<input type=hidden name='theme' value='"+ plugin.theme +"'>"+
				"</form>"
				+"<a href='#' onclick='document.forms["+'"detail'+obj_.kid+'"'+"].submit(); return false;'>"; }
			retval += obj_[dctrl_]; 
			if ((typeof lbase_ !== 'undefined') && (lbase_ != ''))
			{ retval += "</a>"; }
			retval += "</div>"; 
		}
		retval += "</div>";
		return retval;
	}
	
	function ShowNoMore()
	{
		$('#nomore').show(); 
		setTimeout(function() { $('#nomore').hide(); }, 1000); 
	}
});

// end hiding script from old browsers -->


