<?php
//IMathAS:  Add/remove calendar item
//(c) 2006 David Lippman

/*** master php includes *******/
require("../validate.php");

/*** pre-html data manipulation, including function code *******/

//set some page specific variables and counters
$overwriteBody = 0;
$body = "";
if (isset($_GET['tb'])) {
	$totb = $_GET['tb'];
} else {
	$totb = 'b';
}

if (!(isset($teacherid))) {  
	$overwriteBody = 1;
	$body = "You need to log in as a teacher to access this page";
} elseif (!(isset($_GET['cid']))) { 
	$overwriteBody = 1;
	$body = "You need to access this page from the link on the course page";
} elseif (isset($_GET['remove'])) { // a valid delete request loaded the page
	$cid = $_GET['cid'];
	$block = $_GET['block'];	
	
	$itemid = $_GET['id'];
	
	$query = "DELETE FROM imas_items WHERE id='$itemid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
			
	$query = "SELECT itemorder FROM imas_courses WHERE id='$cid'";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	$items = unserialize(mysql_fetch_first($result));
	
	$blocktree = explode('-',$block);
	$sub =& $items;
	for ($i=1;$i<count($blocktree);$i++) {
		$sub =& $sub[$blocktree[$i]-1]['items']; //-1 to adjust for 1-indexing
	}
	$key = array_search($itemid,$sub);
	array_splice($sub,$key,1);
	$itemorder = addslashes(serialize($items));
	$query = "UPDATE imas_courses SET itemorder='$itemorder' WHERE id='$cid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
} else {
	$block = $_GET['block'];
	$cid = $_GET['cid'];
	
	$query = "INSERT INTO imas_items (courseid,itemtype) VALUES ";
	$query .= "('$cid','Calendar');";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$itemid = mysqli_insert_id($GLOBALS['link'])();
	$query = "SELECT itemorder FROM imas_courses WHERE id='$cid'";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	$line = mysql_fetch_assoc($result);
	$items = unserialize($line['itemorder']);
	
	$blocktree = explode('-',$block);
	$sub =& $items;
	for ($i=1;$i<count($blocktree);$i++) {
		$sub =& $sub[$blocktree[$i]-1]['items']; //-1 to adjust for 1-indexing
	}
	if ($totb=='b') {
		$sub[] = $itemid;
	} else if ($totb=='t') {
		array_unshift($sub,$itemid);
	}
	
	$itemorder = addslashes(serialize($items));
	$query = "UPDATE imas_courses SET itemorder='$itemorder' WHERE id='$cid'";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
} 
header('Location: ' . $urlmode  . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/course.php?cid=$cid");
exit;

?>
