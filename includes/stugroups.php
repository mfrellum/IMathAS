<?php

function deletegroupset($grpsetid) {
	$query = "SELECT id FROM imas_stugroups WHERE groupsetid='$grpsetid'";
	$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	while ($row = mysqli_fetch_row($result)) {
		deletegroup($row[0]);
	}
	$query = "DELETE FROM imas_stugroupset WHERE id='$grpsetid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$query = "UPDATE imas_assessments SET isgroup=0,groupsetid=0 WHERE groupsetid='$grpsetid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$query = "UPDATE imas_forums SET groupsetid=0 WHERE groupsetid='$grpsetid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$query = "UPDATE imas_wikis SET groupsetid=0 WHERE groupsetid='$grpsetid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));

}

function deletegroup($grpid,$delposts=true) {
	removeallgroupmembers($grpid);
	
	if ($delposts) {
		$query = "SELECT id FROM imas_forum_threads WHERE stugroupid='$grpid'";
		$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		$todel = array();
		while ($row = mysqli_fetch_row($result)) {
			$todel[] = $row[0];
		}
		if (count($todel)>0) {
			$dellist = implode(',',$todel);
			$query = "DELETE FROM imas_forum_threads WHERE id IN ($dellist)";
			mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
			$query = "DELETE FROM imas_forum_posts WHERE threadid IN ($dellist)";
			mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
		}
	} else {
		$query = "UPDATE imas_forum_threads SET stugroupid=0 WHERE stugroupid='$grpid'";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	}
	$query = "DELETE FROM imas_stugroups WHERE id='$grpid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$query = "DELETE FROM imas_wiki_revisions WHERE stugroupid='$grpid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
}

function removeallgroupmembers($grpid) {
	$query = "DELETE FROM imas_stugroupmembers WHERE stugroupid='$grpid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	//$query = "SELECT assessmentid,userid FROM imas_assessment_sessions WHERE agroupid='$grpid'";
	//$result = mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	//any assessment session using this group, set group to 0
	$query = "UPDATE imas_assessment_sessions SET agroupid=0 WHERE agroupid='$grpid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$now = time();
	
	if (isset($GLOBALS['CFG']['log'])) {
		$query = "INSERT INTO imas_log (time,log) VALUES ($now,'deleting members from $grpid')";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	}
}

function removegroupmember($grpid, $uid) {
	$query = "DELETE FROM imas_stugroupmembers WHERE stugroupid='$grpid' AND userid='$uid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	//update any assessment sessions using this group
	$query = "UPDATE imas_assessment_sessions SET agroupid=0 WHERE agroupid='$grpid' AND userid='$uid'";
	mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	
	$now = time();
	if (isset($GLOBALS['CFG']['log'])) {
		$query = "INSERT INTO imas_log (time,log) VALUES ($now,'deleting $uid from $grpid')";
		mysqli_query($GLOBALS['link'],$query) or die("Query failed : " . mysqli_error($GLOBALS['link']));
	}
}

?>
