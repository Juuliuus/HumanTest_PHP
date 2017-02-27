/*  

PHP code for a "Human Test" to ensure user is a human being and not a robot.

    Copyright (C) 2014 Julius Heinrich Ludwig Schön / Ronald Michael Spicer
    created by Julius Schön / R. Spicer
    Foto.TimePirate.org / TimePirate.org / PaganToday.net
    
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

<?php require_once('Connections/DBaccess.php'); ?>
<?php
session_name('YourSessionName');
session_start();

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

function myDestroy(){
  $_SESSION = array();
  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
	  $params = session_get_cookie_params();
	  setcookie(session_name(), '', time() - 42000,
		  $params["path"], $params["domain"],
		  $params["secure"], $params["httponly"]
	  );
  }
  // Finally, destroy the session.
  session_destroy();	
}

function myRedirect($dGoTo){
  if (isset($_SERVER['QUERY_STRING'])) {
    $dGoTo .= (strpos($dGoTo, '?')) ? "&" : "?";
    $dGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $dGoTo));
}

function sendMeMail($subject, $message){
 $to = "Someonoe@Some.where";
 $from = "You@WhereYou.are";
 $headers = "From:" . $from;
 mail($to,$subject,$message,$headers);
// echo "Mail Sent.";	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formPost")) {
	
if ($_SESSION['okToPost']){
	
	$myValidated = 1;
	if (isset( $_POST['postBody'])) {
	  $pb = trim($_POST['postBody']);
	  if ( $pb == '' ){
  	    $myValidated = 0;
		$_SESSION['postMessage'] = '>>> The body of the post can not be blank';
	  }//if pb
	}//isset
	

	if ( $myValidated ) { 

	  $insertSQL = sprintf("INSERT INTO comitem (username, subject, body, TimeAdded) VALUES (%s, %s, %s, %s)",
						   GetSQLValueString($_POST['postName'], "text"),
						   GetSQLValueString($_POST['postSubject'], "text"),
						   GetSQLValueString($_POST['postBody'], "text"),
						   GetSQLValueString(date('Y-m-d H:i:s', time() ), "date") );
	
	  mysql_select_db($database_Pagan, $Pagan);
	  $Result1 = mysql_query($insertSQL, $Pagan) or die(mysql_error());
	  myDestroy();
	  sendMeMail("Someone Posted!", "You have a new post in your DB.");
	  
	  myRedirect("PostSuccess.php");
	  
	}//validated
  }//okToPost
}//Formpost





function clearArray($Ar, $val)
{
//initialize a MultiDim Array to the specified value
  $r = count($Ar);
  $c = count($Ar[0]);
  for ($i=0;$i<$r;$i++){
      for ($j=0;$j<$c;$j++) {
          $Ar[$i][$j] = $val;
      }
  }
  return $Ar;
}

function generateSlots($Ar)
{
//generates 5 randome coordinates for the testing 5x5 grid on the resulting html page
//base on "column" portion of 2x5 array, ie. the "5"
  $cCnt = count($Ar[0]);
  for ($i=0;$i<$cCnt;$i++){
    do{
      $reDo = false;
      $r = mt_rand(0, 4);
      $c = mt_rand(0, 4);
//the array is (should be!) initialized to -1's. Then the following line limits
//the later FOR statement to only elements that have already been assigned values 
      $idx = array_search(-1, $Ar[0], true);
      if ($idx===false){
          //just in case the array counters / pointers go out of range
          print_r('i==========Emergency BAILOUT=====================================4');
          break;
      }

      for ($j=0;$j<$idx;$j++) {
//keep doing this until you have ensured that no grid coordinates are used twice; that is,
//all of them must be unique.         
          $rKey = $Ar[0][$j];
          $cKey = $Ar[1][$j];
          if ( ($r===$rKey) & ($c === $cKey ) ){
              $reDo = true;
              break;
          }//if
         
      }//for
       
    } while ($reDo);


//ok, it is a unique set of coords, assign them, continue until all array
//slots filled
    $Ar[0][$i] = $r;
    $Ar[1][$i] = $c;
   
  }//main FOR
  return $Ar;
}

function assignAryToAry($Ar, $Mas)
{
//used to assign the random picks to the master 2dim array, and
//to assign the resulting user input in answer array into the slave 2dim array
	$rCnt = count($Ar[0]);
	for ($i=0;$i<$rCnt;$i++){
	  $Mas[$Ar[0][$i]][$Ar[1][$i]] = $i+1;
	}
	return $Mas;
}


function testForNumber($test1){
  try {
//if values are out of this range, I'm not writing them...then who!???	  
//used to have a test to see if AlphaChars and such were hacked in but it failed and I got tired of trying to find a simple answer to determining that
    if  ( ($test1 < -1) || ($test1 > 4) ) {
        throw new Exception('Bad INPUT: Out of RANGE');
    }
  }
  catch(Exception $e) {
	sendMeMail("Exception Raised in PaganToday", "Someone seems to be filling in hidden fields. Message: ".$e->getMessage());
//	echo 'Message: _________________________' .$e->getMessage();
//	die('hacker');
    myDestroy();
	myRedirect("PostFailed.php");
  }

}

function assignAnswer($Ar) {

  global $row1,$col1,$row2,$col2,$row3,$col3,$row4,$col4,$row5,$col5;

//get user input coords from the FORM, assign values to 2x5 array to assign
//to slave 2dim array. Check to make sure everything is integers and in range
//these are all cave-man brute force, don't know any function that helps
  testForNumber($row1);
  testForNumber($col1);
  testForNumber($row2);
  testForNumber($col2);
  testForNumber($row3);
  testForNumber($col3);
  testForNumber($row4);
  testForNumber($col4);
  testForNumber($row5);
  testForNumber($col5);
 
  $Ar[0][0] = $row1;
  $Ar[1][0] = $col1;
  $Ar[0][1] = $row2;
  $Ar[1][1] = $col2;
  $Ar[0][2] = $row3;
  $Ar[1][2] = $col3;
  $Ar[0][3] = $row4;
  $Ar[1][3] = $col4;
  $Ar[0][4] = $row5;
  $Ar[1][4] = $col5;

//if we've passed other tests now we check that they've ticked off all five numbers.
//we know this because the form input fields are -1 if they havent been used by the numbers being clicked.
  for ($i=4;$i>-1;$i--){
	if  ( ($Ar[0][$i] < 0) || ($Ar[1][$i] < 0) ) {
        myDestroy();
	myRedirect("PostFailed.php");
	}
	  
  }
       
  return $Ar;
}

function areEqual($M, $S)
{
//element by element comparison between master 2dim array and slave 2dimarray
  $theyAre = true;
  $r = count($M);
  $c = count($M[0]);
  for ($i=0;$i<$r;$i++){
      for ($j=0;$j<$c;$j++) {
          if ( $M[$i][$j] != $S[$i][$j] ) {
            $theyAre = false;
            break;
         }
      }
  }
  return $theyAre;
}

if ( !isset( $_SESSION['gridInitialized'] ) ){
	$_SESSION['gridInitialized'] = false;
	$_SESSION['okToPost'] = false;
	$_SESSION['redoCnt'] = 0;
	$_SESSION['questMessage'] = '';
	$_SESSION['postMessage'] = '';		
}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmRedo")) {	
	$_SESSION['redoCnt']++;
	$_SESSION['gridInitialized'] = false;
	if ($_SESSION['redoCnt'] > 5){
	  myDestroy();
	  //randomly put a limit into how many times they can request a new grid because I get suspicious
	  myRedirect("PostFailed.php");
	}
}		

//first time through, Initialize test grid
if ( $_SESSION['gridInitialized'] === false ){

  $_SESSION['master'] = array
	(
	array(0,1,2,3,4),
	array(11,12,13,14,15),
	array(22,23,24,25,26),
	array(33,34,35,36,37),
	array(44,45,46,47,48)
	);
  $_SESSION['master'] = clearArray($_SESSION['master'], 0);

  $rndPick = array
	(
	array(11,12,13,14,15),
	array(21,22,23,24,25),
	);
  $rndPick = clearArray($rndPick, -1);

  $rndPick = generateSlots($rndPick);
  $_SESSION['master'] = assignAryToAry($rndPick, $_SESSION['master']);
	
  $_SESSION['gridInitialized'] = true;
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmGrid")) {	

  $_SESSION['questMessage'] = '';
  
  $slave = array
	(
	array(0,1,2,3,4),
	array(11,12,13,14,15),
	array(22,23,24,25,26),
	array(33,34,35,36,37),
	array(44,45,46,47,48)
	);
  $slave = clearArray($slave, 0);
	
  $row1 = (int)$_POST['row1'];
  $col1 = (int)$_POST['col1'];
  $row2 = (int)$_POST['row2'];
  $col2 = (int)$_POST['col2'];
  $row3 = (int)$_POST['row3'];
  $col3 = (int)$_POST['col3'];
  $row4 = (int)$_POST['row4'];
  $col4 = (int)$_POST['col4'];
  $row5 = (int)$_POST['row5'];
  $col5 = (int)$_POST['col5'];
  
  $answer = array
	(
	array(11,12,13,14,15),
	array(21,22,23,24,25),
	);
  $answer = clearArray($answer, -1);
  $answer = assignAnswer( $answer );
  
  $slave = assignAryToAry($answer, $slave);
  
  if ( areEqual($_SESSION['master'], $slave) ) {
	$_SESSION['okToPost'] = true;
	$_SESSION['questMessage'] = '>>> You are cleared to Post...';
  } else {
	myDestroy();
	myRedirect("PostFailed.php");
  }

}		

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Post</title>
<meta name="Description" content="Add Post">
<meta http-equiv="imagetoolbar" content="no">

<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="favicon.ico">

<script>
        /*    
        @licstart  The following is the entire license notice for the 
        JavaScript code in this page.

        Copyright (C) 2014 Julius Heinrich Ludwig Schön / Ronald Michael Spicer
        created by Julius Schön / R. Spicer
        Foto.TimePirate.org / TimePirate.org / PaganToday.net

        The JavaScript code in this page is free software: you can
        redistribute it and/or modify it under the terms of the GNU
        General Public License (GNU GPL) as published by the Free Software
        Foundation, either version 3 of the License, or (at your option)
        any later version.  The code is distributed WITHOUT ANY WARRANTY;
        without even the implied warranty of MERCHANTABILITY or FITNESS
        FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

        As additional permission under GNU GPL version 3 section 7, you
        may distribute non-source (e.g., minimized or compacted) forms of
        that code without the copy of the GNU GPL normally required by
        section 4, provided you include this license notice and a URL
        through which recipients can access the Corresponding Source.   


        @licend  The above is the entire license notice
        for the JavaScript code in this page.
        */
</script>

<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>

<!--<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
-->
<script type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,save,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,fullscreen",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,print,|,bold,italic,underline,strikethrough,sub,sup,charmap,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,insertdate,inserttime,|,forecolor,backcolor,hr,|,link,unlink,|,",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
		width: "95%",
        auto_focus: "postBody",
        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
</script>


<script type="text/javascript" src="scripts/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="scripts/mootools-more-1.4.0.1.js"></script>


<script>

cnt = 0;
lastVal = '';
lastColor = '';
//I allow the user to correct one click error backward, coords are stored in these
lastR = -1;
lastC = -1;

function recordIt(r,c,theID){
//when they click a grid position the coords are stored in hidden fields
//and the "clue" number is changed to an "X" to show it was recorded
	el = $(theID);
	theText = el.get('text');
	
	if (theText == 'X') {
	  if ((lastVal != '') & ( (lastR == r) & (lastC==c) ) ) {
	    document.getElementById('row'+cnt.toString()).value=-1;
	    document.getElementById('col'+cnt.toString()).value=-1;
	    cnt--;
	    el.setStyle('color', lastColor );
	    el.set( 'text', lastVal ); 
	    lastVal = '';
	  }
	} else {
	  if (cnt < 5 ) {
	    cnt++;
	    lastVal = theText;
	    lastColor = el.getStyle( 'color' );
	    el.setStyle('color', '#DFDFDF' );
	    el.set( 'text', 'X' ); 
	    lastR = r;
	    lastC = c;
	    document.getElementById('row'+cnt.toString()).value=r;
	    document.getElementById('col'+cnt.toString()).value=c;
	  }
	  
	};
//	alert('row: '+r+'  col: '+c+' ID: '+theID);
}

function doCancel() {

	var req = new Request.HTML({
	  method: 'get',
	  url: 'YourCleanUp.php'
	});

	req.send(); 

	window.open( 'BackToMainForum.php', '_self' );
}

</script>


<link href="styles/html5.css" rel="stylesheet" type="text/css">
<link href="styles/ie7.css" rel="stylesheet" type="text/css">
<link href="styles/forum.css" rel="stylesheet" type="text/css">
<style type="text/css">
.TitleFields {
	width: 40%;
}
.SubjFields {
	width: 80%;
}



<?php

if ( !$_SESSION['okToPost'] ){     

  $clue = array
	(
	  '#9C91EE',
	  '#9C96ED',
	  '#BE92EB',
	  '#BE93EA',
	  '#C593EA',
	  '#D993EA',
	  '#CB95E8',
	  '#AD97E8',
	  '#BD9AE4',
	  '#B89DE3',
	  '#A0ACE0',
	  '#AAA0E0',
	  '#BFA4DD',
	  '#ADA7DC',
	  '#B5A6DD',
	  '#9C91E9',
	  '#9C96EA',
	  '#BE92E8',
	  '#BE95EA',
	  '#C593EA',
	  '#D793EA',
	  '#CA95E8',
	  '#AD93E8',
	  '#BD9AEB',
	  '#B89DE1',
	  '#A0AAE0',
	  '#ABA0E0',
	  '#BEA4DD',
	  '#ADA5DC',
	  '#B5A5DD'
	);
	
  $nouse = array
	(
	  '#475AE0',
	  '#4545E4',
	  '#4350E9',
	  '#5544EA',
	  '#3E39EA',
	  '#3A47EB',
	  '#3C3CEA',
	  '#6C49E4',
	  '#3039E4',
	  '#5743E2',
	  '#436CE7',
	  '#5043E7',
	  '#5638E4',
	  '#443CE1',
	  '#3547E8',
	  '#475BE0',
	  '#4545E3',
	  '#4750E9',
	  '#5643EA',
	  '#3E3AEA',
	  '#3A47EC',
	  '#3C3CEB',
	  '#6C48E4',
	  '#3339E4',
	  '#5748E2',
	  '#436DE7',
	  '#5042E7',
	  '#5538E4',
	  '#433CE1',
	  '#3549E8'
	);
  
  $bkg = array
	(
	  '#393F9F',
	  '#422ECB',
	  '#3C1DE7',
	  '#1525E8',
	  '#3229D3',
	  '#4E2ECC',
	  '#3164C1',
	  '#304AC9',
	  '#3F33A6',
	  '#3D32A0',
	  '#3150A2',
	  '#3444AF',
	  '#333DB9',
	  '#3540BF',
	  '#343CC0',
	  '#393F9E',
	  '#422ECC',
	  '#3C1AE7',
	  '#1425E8',
	  '#3329D3',
	  '#4E2DCC',
	  '#3164C4',
	  '#304AC5',
	  '#3F32A6',
	  '#3E32A0',
	  '#3250A2',
	  '#3445AF',
	  '#333DBA',
	  '#3540BE',
	  '#343BC0'
	);


  echo '  
  #frmGrid {
	margin-left: 60px;
  }

  #frmRedo {
	margin-left: 220px;
  }

  #tblGrid {
	margin-left: 20px;
  }

  #tblGrid tr td {
	  text-align: center;
	  vertical-align: middle;
	  font-family: "Courier New", Courier, monospace;
	  font-size: 24px;
	  font-weight: bold;
  }';

//generate CSS entries for the IDs for each grid position, 
//and in CSS assign random background colors and font colors
  $r = count($_SESSION['master']);
  $c = count($_SESSION['master'][0]);
  for ($i=0;$i<$r;$i++){
      for ($j=0;$j<$c;$j++) {
		  
		if ( $_SESSION['master'][$i][$j] === 0 ){
		  echo '
		  #tblGrid tr #s'.$i.$j.'  {
			  color: '.$nouse[mt_rand(0, 29)].';
			  background-color: '.$bkg[mt_rand(0, 29)].';
		  }
		  ';		  
		} else {
		  echo '
		  #tblGrid tr #s'.$i.$j.'  {
			  color: '.$clue[mt_rand(0, 29)].';
			  background-color: '.$bkg[mt_rand(0, 29)].';
		  }
		  ';		  
		}
      }//for j
  }// for i

}//end !okToPost


?>

</style>
</head>

<body>

<div class="container">

  <div class="header" ondblclick="homeclick()">
    <h1>My Page Title</h1>
    <h2>Post to Community Forum</h2>
    
  </div> <!-- end .header -->
  
<div id="SuperBody">
<div class="sidebar1">
<noscript>
<p>&nbsp;</p>
<p>It looks like javascript is not enabled, this page can't function without it, sorry. Turn on Javascript and refresh page to continue...</p>
<p>&nbsp;</p>
</noscript>

<div id="Q1question">
<?php

if ( !$_SESSION['okToPost'] ){     
  echo '
    <p class="forumright">Here you can post to our forum anonymously.</p>
    <p class="forumright">If you have any questions <a href="ajWordsDisplay.php?ID=6" class="AjaxClass">Contact us</a>.</p>
    <p class="forumright">To help prevent Internet &quot;Bots&quot; from accessing the posting page you need to click the brightest  numbers below, in order from 1 -&gt; 5, and  prove you are human. Cookies need to be enabled.</p>
    <p class="forumright">&nbsp;</p> 
    <p><img src="images/PostHint.png" width="629" height="154" alt="Sun"></p>
';

  echo '
  <form action="" method="POST" name="frmRedo" id="frmRedo">
    <input type="submit" name="btnRedo" id="btnRedo" value="New">
    <input type="hidden" name="MM_insert" value="frmRedo">
  </form>';

  echo '<table width="272" border="1" cellpadding="1" cellspacing="5" id="tblGrid">';

//create grid entries, set "unset" entries to a random number (serves as clutter) and give they're ID names 
//so CSS can be read and assign to javascript click event
  $r = count($_SESSION['master']);
  $c = count($_SESSION['master'][0]);
  for ($i=0;$i<$r;$i++){
	  echo '<tr>';
      for ($j=0;$j<$c;$j++) {
		  
		if ( $_SESSION['master'][$i][$j] === 0 ){
		  echo '
            <td id="s'.$i.$j.'" onClick="recordIt('.$i.','.$j.',\'s'.$i.$j.'\')">'.mt_rand(1, 5).'</td>
		  ';		  
		} else {
		  echo '
            <td id="s'.$i.$j.'" onClick="recordIt('.$i.','.$j.',\'s'.$i.$j.'\')">'.$_SESSION['master'][$i][$j].'</td>
		  ';		  
		}
      }//for j
	  echo '</tr>';
  }// for i
  echo '</table>';
//  echo '<p>&nbsp;</p>';
 
}//end !okToPost

?>

</div><!-- Q1question-->



<div id="Q1form">
<?php 
if ( !$_SESSION['okToPost'] ){     

  echo '
  <form action="" method="POST" name="frmGrid" id="frmGrid">
    <input name="row1" type="hidden" id="row1" value="-1">
    <input name="row2" type="hidden" id="row2" value="-1">
    <input name="row3" type="hidden" id="row3" value="-1">
    <input name="row4" type="hidden" id="row4" value="-1">
    <input name="row5" type="hidden" id="row5" value="-1"><br>
    <input name="col1" type="hidden" id="col1" value="-1">
    <input name="col2" type="hidden" id="col2" value="-1">
    <input name="col3" type="hidden" id="col3" value="-1">
    <input name="col4" type="hidden" id="col4" value="-1">
    <input name="col5" type="hidden" id="col5" value="-1">
    
    <input type="submit" name="btnDone" id="btnDone" value="Done">
    <input type="hidden" name="MM_insert" value="frmGrid">
  </form>';
  
  
  
  echo '<p>&nbsp;</p>';


} 
?>
</div>



<div id="divPost">
<?php

if ($_SESSION['okToPost'] ){
	
  echo '
    <p class="forumright"><img src="images/FromFlorence/PTSm.png" width="200" height="171" alt="Sun"></p>
    <p class="forumright">Approved human being. You can post!</p>
    <p class="forumright">Forum posts are monitored. This site reserves the right to decide whether your comments are posted or not.</p>
    <p class="forumright">Hate speech, insulting language, racism/bigotry, sexism, trolling are not tolerated. Also remember: We are not yelling at each other, we are discussing topics related to...etc......</p>
    <p class="forumright">&nbsp;</p>'; 
	
echo '<form action="'.$editFormAction.';" method="POST" name="formPost" id="formPost">
      <p>
        <label for="postBody"></label>
        <textarea name="postBody" id="postBody"></textarea>
      </p>
      <p>
        <label for="postSubject">Subject</label>
        <input name="postSubject" type="text" class="SubjFields" id="postSubject" maxlength="150">
      </p>
      <p>
        <label for="postName">Name you want to use</label>
        <input name="postName" type="text" class="TitleFields" id="postName" maxlength="75">
      </p>
      <p>
        <input type="submit" name="postPost" id="postPost" value="Submit Post">
		&nbsp;&nbsp;&nbsp;'.$_SESSION['postMessage'].'
      </p>
	  <input type="hidden" name="MM_insert" value="formPost">
    </form>';	
}
?>
<!--<script type="text/javascript">document.id("postBody").focus();</script>';
-->    <p><a href="Javascript:{}" onclick="doCancel()">Cancel Posting</a></p>
    <p>&nbsp;</p>
</div> <!--divPost-->
    
</div> <!-- end .sidebar1 -->
</div> <!-- end .superbody -->


      
<div class="clearfloat"></div>
  
    
  <div class="footer">
  </div> <!-- end .footer -->

</div> <!-- end .container -->

</body>
</html>