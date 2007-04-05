<?php
	//Displays forum threads
	//(c) 2006 David Lippman
	
	require("../validate.php");
	if (!isset($teacherid) && !isset($tutorid) && !isset($studentid)) {
	   require("../header.php");
	   echo "You are not enrolled in this course.  Please return to the <a href=\"../index.php\">Home Page</a> and enroll\n";
	   require("../footer.php");
	   exit;
	}
	if (isset($teacherid)) {
		$isteacher = true;	
	} else {
		$isteacher = false;
	}
	
	$threadsperpage = 20;
	
	$cid = $_GET['cid'];
	$forumid = $_GET['forum'];
	$query = "SELECT name,postby,settings,grpaid FROM imas_forums WHERE id='$forumid'";
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	$forumname = mysql_result($result,0,0);
	$postby = mysql_result($result,0,1);
	$allowmod = ((mysql_result($result,0,2)&2)==2);
	$allowdel = ((mysql_result($result,0,2)&4)==4);
	$grpaid = mysql_result($result,0,3);
	$dofilter = false;
	if ($grpaid>0) {
		if (isset($_GET['ffilter'])) {
			$sessiondata['ffilter'.$forumid] = $_GET['ffilter'];
			writesessiondata();
		}
		if (!$isteacher) {
			$query = "SELECT agroupid FROM imas_assessment_sessions WHERE assessmentid='$grpaid' AND userid='$userid'";
			$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
			if (mysql_num_rows($result)>0) {
				$agroupid = mysql_result($result,0,0);
			} else {
				$agroupid=0;
			}
			$dofilter = true;
		} else {
			if (isset($sessiondata['ffilter'.$forumid]) && $sessiondata['ffilter'.$forumid]>0) {
				$agroupid = $sessiondata['ffilter'.$forumid];
				$dofilter = true;
			}
		}
		if ($dofilter) {
			$query = "SELECT userid FROM imas_assessment_sessions WHERE agroupid='$agroupid'";
			$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
			$limids = array();
			while ($row = mysql_fetch_row($result)) {
				$limids[] = $row[0];
			}
			$query = "SELECT userid FROM imas_teachers WHERE courseid='$cid'";
			$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
			while ($row = mysql_fetch_row($result)) {
				$limids[] = $row[0];
			}
			$limids = "'".implode("','",$limids)."'";
		}
	}
	
	
	
	if (!isset($_GET['page']) || $_GET['page']=='') {
		$page = 1;
	} else {
		$page = $_GET['page'];
	}
	if (isset($_GET['search']) && trim($_GET['search'])!='') {
		require("../header.php");
		echo "<div class=breadcrumb><a href=\"../index.php\">Home</a> &gt; <a href=\"../course/course.php?cid=$cid\">$coursename</a> &gt; ";
		echo "<a href=\"thread.php?page=$page&cid=$cid&forum=$forumid\">Forum Topics</a> &gt; Search Results</div>\n";
	
		echo "<h2>Forum Search Results</h2>";
		
		$safesearch = $_GET['search'];
		$safesearch = str_replace(' and ', ' ',$safesearch);
		$searchterms = explode(" ",$safesearch);
		$searchlikes = "(imas_forum_posts.message LIKE '%".implode("%' AND imas_forum_posts.message LIKE '%",$searchterms)."%')";
		$searchlikes2 = "(imas_forum_posts.subject LIKE '%".implode("%' AND imas_forum_posts.subject LIKE '%",$searchterms)."%')";
		$searchlikes3 = "(imas_users.LastName LIKE '%".implode("%' AND imas_users.LastName LIKE '%",$searchterms)."%')";
		if (isset($_GET['allforums'])) {
			$query = "SELECT imas_forums.id,imas_forum_posts.threadid,imas_forum_posts.subject,imas_forum_posts.message,imas_users.FirstName,imas_users.LastName,imas_forum_posts.postdate,imas_forums.name FROM imas_forum_posts,imas_forums,imas_users ";
			$query .= "WHERE imas_forum_posts.forumid=imas_forums.id AND imas_users.id=imas_forum_posts.userid AND imas_forums.courseid='$cid' AND ($searchlikes OR $searchlikes2 OR $searchlikes3)";
		} else {
			$query = "SELECT imas_forum_posts.forumid,imas_forum_posts.threadid,imas_forum_posts.subject,imas_forum_posts.message,imas_users.FirstName,imas_users.LastName,imas_forum_posts.postdate ";
			$query .= "FROM imas_forum_posts,imas_users WHERE imas_forum_posts.forumid='$forumid' AND imas_users.id=imas_forum_posts.userid AND ($searchlikes OR $searchlikes2 OR $searchlikes3)";
		}
		if ($dofilter) {
			$query .= " AND imas_forum_posts.userid IN ($limids)";
		}
		$query .= " ORDER BY imas_forum_posts.postdate DESC";
		$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
		while ($row = mysql_fetch_row($result)) {
			echo "<div class=block>";
			echo "<b>{$row[2]}</b>";
			if (isset($_GET['allforums'])) {
				echo ' (in '.$row[7].')';
			}
			echo "<br/>Posted by: {$row[4]} {$row[5]}, ";
			echo tzdate("F j, Y, g:i a",$row[6]);
			
			echo "</div><div class=blockitems>";
			echo filter($row[3]);
			echo "<p><a href=\"posts.php?cid=$cid&forum={$row[0]}&thread={$row[1]}\">Show full thread</a></p>";
			echo "</div>\n";
		}
		require("../footer.php");
		exit;
	}
	
	if (isset($_GET['modify'])) { //adding or modifying thread
		if (isset($_POST['subject'])) {  //form submitted
			if ($isteacher) {
				$type = $_POST['type'];
			} else {
				$type = 0;
			}
			if (trim($_POST['subject'])=='') {
				$_POST['subject']= '(none)';
			}
			if (isset($_POST['postanon']) && $_POST['postanon']==1) {
				$isanon = 1;
			} else {
				$isanon = 0;
			}
			if (!isset($_POST['replyby']) || $_POST['replyby']=="null") {
				$replyby = "NULL";
			} else if ($_POST['replyby']=="Always") {
				$replyby = 2000000000;
			} else if ($_POST['replyby']=="Never") {
				$replyby = 0;
			} else {
				require_once("../course/parsedatetime.php");
				$replyby = parsedatetime($_POST['replybydate'],$_POST['replybytime']);
			}
			
			if ($_GET['modify']=="new") {	
				$now = time();
				$query = "INSERT INTO imas_forum_posts (forumid,subject,message,userid,postdate,parent,posttype,isanon,replyby) VALUES ";
				$query .= "('$forumid','{$_POST['subject']}','{$_POST['message']}','$userid',$now,0,'$type','$isanon',$replyby)";
				mysql_query($query) or die("Query failed : $query " . mysql_error());
				$threadid = mysql_insert_id();
				$query = "UPDATE imas_forum_posts SET threadid='$threadid' WHERE id='$threadid'";
				mysql_query($query) or die("Query failed : $query " . mysql_error());
				$query = "INSERT INTO imas_forum_views (userid,threadid,lastview) VALUES ('$userid','$threadid',$now)";
				mysql_query($query) or die("Query failed : $query " . mysql_error());
				
				$query = "SELECT iu.email FROM imas_users AS iu,imas_forum_subscriptions AS ifs WHERE ";
				$query .= "iu.id=ifs.userid AND ifs.forumid='$forumid' AND iu.id<>'$userid'";
				if ($dofilter) {
					$query .= " AND iu.id IN ($limids)";
				}
				$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
				if (mysql_num_rows($result)>0) {
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= "From: $sendfrom\r\n";
					$message  = "<h4>This is an automated message.  Do not respond to this email</h4>\r\n";
					$message .= "<p>A new thread has been started in forum $forumname in course $coursename</p>\r\n";
					$message .= "<p>Subject:".stripslashes($_POST['subject'])."</p>";
					$message .= "<a href=\"http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/posts.php?cid=$cid&forum=$forumid&thread=$threadid\">";
					$message .= "View Posting</a>\r\n";
				}
				while ($row = mysql_fetch_row($result)) {
					mail($row[0],'New forum post notification',$message,$headers);
				}
			} else {
				$query = "UPDATE imas_forum_posts SET subject='{$_POST['subject']}',message='{$_POST['message']}',posttype='$type',replyby=$replyby,isanon='$isanon' ";
				$query .= "WHERE id='{$_GET['modify']}'";
				if (!$isteacher) { $query .= " AND userid='$userid'";}
				mysql_query($query) or die("Query failed : $query " . mysql_error());
			}
			header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/thread.php?page=$page&cid=$cid&forum=$forumid");
			exit;
		} else { //display mod
			$pagetitle = "Add/Modify Thread";
			$useeditor = "message";
			require("../header.php");
			echo "<div class=breadcrumb><a href=\"../index.php\">Home</a> &gt; <a href=\"../course/course.php?cid=$cid\">$coursename</a> ";
			echo "&gt; <a href=\"thread.php?page=$page&cid=$cid&forum=$forumid\">Forum Topics</a> &gt;";
			if ($_GET['modify']!="new") {
				echo "Modify Thread</div>\n";
				$query = "SELECT * from imas_forum_posts WHERE id='{$_GET['modify']}'";
				$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				echo "<h3>Modify Thread - \n";
				$replyby = $line['replyby'];
			} else {
				echo "Add Thread</div>\n";
				$line['subject'] = "";
				$line['message'] = "";
				$line['posttype'] = 0;
				$replyby = null;
				echo "<h3>Add Thread - \n";
			}
			$query = "SELECT name,settings FROM imas_forums WHERE id='$forumid'";
			$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
			$allowanon = mysql_result($result,0,1)%2;
			echo mysql_result($result,0,0).'</h3>';
			
			if ($replyby!=null && $replyby<2000000000 && $replyby>0) {
				$replybydate = tzdate("m/d/Y",$replyby);
				$replybytime = tzdate("g:i a",$replyby);	
			} else {
				$replybydate = tzdate("m/d/Y",time()+7*24*60*60);
				$replybytime = tzdate("g:i a",time()+7*24*60*60);
			}
			echo "<form method=post action=\"thread.php?page=$page&cid=$cid&forum=$forumid&modify={$_GET['modify']}\">\n";
			echo "<span class=form><label for=\"subject\">Subject:</label></span>";
			echo "<span class=formright><input type=text size=50 name=subject id=subject value=\"{$line['subject']}\"></span><br class=form>\n";
			echo "<span class=form><label for=\"message\">Message:</label></span>";
			echo "<span class=left><div class=editor><textarea id=message name=message style=\"width: 100%;\" rows=20 cols=70>{$line['message']}</textarea></div></span><br class=form>\n";
			if ($isteacher) {
				echo "<span class=form>Post Type:</span><span class=formright>\n";
				echo "<input type=radio name=type value=0 ";
				if ($line['posttype']==0) { echo "checked=1";}
				echo ">Regular<br>\n";
				echo "<input type=radio name=type value=1 ";
				if ($line['posttype']==1) { echo "checked=1";}
				echo ">Displayed at top of list<br>\n";
				echo "<input type=radio name=type value=2 ";
				if ($line['posttype']==2) { echo "checked=1";}
				echo ">Displayed at top and locked (no replies)<br>\n";
				echo "<input type=radio name=type value=3 ";
				if ($line['posttype']==3) { echo "checked=1";}
				echo ">Displayed at top and replies hidden from students\n";
				echo "</span><br class=form>";
				echo "<span class=form>Allow replies:</span><span class=formright>\n";
				echo "<input type=radio name=replyby value=\"null\" ";
				if ($line['replyby']==null) { echo "checked=1";}
				echo "/>Use default<br/>";
				echo "<input type=radio name=replyby value=\"Always\" ";
				if ($line['replyby']==2000000000) { echo "checked=1";}
				echo "/>Always<br/>";
				echo "<input type=radio name=replyby value=\"Never\" ";
				if ($line['replyby']==='0') { echo "checked=1";}
				echo "/>Never<br/>";
				echo "<input type=radio name=replyby value=\"Date\" ";
				if ($line['replyby']<2000000000 && $line['replyby']>0) { echo "checked=1";}
				echo "/>Before: "; 
				echo "<input type=text size=10 name=replybydate value=\"$replybydate\"/>";
				echo "<A HREF=\"#\" onClick=\"cal1.select(document.forms[0].replybydate,'anchor3','MM/dd/yyyy',(document.forms[0].replybydate.value==$replybydate')?(document.forms[0].replyby.value):(document.forms[0].replyby.value)); return false;\" NAME=\"anchor3\" ID=\"anchor3\"><img src=\"../img/cal.gif\" alt=\"Calendar\"/></A>";
				echo "at <input type=text size=10 name=replybytime value=\"$replybytime\"></span><br class=\"form\" />";
			} else {
				if ($allowanon==1) {
					echo "<span class=form>Post Anonymously:</span><span class=formright>";
					echo "<input type=checkbox name=\"postanon\" value=1 ";
					if ($line['isanon']==1) {echo "checked=1";}
					echo "></span><br class=form/>";
				}
			}
			echo "<div class=submit><input type=submit value='Submit'></div>\n";
			require("../footer.php");
			exit;
		}
	} else if (isset($_GET['remove']) && $isteacher) { //removing thread
		if (isset($_GET['confirm'])) {
			$query = "DELETE FROM imas_forum_posts WHERE id='{$_GET['remove']}'";
			mysql_query($query) or die("Query failed : $query " . mysql_error());

			$query = "DELETE FROM imas_forum_posts WHERE threadid='{$_GET['remove']}'";
			mysql_query($query) or die("Query failed : $query " . mysql_error());
			header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/thread.php?page=$page&cid=$cid&forum=$forumid");
			exit;
		} else {
			$pagetitle = "Remove Thread";
			require("../header.php");
			echo "<div class=breadcrumb><a href=\"../index.php\">Home</a> &gt; <a href=\"../course/course.php?cid=$cid\">$coursename</a> ";
			echo "&gt; <a href=\"thread.php?page=$page&cid=$cid&forum=$forumid\">Forum Topics</a> &gt; Remove Thread</div>";
			echo "<h3>Remove Thread</h3>\n";
			echo "<p>Are you SURE you want to remove this Thread and all enclosed posts?</p>\n";

			echo "<p><input type=button value=\"Yes, Remove\" onClick=\"window.location='thread.php?page=$page&cid=$cid&forum=$forumid&remove={$_GET['remove']}&confirm=true'\">\n";
			echo "<input type=button value=\"Nevermind\" onClick=\"window.location='thread.php?page=$page&cid=$cid&forum=$forumid'\"></p>\n";
			require("../footer.php");
			exit;
		}
	}
	
	$pagetitle = "Threads";
	$placeinhead = "<style type=\"text/css\">\n@import url(\"$imasroot/forums/forums.css\");\n</style>\n";
	require("../header.php");
	
	
	echo "<div class=breadcrumb><a href=\"../index.php\">Home</a> &gt; <a href=\"../course/course.php?cid=$cid\">$coursename</a> &gt; Forum Topics</div>\n";
	echo "<h3>Forum - $forumname</h3>\n";
	
	
	$query = "SELECT COUNT(id) FROM imas_forum_posts WHERE parent=0 AND forumid='$forumid'";
	if ($dofilter) {
		$query .= " AND userid IN ($limids)";
	}
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	$numpages = ceil(mysql_result($result,0,0)/$threadsperpage);
	
	if ($numpages > 1) {
		echo "<div >Page: ";
		if ($page < $numpages/2) {
			$min = max(2,$page-4);
			$max = min($numpages-1,$page+8+$min-$page);
		} else {
			$max = min($numpages-1,$page+4);
			$min = max(2,$page-8+$max-$page);
		}
		if ($page==1) {
			echo "<b>1</b> ";
		} else {
			echo "<a href=\"thread.php?page=1&cid=$cid&forum=$forumid\">1</a> ";
		}
		if ($min!=2) { echo " ... ";}
		for ($i = $min; $i<=$max; $i++) {
			if ($page == $i) {
				echo "<b>$i</b> ";
			} else {
				echo "<a href=\"thread.php?page=$i&cid=$cid&forum=$forumid\">$i</a> ";
			}
		}
		if ($max!=$numpages-1) { echo " ... ";}
		if ($page == $numpages) {
			echo "<b>$numpages</b> ";
		} else {
			echo "<a href=\"thread.php?page=$numpages&cid=$cid&forum=$forumid\">$numpages</a> ";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if ($page>1) {
			echo "<a href=\"thread.php?page=".($page-1)."&cid=$cid&forum=$forumid\">Previous</a> ";
		}
		if ($page < $numpages) {
			echo "<a href=\"thread.php?page=".($page+1)."&cid=$cid&forum=$forumid\">Next</a> ";
		}
		echo "</div>\n";
	}
	echo "<form method=get action=\"thread.php\">";
	echo "<input type=hidden name=page value=\"$page\"/>";
	echo "<input type=hidden name=cid value=\"$cid\"/>";
	echo "<input type=hidden name=forum value=\"$forumid\"/>";
	
?>
	Search: <input type=text name="search" /> <input type=checkbox name="allforums" />All forums in course? <input type="submit" value="Search"/>
	</form>
<?php
	if ($isteacher && $grpaid>0) {
		$curfilter = $sessiondata['ffilter'.$forumid];
		$query = "SELECT DISTINCT agroupid FROM imas_assessment_sessions WHERE assessmentid='$grpaid' ORDER BY agroupid";
		$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
		echo "<script type=\"text/javascript\">";
		echo 'function chgfilter() {';
		echo '  var ffilter = document.getElementById("ffilter").value;';
		echo "  window.location = \"thread.php?page=$pages&cid=$cid&forum=$forumid&ffilter=\"+ffilter;";
		echo '}';
		echo '</script>';
		echo '<p>Show posts for group: <select id="ffilter" onChange="chgfilter()"><option value="0" ';
		if ($curfilter==0) { echo 'selected="1"';}
		echo '>All groups</option>';
		$grpcnt = 1;
		while ($row = mysql_fetch_row($result)) {
			echo "<option value=\"{$row[0]}\" ";
			if ($curfilter==$row[0]) { echo 'selected="1"';}
			echo ">$grpcnt</option>";
			$grpcnt++;
		}
		echo '</select></p>';
	}
	if (($myrights > 5 && time()<$postby) || $isteacher) {
		echo "<p><a href=\"thread.php?page=$page&cid=$cid&forum=$forumid&modify=new\">Add New Thread</a>\n";
		if ($isteacher) {
			echo " | <a href=\"postsbyname.php?page=$page&cid=$cid&forum=$forumid\">List Posts by Name</a>";
		}
		echo "</p>";
	}
?>
	<table class=forum>
	<thead>
	<tr><th>Topic</th><th>Replies</th><th>Views (Unique)</th><th>Last Post Date</th></tr>
	</thead>
	<tbody>
<?php
	
	
	$query = "SELECT threadid,COUNT(id) AS postcount,MAX(postdate) AS maxdate FROM imas_forum_posts ";
	$query .= "WHERE forumid='$forumid' ";
	if ($dofilter) {
		$query .= " AND userid IN ($limids) ";
	}
	$query .= "GROUP BY threadid";
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	while ($row = mysql_fetch_row($result)) {
		$postcount[$row[0]] = $row[1] -1;
		$maxdate[$row[0]] = $row[2];
	}
	
	$query = "SELECT threadid,lastview FROM imas_forum_views WHERE userid='$userid'";
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	while ($row = mysql_fetch_row($result)) {
		$lastview[$row[0]] = $row[1];
	}
	
	$query = "SELECT imas_forum_posts.id,count(imas_forum_views.userid) FROM imas_forum_views,imas_forum_posts ";
	$query .= "WHERE imas_forum_views.threadid=imas_forum_posts.id AND imas_forum_posts.parent=0 AND ";
	$query .= "imas_forum_posts.forumid='$forumid' ";
	if ($dofilter) {
		$query .= "AND imas_forum_posts.userid IN ($limids) ";
	}
	$query .= "GROUP BY imas_forum_posts.id";
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	while ($row = mysql_fetch_row($result)) {
		$uniqviews[$row[0]] = $row[1]-1;
	}
	
	$query = "SELECT imas_forum_posts.*,imas_users.LastName,imas_users.FirstName FROM imas_forum_posts,imas_users WHERE ";
	$query .= "imas_forum_posts.userid=imas_users.id AND imas_forum_posts.parent=0 AND imas_forum_posts.forumid='$forumid' ";
	if ($dofilter) {
		$query .= "AND imas_forum_posts.userid IN ($limids) ";
	}
	$query .= "ORDER BY imas_forum_posts.posttype DESC,imas_forum_posts.id DESC ";
	$offset = ($page-1)*$threadsperpage;
	$query .= "LIMIT $offset,$threadsperpage";// OFFSET $offset"; 
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if (isset($postcount[$line['id']])) {
			$posts = $postcount[$line['id']];
			$lastpost = tzdate("F j, Y, g:i a",$maxdate[$line['id']]);
		} else {
			$posts = 0;
			$lastpost = '';
		}
		echo "<tr ";
		if ($line['posttype']>0) {
			echo "class=sticky";
		}
		echo "><td>";
		echo "<span class=right>\n";
		if ($isteacher || ($line['userid']==$userid && $allowmod)) {
			echo "<a href=\"thread.php?page=$page&cid=$cid&forum=$forumid&modify={$line['id']}\">Modify</a> ";
		} 
		if ($isteacher || ($allowdel && $line['userid']==$userid && $posts==0)) {
			echo "<a href=\"thread.php?page=$page&cid=$cid&forum=$forumid&remove={$line['id']}\">Remove</a>";
		}
		echo "</span>\n";
		
		echo "<b><a href=\"posts.php?cid=$cid&forum=$forumid&thread={$line['id']}&page=$page\">{$line['subject']}</a></b>: {$line['LastName']}, {$line['FirstName']}";
		
		echo "</td>\n";
		
		echo "<td class=c>$posts</td><td class=c>{$line['views']} ({$uniqviews[$line['id']]})</td><td class=c>$lastpost ";
		if ($lastpost=='' || $maxdate[$line['id']]>$lastview[$line['id']]) {
			echo "<span style=\"color: red;\">New</span>";
		}
		echo "</td></tr>\n";
	}
?>
	</tbody>
	</table>
<?php
	if (($myrights > 5 && time()<$postby) || $isteacher) {
		echo "<p><a href=\"thread.php?page=$page&cid=$cid&forum=$forumid&modify=new\">Add New Thread</a></p>\n";
	}
	
	require("../footer.php");
?>