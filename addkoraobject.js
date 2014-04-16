jQuery(document).ready(function() {
	
	jQuery('<a href="#"  id="kora-upload" class="button">Kora Upload</a>').insertAfter('.wp-editor-tools');   
	
   	jQuery('#kora-upload').click(function(){
   	
   		tb_show('Kora Upload',plugin.url+'/kora_upload.php?pid='+plugin.pid+
   		'&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
   		'&height=200&width=400&TB_iframe=true');
 		
 		return false;
   	});
	
});