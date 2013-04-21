<?php
require_once("../includes/filehandler.php");
	
function delitembyid($itemid) {
		
	$query = "SELECT itemtype,typeid FROM imas_items WHERE id='$itemid'";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed :$query " . mysqli_error($GLOBALS['link']));
	list($itemtype,$typeid) = mysql_fetch_row($result);
	
	if ($itemtype == "InlineText") {
		$query = "DELETE FROM imas_inlinetext WHERE id='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		
		$query = "SELECT filename FROM imas_instr_files WHERE itemid='$typeid'";
		$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		//$uploaddir = rtrim(dirname(__FILE__), '/\\') .'/files/';
		while ($row = mysql_fetch_row($result)) {
			$safefn = addslashes($row[0]);
			$query = "SELECT id FROM imas_instr_files WHERE filename='$safefn'";
			$r2 = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
			if (mysql_num_rows($r2)==1) {
				//unlink($uploaddir . $row[0]);
				deletecoursefile($row[0]);
			}
		}
		$query = "DELETE FROM imas_instr_files WHERE itemid='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		
		
	} else if ($itemtype == "LinkedText") {
		$query = "SELECT text FROM imas_linkedtext WHERE id='$typeid'";
		$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		$text = mysql_result($result,0,0);
		if (substr($text,0,5)=='file:') { //delete file if not used
			$safetext = addslashes($text);
			$query = "SELECT id FROM imas_linkedtext WHERE text='$safetext'"; //any others using file?
			$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
			if (mysql_num_rows($result)==1) { 
				//$uploaddir = rtrim(dirname(__FILE__), '/\\') .'/files/';
				$filename = substr($text,5);
				//unlink($uploaddir . $filename);
				deletecoursefile($filename);
			}
		}
		
		$query = "DELETE FROM imas_linkedtext WHERE id='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	} else if ($itemtype == "Forum") {
		$query = "DELETE FROM imas_forums WHERE id='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		
		$query = "DELETE FROM imas_forum_posts WHERE forumid='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : $query " . mysqli_error($GLOBALS['link']));
	} else if ($itemtype == "Assessment") {
		$query = "DELETE FROM imas_assessment_sessions WHERE assessmentid='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		$query = "DELETE FROM imas_questions WHERE assessmentid='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		$query = "DELETE FROM imas_assessments WHERE id='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	} else if ($itemtype == "Drill") {
		$query = "DELETE FROM imas_drillassess_sessions WHERE drillassessid='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		$query = "DELETE FROM imas_drillassess WHERE id='$typeid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	}
	$query = "DELETE FROM imas_items WHERE id='$itemid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));

}

function delrecurse($itemarr) { //delete items, recursing through blocks as needed
	foreach($itemarr as $itemid) {
		if (is_array($itemid)) {
			delrecurse($itemid['items']);
		} else {
			delitembyid($itemid);
		}
	}
}
?>
