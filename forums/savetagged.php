<?php
//IMathAS.  Records tags/untags
//(c) 2007 David Lippman

require("../validate.php");

if (!isset($_GET['threadid'])) {
	exit;
}

$ischanged = false;

$query = "UPDATE imas_forum_views SET tagged='{$_GET['tagged']}' WHERE userid='$userid' AND threadid='{$_GET['threadid']}'";
mysqli_query($GLOBALS['link'],$query) or die("Query failed : $query " . mysqli_error($GLOBALS['link']));
if (mysqli_affected_rows($GLOBALS['link'])>0) {
	$ischanged = true;
}
if (!$ischanged) {
	$query = "INSERT INTO imas_forum_views (userid,threadid,lastview,tagged) ";
	$query .= "VALUES ('$userid','{$_GET['threadid']}',0,'{$_GET['tagged']}')";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : $query " . mysqli_error($GLOBALS['link']));
	if (mysqli_affected_rows($GLOBALS['link'])>0) {
		$ischanged = true;
	}
}

if ($ischanged) {
	echo "OK";
} else {
	echo "Error";
}


?>
