<?php
$usePDO = true;
require("../validate.php");
if (isset($guestid)) {
	$teacherid=$guestid;
}
if (!isset($sessiondata['sessiontestid']) && !isset($teacherid) && !isset($tutorid) && !isset($studentid)) {
	echo "<html><body>";
	echo _("You are not authorized to view this page.  If you are trying to reaccess a test you've already started, access it from the course page");
	echo "</body></html>\n";
	exit;
}
$actas = false;
$isreview = false;
if (isset($teacherid) && isset($_GET['actas'])) {
	$userid = $_GET['actas'];
	unset($teacherid);
	$actas = true;
}
include("displayq2.php");
include("testutil.php");
include("asidutil.php");

$inexception = false;
$exceptionduedate = 0;

$isdiag = isset($sessiondata['isdiag']);
if ($isdiag) {
	$diagid = $sessiondata['isdiag'];
}
$isltilimited = (isset($sessiondata['ltiitemtype']) && $sessiondata['ltiitemtype']==0 && $sessiondata['ltirole']=='learner');
$showbreadcrumbs =(!$isdiag && strpos($_SERVER['HTTP_REFERER'],'treereader')===false && !$isltilimited);

if (isset($_GET['id'])) { //if starting or returning to test
	$aid = intval($_GET['id']);
	
	//load assessment data
	$STM = $DBH->prepare("SELECT * FROM imas_assessments WHERE id=?");
	$STM->execute(array($aid)) or die("Query failed : " . $DBH->errorInfo());
	$testsettings = $STM->fetch(PDO::FETCH_ASSOC);
	$now = time();
	
	if (trim($testsettings['itemorder'])=='') {
		echo _('No questions in assessment!');
		exit;
	}
	
	//check assessment dates, and give notice and exit if outside
	$isreview = checkassessmentdates($testsettings);
	
	//check for password
	if (trim($testsettings['password'])!='' && !isset($teacherid) && !isset($tutorid)) { //has passwd
		$pwfail = true;
		$out = '';
		if (isset($_POST['password'])) {
			if (trim($_POST['password'])==trim($testsettings['password'])) {
				$pwfail = false;
			} else {
				$out = '<p>' . _('Password incorrect.  Try again.') . '<p>';
			}
		} 
		if ($pwfail) {
			require("../header.php");
			if ($showbreadcrumbs) {
				echo "<div class=breadcrumb>$breadcrumbbase <a href=\"../course/course.php?cid=$cid\">{$sessiondata['coursename']}</a> ";
				echo '&gt; ', _('Assessment'), '</div>';
			}
			echo $out;
			echo '<h2>'.$adata['name'].'</h2>';
			echo '<p>', _('Password required for access.'), '</p>';
			echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"showtest.php?cid=$cid&amp;id=$aid\">";
			echo "<p>Password: <input type=\"password\" name=\"password\" /></p>";
			echo '<input type="submit" value="', _('Begin Assessment'), '" />';
			echo "</form>";
			require("../footer.php");
			exit;
		}
	}
	
	//get latepass info
	if (!isset($teacherid) && !isset($tutorid) && !$actas && !isset($sessiondata['stuview'])) {
		$STM = $DBH->prepare("SELECT latepass FROM imas_students WHERE userid=? AND courseid=?");
		$STM->execute(array($userid,$cid)) or die("Query failed : " . $DBH->errorInfo());
		$sessiondata['latepasses'] = $STM->fetchColumn(0);

	} else {
		$sessiondata['latepasses'] = 0;
	}
	
	$sessiondata['istutorial'] = $testsettings['istutorial'];
	$_SESSION['choicemap'] = array();
	
	//get most recent imas_assessment_session
	$STM = $DBH->prepare("SELECT id,agroupid,lastanswers,bestlastanswers,starttime FROM imas_assessment_sessions WHERE userid=? AND assessmentid=? and isreview=? ORDER BY id DESC LIMIT 1");
	$STM->execute(array($userid,$aid,$isreview?1:0)) or die("Query failed : " . $DBH->errorInfo());
	$line = $STM->fetch(PDO::FETCH_ASSOC);
	
	if (isset($_GET['retake']) && !$isreview) {
		$STM = $DBH->prepare("SELECT COUNT(*) FROM imas_assessment_sessions WHERE userid=? AND assessmentid=? and isreview=0");
		$STM->execute(array($userid,$aid)) or die("Query failed : " . $DBH->errorInfo());
		$takenversions = $STM->fetchColumn(0);
		if ($takenversions>$testsettings['defregens']) {
			require('header.php');
			echo '<p>'._('You have used up all your retakes for this assessment.').' <a href="../course/course.php?cid='.$cid.'">'._('Back').'</a></p>';
			require('footer.php');
			exit;
		}
		//this will force a new asid to be created
		$line = false;
	}
	
	if ($line===false) { //starting test - no existing record
		$err = '';
		
		//check if is a group assessment
		$stugroupid = 0;
		$groupmembers = array();
		if ($testsettings['isgroup']>0 && !$isreview && !isset($teacherid) && !isset($tutorid) && !isset($_GET['retake'])) {
			$STM = $DBH->prepare("SELECT i_sg.id FROM imas_stugroups as i_sg JOIN imas_stugroupmembers as i_sgm ON i_sg.id=i_sgm.stugroupid WHERE i_sgm.userid=? AND i_sg.groupsetid=?");
			$STM->execute(array($userid,$testsettings['groupsetid'])) or die("Query failed : " . $DBH->errorInfo());
			$grpdata = $STM->fetch(PDO::FETCH_ASSOC);
			if ($grpdata!==false) { //already in a group
				$stugroupid = $grpdata['id'];
				$sessiondata['groupid'] = $stugroupid;
			} else { 
				if ($testsettings['isgroup']==3) { 
					//is an instructor-selected group, and student has not been put in a group yet.
					require("header.php");
					echo '<p>'._('You are not yet a member of a group.  Contact your instructor to be added to a group.').'</p>';
					echo '<p><a href="../course/course.php?cid='.$cid.'">'._('Back').'</a></p>';
					require("footer.php");
					exit;
				} else {
					if (isset($_POST['grpsubmit'])) {
						list($newmembers, $err) = processgroupform($testsettings);
						$STM = $DBH->prepare("INSERT INTO imas_stugroups (name,groupsetid) VALUES ('Unnamed group',?)");
						$STM->execute(array($testsettings['groupsetid'])) or die("Query failed : " . $DBH->errorInfo());
						$stugroupid = $DBH->lastInsertId();
						
						$STM = $DBH->prepare("INSERT INTO imas_stugroupmembers (userid,stugroupid) VALUES (?,?)");
						$STM->execute(array($userid,$stugroupid)) or die("Query failed : " . $DBH->errorInfo());
						foreach ($newmembers as $newmemid) {
							$STM->execute(array($newmemid,$stugroupid)) or die("Query failed : " . $DBH->errorInfo());
						}
					} else {
						require("header.php");
						echo '<div id="headershowtest" class="pagetitle"><h2>', _('Select group members'), '</h2></div>';
						
						echo '<p>'._('If you want to join an existing group, exit now, and talk with one of the group member to have them add you.').'</p>';
						if ($testsettings['isgroup']==1) {
							echo '<p>'._('If you want to create a new group, each group member (other than you) to be added should select their name and enter their login password.').'</p>';
						} else {
							echo '<p>'._('If you want to create a new group, each group member (other than you) to be added should select their name.').'</p>';
						}
						echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"showtest.php?cid=$cid&amp;id=$aid\">";
						echo '<input type="hidden" name="disptime" value="'.time().'" />';
						$nongrouped = getnongroupedstudents($testsettings);
						echo '<p><input type=submit name="grpsubmit" value="', _('Record Group and Begin'), '"/></p>';
						echo '</form>';
						require("footer.php");
						exit;
					}
				}
			}
		}
		
		$sessiondata['groupid'] = $stugroupid;
			
			
		//build assessment data
		
		list($qlist,$seedlist,$reviewseedlist,$scorelist,$attemptslist,$lalist) = generateAssessmentData($adata['itemorder'],$adata['shuffle'],$aid);
		
		$bestscorelist = $scorelist.';'.$scorelist.';'.$scorelist;  //bestscores;bestrawscores;firstscores
		$scorelist = $scorelist.';'.$scorelist;  //scores;rawscores 
		$bestattemptslist = $attemptslist;
		$bestseedslist = $seedlist;
		$bestlalist = $lalist;
		
		$starttime = time();
		
		if (isset($sessiondata['lti_lis_result_sourcedid']) && strlen($sessiondata['lti_lis_result_sourcedid'])>1) {
			$ltisourcedid = addslashes(stripslashes($sessiondata['lti_lis_result_sourcedid'].':|:'.$sessiondata['lti_outcomeurl'].':|:'.$sessiondata['lti_origkey'].':|:'.$sessiondata['lti_keylookup']));
		} else {
			$ltisourcedid = '';
		}
		
		$STM = $DBH->prepare("INSERT INTO imas_assessment_sessions (userid,assessmentid,questions,seeds,scores,attempts,lastanswers,starttime,bestscores,bestattempts,bestseeds,bestlastanswers,agroupid,feedback,lti_sourcedid) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$STM->execute(array($userid,$aid,$qlist,$seedlist,$scorelist,$attemptslist,$lalist,$starttime,$bestscorelist,$bestattemptslist,$bestseedslist,$bestlalist,$stugroupid,$testsettings['deffeedbacktext'],$ltisourcedid));
		if ($STM->rowCount()==0) {
			echo 'Error DupASID. <a href="showtest.php?cid='.$cid.'&aid='.$aid.'">Try again</a>';
			exit;
		}
		$sessiondata['sessiontestid'] = $DBH->lastInsertId();
		
		//create or update imas_grades record
		if (!$isreview) {
			$STMg = $DBH->prepare("SELECT id,refid FROM imas_grades WHERE userid=? AND gradetypeid=? and gradetype='online'");
			$STMg->execute(array($userid,$aid)) or die("Query failed : " . $DBH->errorInfo());
			$imasgradesdata = $STMg->fetch(PDO::FETCH_ASSOC);
			
			if ($imasgradesdata===false) {
				$STMi = $DBH->prepare("INSERT INTO imas_grades (gradetypeid,userid,refid,gradetype) VALUES (?,?,?,?)");
				$STMi->execute(array($aid,$userid,$sessiondata['sessiontestid'],'online')) or die("Query failed : " . $DBH->errorInfo());
			} else {
				$STMu = $DBH->prepare("UPDATE imas_grades SET refid=? WHERE id=?");
				$STMu->execute(array($sessiondata['sessiontestid'],$imasgradesdata['id'])) or die("Query failed : " . $DBH->errorInfo());
			}
		}
		
		if ($stugroupid!=0) {
			//if a group assessment and already in a group, we'll create asids for all the group members now, and imas_grades records if needed
			$STMu = $DBH->prepare("SELECT userid FROM imas_stugroupmembers WHERE stugroupid=? AND userid<>?");
			$STMg = $DBH->prepare("SELECT id,refid FROM imas_grades WHERE userid=? AND gradetypeid=? and gradetype='online'");
			$STMi = $DBH->prepare("INSERT INTO imas_grades (gradetypeid,userid,refid,gradetype) VALUES (?,?,?,?)");
			$STMup = $DBH->prepare("UPDATE imas_grades SET refid=? WHERE id=?");
			$STMu->execute(array($stugroupid,$userid));
			while ($row = $STMu->fetch(PDO::FETCH_ASSOC)) {
				$STM->execute(array($row['userid'],$aid,$qlist,$seedlist,$scorelist,$attemptslist,$lalist,$starttime,$bestscorelist,$bestattemptslist,$bestseedslist,$bestlalist,$stugroupid,$testsettings['deffeedback'],''));
				$thisgasid = $DBH->lastInsertId();
				$STMg->execute(array($row['userid'],$aid));
				if ($r = $STMg->fetch(PDO::FETCH_ASSOC)) {
					$STMi->execute(array($aid, $row['userid'], $thisgasid, 'online'));
				} else {
					$STMup->execute(array($thisgasid, $r['id']));
				}
			}
		}

		
	} else { //already started test
		// if teacher or guest - delete out out assessment session
		if ($myrights<6 || isset($teacherid) || isset($tutorid)) {  
			require_once("../includes/filehandler.php");
			//deleteasidfilesbyquery(array('userid'=>$userid,'assessmentid'=>$aid),1);
			deleteasidfilesbyquery2('userid',$userid,$aid,1);
			$query = "DELETE FROM imas_assessment_sessions WHERE userid='$userid' AND assessmentid='$aid' LIMIT 1";
			$result = mysql_query($query) or die("Query failed : $query: " . mysql_error());
			header('Location: ' . $urlmode  . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/showtest.php?cid={$_GET['cid']}&id=$aid");
			exit;
		}
		if ($testsettings['isgroup']==0 || $line['agroupid']>0) {
			$sessiondata['groupid'] = $line['agroupid'];
		} else if (!isset($teacherid) && !isset($tutorid)) { //only happens if isgroup>0 && agroupid==0
			//already has asid, but broken from group
			//create new group
			if ($testsettings['isgroup']==3) {
				require("header.php");
				echo '<p>'._('You are not yet a member of a group.  Contact your instructor to be added to a group.').'</p>';
				echo '<p><a href="../course/course.php?cid='.$cid.'">'._('Back').'</a></p>';
				require("footer.php");
				exit;
			}
			
			$STM = $DBH->prepare("INSERT INTO imas_stugroups (name,groupsetid) VALUES ('Unnamed group',?)");
			$STM->execute(array($testsettings['groupsetid'])) or die("Query failed : " . $DBH->errorInfo());
			$stugroupid = $DBH->lastInsertId();
			
			$sessiondata['groupid'] = $stugroupid;
			
			$STM = $DBH->prepare("INSERT INTO imas_stugroupmembers (userid,stugroupid) VALUES (?,?)");
			$STM->execute(array($userid,$stugroupid)) or die("Query failed : " . $DBH->errorInfo());
			
			$STM = $DBH->prepare("UPDATE imas_assessment_sessions SET agroupid=? WHERE id=?");
			$STM->execute(array($stugroupid,$line['id'])) or die("Query failed : " . $DBH->errorInfo());
		}
		if (isset($sessiondata['lti_lis_result_sourcedid'])) {
			$altltisourcedid = stripslashes($sessiondata['lti_lis_result_sourcedid'].':|:'.$sessiondata['lti_outcomeurl'].':|:'.$sessiondata['lti_origkey'].':|:'.$sessiondata['lti_keylookup']);
			if ($altltisourcedid != $line['lti_sourcedid']) {
				$STM = $DBH->prepare("UPDATE imas_assessment_sessions SET lti_sourcedid=? WHERE id=?");
				$STM->execute(array($altltisourcedid,$line['id'])) or die("Query failed : " . $DBH->errorInfo());
			}
		}
	}
	//set session values
	$sessiondata['isreview'] = $isreview;
	if (isset($teacherid) || isset($tutorid) || $actas) {
		$sessiondata['isteacher']=true;
	} else {
		$sessiondata['isteacher']=false;
	}
	if ($actas) {
		$sessiondata['actas']=$_GET['actas'];
		$sessiondata['isreview'] = false;
	} else {
		unset($sessiondata['actas']);
	}
	if (strpos($_SERVER['HTTP_REFERER'],'treereader')!==false) {
		$sessiondata['intreereader'] = true;
	} else {
		$sessiondata['intreereader'] = false;
	}
	
	//load course data into session
	$STM = $DBH->prepare("SELECT name,theme,topbar,msgset,toolset FROM imas_courses WHERE id=?");
	$STM->execute(array($cid));
	$crow = $STM->fetch(PDO::FETCH_ASSOC);
	
	$sessiondata['courseid'] = intval($cid);
	$sessiondata['coursename'] = $crow['name'];
	$sessiondata['coursetheme'] = $crow['theme'];
	$sessiondata['coursetopbar'] =  $crow['topbar'];
	$sessiondata['msgqtoinstr'] = (floor($crow['msgset']/5))&2;
	$sessiondata['coursetoolset'] = $crow['toolset'];
	if (isset($studentinfo['timelimitmult'])) {
		$sessiondata['timelimitmult'] = $studentinfo['timelimitmult'];
	} else {
		$sessiondata['timelimitmult'] = 1.0;
	}
	
	
	writesessiondata();
	session_write_close();
	if ($err != '') {
		require('header.php');
		echo $err;
		echo '<p><a href="'.$urlmode  . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/showtest.php">';
		echo _('Continue').'</a></p>';
		require('footer.php');
	} else {
		header('Location: ' . $urlmode  . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/showtest.php");
	}
	exit;	
}

//if we're here, they've already started or returned to the test
if (!isset($sessiondata['sessiontestid'])) {
	echo "<html><body>", _('Error.  Access test from course page'), "</body></html>\n";
	exit;
}
$testid = intval($sessiondata['sessiontestid']);
$asid = $testid;
$isteacher = $sessiondata['isteacher'];
if (isset($sessiondata['actas'])) {
	$userid = $sessiondata['actas'];
}

//load assessment_sessions data
$STM = $DBH->prepare("SELECT * FROM imas_assessment_sessions WHERE id=?");
$STM->execute(array($testid)) or die("Query failed : " . $DBH->errorInfo());
$asiddata = $STM->fetch(PDO::FETCH_ASSOC);

$questions = explode(',', $asiddata['questions']);
$seeds = explode(",",$asiddata['seeds']);
$sp = explode(';',$asiddata['scores']);
$scores = explode(',', $sp[0]);
$rawscores = explode(',', $sp[1]);

$attempts = explode(",",$asiddata['attempts']);
$lastanswers = explode("~",$asiddata['lastanswers']);
if ($asiddata['timeontask']=='') {
	$timesontask = array_fill(0,count($questions),'');
} else {
	$timesontask = explode(',',$asiddata['timeontask']);
}

$lti_sourcedid = $asiddata['lti_sourcedid'];

if (trim($asiddata['reattempting'])=='') {
	$reattempting = array();
} else {
	$reattempting = explode(",",$asiddata['reattempting']);
}
$bestseeds = explode(",",$asiddata['bestseeds']);
$sp = explode(';',$asiddata['bestscores']);
$bestscores = explode(',', $sp[0]);
$bestrawscores = explode(',', $sp[1]);
$firstrawscores = explode(',', $sp[2]);

$bestattempts = explode(",",$asiddata['bestattempts']);
$bestlastanswers = explode("~",$asiddata['bestlastanswers']);
$starttime = $asiddata['starttime'];

if ($starttime == 0) {
	$starttime = time();
	$STM = $DBH->prepare("UPDATE imas_assessment_sessions SET starttime=? WHERE id=?");
	$STM->execute(array($starttime,$testid)) or die("Query failed : " . $DBH->errorInfo());
}

//load assessment data
$STM = $DBH->prepare("SELECT * FROM imas_assessments WHERE id=?");
$STM->execute(array($asiddata['assessmentid'])) or die("Query failed : " . $DBH->errorInfo());
$testsettings = $STM->fetch(PDO::FETCH_ASSOC);

$cid = $testsettings['courseid'];

//Switch VideoCue to Embed if no video info
if ($testsettings['displaymethod']=='VideoCue' && $testsettings['viddata']=='') {
	$testsettings['displaymethod']= 'Embed';
}

//add tracking data to links in intro
if (!$isteacher) {
	$rec = "data-base=\"assessintro-{$asiddata['assessmentid']}\" ";
	$testsettings['intro'] = str_replace('<a ','<a '.$rec, $testsettings['intro']);
}

$timelimitkickout = ($testsettings['timelimit']<0);
$testsettings['timelimit'] = abs($testsettings['timelimit']);
//do time limit mult
$testsettings['timelimit'] *= $sessiondata['timelimitmult'];

//break apart deffeedback setting.  **Maybe should store separately?
list($testsettings['testtype'],$testsettings['showans']) = explode('-',$testsettings['deffeedback']);


//if submitting, verify it's the correct assessment
if (isset($_POST['asidverify']) && $_POST['asidverify']!=$testid) {
	require('header.php');
	echo _('Error.  It appears you have opened another assessment since you opened this one. ');
	echo _('Only one open assessment can be handled at a time. Please reopen the assessment and try again. ');
	echo "<a href=\"../course/course.php?cid={$testsettings['courseid']}\">", _('Return to course page'), "</a>";
	require('footer.php');
	exit;
}
//verify group is ok
if ($testsettings['isgroup']>0 && !$isteacher &&  ($asiddata['agroupid']==0 || ($sessiondata['groupid']>0 && $asiddata['agroupid']!=$sessiondata['groupid']))) {
	require('header.php');
	echo _('Error.  Looks like your group has changed for this assessment. Please reopen the assessment and try again.');
	echo "<a href=\"../course/course.php?cid={$testsettings['courseid']}\">", _('Return to course page'), "</a>";
	require('footer.php');
	exit;
}

$now = time();

//check assessment dates, and give notice and exit if outside
$isreview = checkassessmentdates($testsettings);

if ($isreview) {
	$testsettings['testtype']="Practice";
	$testsettings['defattempts'] = 0;
	$testsettings['defpenalty'] = 0;
	$testsettings['showans'] = '0';
} else if ($timelimitkickout) {
	$now = time();
	$timelimitremaining = $testsettings['timelimit']-($now - $starttime);
	//check if past timelimit
	if ($timelimitremaining<1 || isset($_GET['superdone'])) {
		$finalize = true;
		$_GET['finalize']=true;
	}
}
$qi = getquestioninfo($questions,$testsettings);


//load options from testsettings
$allowperqregen = 5;
$allowretakes = 0;
$showeachscore = true;
$showansduring = false;
$noindivscores = true;
$reshowatend = false;
$showhints = true;
$showtips = 1;


/*  Ready for HTML output  */
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$useeditor = 1;
if ($testsettings['eqnhelper']==1 || $testsettings['eqnhelper']==2) {
	$placeinhead = '<script type="text/javascript">var eetype='.$testsettings['eqnhelper'].'</script>';
	$placeinhead .= "<script type=\"text/javascript\" src=\"$imasroot/javascript/eqnhelper.js?v=030112\"></script>";
	$placeinhead .= '<style type="text/css"> div.question input.btn { margin-left: 10px; } </style>';
	
} else if ($testsettings['eqnhelper']==3 || $testsettings['eqnhelper']==4) {
	$placeinhead = "<link rel=\"stylesheet\" href=\"$imasroot/assessment/mathquill.css?v=102113\" type=\"text/css\" />";
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')!==false) {
		$placeinhead .= '<!--[if lte IE 7]><style style="text/css">
			.mathquill-editable.empty { width: 0.5em; }
			.mathquill-rendered-math .numerator.empty, .mathquill-rendered-math .empty { padding: 0 0.25em;}
			.mathquill-rendered-math sup { line-height: .8em; }
			.mathquill-rendered-math .numerator {float: left; padding: 0;}
			.mathquill-rendered-math .denominator { clear: both;width: auto;float: left;}
			</style><![endif]-->';
	}
	$placeinhead .= "<script type=\"text/javascript\" src=\"$imasroot/javascript/mathquill_min.js?v=102113\"></script>";
	$placeinhead .= "<script type=\"text/javascript\" src=\"$imasroot/javascript/mathquilled.js?v=102113\"></script>";
	$placeinhead .= "<script type=\"text/javascript\" src=\"$imasroot/javascript/AMtoMQ.js?v=102113\"></script>";
	$placeinhead .= '<style type="text/css"> div.question input.btn { margin-left: 10px; } </style>';
	
}
$useeqnhelper = $testsettings['eqnhelper'];

if ($testsettings['showtips']==2) {
	$placeinhead .= "<script type=\"text/javascript\" src=\"$imasroot/javascript/eqntips.js?v=032810\"></script>";
}
$placeinhead .= '<script type="text/javascript">
   function toggleintroshow(n) {
      var link = document.getElementById("introtoggle"+n);
      var content = document.getElementById("intropiece"+n);
      if (link.innerHTML.match("Hide")) {
	   link.innerHTML = link.innerHTML.replace("Hide","Show");
	   content.style.display = "none";
      } else {
	   link.innerHTML = link.innerHTML.replace("Show","Hide");
	   content.style.display = "block";
      }
     }
     function togglemainintroshow(el) {
	if ($("#intro").hasClass("hidden")) {
		$(el).html("'._("Hide Intro/Instructions").'");
		$("#intro").removeClass("hidden").addClass("intro");
	} else {
		$("#intro").addClass("hidden");
		$(el).html("'._("Show Intro/Instructions").'");
	}
     }
     </script>';
if ($testsettings['displaymethod'] == "VideoCue") {
	$placeinhead .= '<script src="'.$imasroot.'/javascript/ytapi.js"></script>';
}

require("header.php");
if ($testsettings['noprint'] == 1) {
	echo '<style type="text/css" media="print"> div.question, div.todoquestion, div.inactive { display: none;} </style>';
}
if (!$isdiag && !$isltilimited && !$sessiondata['intreereader']) {
	if (isset($sessiondata['actas'])) {
		echo "<div class=breadcrumb>$breadcrumbbase <a href=\"../course/course.php?cid=$cid\">{$sessiondata['coursename']}</a> ";
		echo "&gt; <a href=\"../course/gb-viewasid.php?cid=$cid&amp;asid=$testid&amp;uid={$sessiondata['actas']}\">", _('Gradebook Detail'), "</a> ";
		echo "&gt; ", _('View as student'), "</div>";
	} else {
		echo "<div class=breadcrumb>";
		echo "<span style=\"float:right;\">$userfullname</span>";
		if (isset($sessiondata['ltiitemtype']) && $sessiondata['ltiitemtype']==0) {
			echo "$breadcrumbbase ", _('Assessment'), "</div>";
		} else {
			echo "$breadcrumbbase <a href=\"../course/course.php?cid={$testsettings['courseid']}\">{$sessiondata['coursename']}</a> ";
 
			echo "&gt; ", _('Assessment'), "</div>";
		}
	}
} else if ($isltilimited) {
	echo '<span style="float:right;">';
	if ($testsettings['msgtoinstr']==1) {
		$STM = $DBH->prepare("SELECT COUNT(id) FROM imas_msgs WHERE msgto=? AND courseid=? AND (isread=0 OR isread=4)");
		$STM->execute(array($userid, $cid)) or die("Query failed : " . $DBH->errorInfo());
		$msgcnt = $STM->fetchColumn(0);
		
		echo "<a href=\"#\" onclick=\"GB_show('"._('Messages')."','$imasroot/msgs/msglist.php?cid=$cid',800,'auto')\">", _('Messages'), "</a>";	
		//echo "<a href=\"$imasroot/msgs/msglist.php?cid=$cid\" onclick=\"return confirm('", _('This will discard any unsaved work.'), "');\">", _('Messages'), " ";
		if ($msgcnt>0) {
			echo '<span style="color:red;">('.$msgcnt.' new)</span>';
		} 
		echo '</a> ';
	}
	if ($testsettings['allowlate']==1 && $sessiondata['latepasses']>0 && !$isreview) {
		echo "<a href=\"$imasroot/course/redeemlatepass.php?cid=$cid&aid={$testsettings['id']}\" onclick=\"return confirm('", _('This will discard any unsaved work.'), "');\">", _('Redeem LatePass'), "</a> ";
	}
	
	if ($sessiondata['ltiitemid']==$testsettings['id'] && $isreview) {
		if ($testsettings['showans']!='N') {
			echo '<p><a href="../course/gb-viewasid.php?cid='.$cid.'&asid='.$testid.'">', _('View your scored assessment'), '</a></p>';
		}
	}
	echo '</span>';
}

     
//------------------------------------------

function processgroupform($testsettings) {
	if ($testsettings['isgroup']==1 && isset($CFG['GEN']['newpasswords'])) {
		require_once("../includes/password.php");
	}
	$STMu = $DBH->prepare("SELECT password,FirstName,LastName FROM imas_users WHERE id=?");
	$err = '';
	$groupmembers = array();
	$potentialgroupmembers = array();
	for ($i=1;$i<$testsettings['groupmax'];$i++) {
		if (isset($_POST['user'.$i]) && $_POST['user'.$i]!=0) {
			if ($testsettings['isgroup']==1) {
				$STMu->execute(array($_POST['user'.$i]));
				$row = $STMu->fetch(PDO::FETCH_ASSOC);
				$md5pw = md5($_POST['pw'.$i]);
				if (!($row['password']==$md5pw || (isset($CFG['GEN']['newpasswords']) && password_verify($_POST['pw'.$i],$row['password'])))) {
					$err .= "<p>".$row['FirstName'].' '.$row['LastName'].": ", _('password incorrect'), "</p>";
					continue;
				} 	
			} 
			$potentialgroupmembers[] = $_POST['user'.$i];
		}
	}
	//check to make sure potential users aren't already in a group
	$STM = $DBH->prepare("SELECT id,agroupid FROM imas_assessment_sessions WHERE userid=? AND assessmentid=?");
	foreach ($potentialgroupmembers as $potuser) {
		$STM->execute(array($potuser, $aid));	
		if (($row = $STM->fetch(PDO::FETCH_ASSOC))===false) {
			$groupmembers[] = $potuser;
		} else {
			$STMu->execute(array($potuser));
			$row = $STMu->fetch(PDO::FETCH_ASSOC);
			$err .= "<p>", sprintf(_('%s already has a group.  No change made'), $row['FirstName'].' '.$row['LastName']), ".</p>";		
		}
	}
	return array($groupmembers, $err);
}

?>
