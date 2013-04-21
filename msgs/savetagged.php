<?php
//IMathAS.  Records tags/untags for messages
//(c) 2007 David Lippman

require("../validate.php");

if (!isset($_GET['threadid'])) {
	exit;
}

$ischanged = false;

$query = "UPDATE imas_msgs SET isread=(isread^8) WHERE msgto='$userid' AND id='{$_GET['threadid']}'";
mysqli_query($GLOBALS['link'],$query) or die("Query failed : $query " . mysqli_error($GLOBALS['link']));
if (mysqli_affected_rows($GLOBALS['link'])()>0) {
	$ischanged = true;
}

if ($ischanged) {
	echo "OK";
} else {
	echo "Error";
}


?>
