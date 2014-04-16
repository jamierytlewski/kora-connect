<html><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
</head><body>
<?php 
require_once('/matrix/home/tyler.erskine/public_html/kora/includes/koraSearch.php');
$token = get_option('kordat_dbtoken');
$pid = get_option('kordat_dbproj')
$sid = get_option('kordat_dbscheme')
$kid = $_REQUEST['kid'];


$qclause = $kid;
$query = new KORA_Clause('KID', '=', $qclause);

$results = KORA_Search( $token,
                          $pid,
                           $sid,
                           $query,
                           array('File', 'Title'),
                           array(),
                           $limitStart=0,
                           $limitNum=0);
						   

						   
$step1=$results[$qclause];
$step2= $step1['File'];
$step3= $step2['localName'];	
$name= getFullURLFromFileName($step3);			   
echo "<img src=".$name.">";
?></body></html>