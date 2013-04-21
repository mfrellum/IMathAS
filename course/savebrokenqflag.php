<?php
//IMathAS.  Records broken question flag
//(c) 2010 David Lippman

require("../validate.php");

if (!isset($_GET['qsetid']) || $myrights<20) {
	exit;
}

$ischanged = false;

$query = "UPDATE imas_questionset SET broken='{$_GET['flag']}' WHERE id='{$_GET['qsetid']}'";
mysqli_query($GLOBALS['link'],$query) or die("Query failed : $query " . mysqli_error($GLOBALS['link']));
if (mysqli_affected_rows($GLOBALS['link'])>0) {
	$ischanged = true;
}

if ($ischanged) {
	echo "OK";
} else {
	echo "Error";
}


?>
