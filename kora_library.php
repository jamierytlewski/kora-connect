<h2>Kora Library</h2>

<?php
global $wpdb;
$token = get_option('kordat_dbtoken');
$pid = get_option('kordat_dbproj');
$sid = get_option('kordat_dbscheme');
$library= $wpdb->prefix . 'koralibrary';
$query = "SELECT * FROM  $library";

if(empty($wpdb->get_results("SELECT * FROM  $library"))){
	echo "Library is empty, add new Kora objects in order for them to appear here!";
}

foreach( $wpdb->get_results("SELECT * FROM  $library") as $key => $row) {
	$url = preg_replace('/ /','%20',$row->url);
	echo "<div class = 'lib_obj'>";
	echo "<div class='lib_image'><img src=".$url." alt=".$row->KID."></div>";
	echo "<div class='lib_title'>".$row->title."</div>";
	echo "<div class='lib_kid'><strong>KID:</strong> ".$row->KID."</div>";
	echo "<div class='lib_url'><a target='_blank' href='".$row->url."'>".$row->url."</a></div>";
	echo "</div>";
}
?>