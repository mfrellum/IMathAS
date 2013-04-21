<?php
//IMathAS.  Records library items junk tag
//(c) 2010 David Lippman

require("../validate.php");

if (!isset($_GET['libitemid']) || $myrights<20) {
	exit;
}

$ischanged = false;

$query = "UPDATE imas_library_items SET junkflag='{$_GET['flag']}' WHERE id='{$_GET['libitemid']}'";
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
