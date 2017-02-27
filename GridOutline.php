<?php
function myfunction($v)
{
  return($v);
}


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

function generateSlots($Ar, $val)
{
//base on "column" portion of 2x5 array, ie. the "5"
  $cCnt = count($Ar[0]);
//  $r = -1;
//  $c = -1;
  
//$flag = false;
//$flag2 = false;
  for ($i=0;$i<$cCnt;$i++){
	do{
      $reDo = false;

/*if (($i===0) & !$flag2 ){
	$r = 1;
	$c = 1;
	$flag2 = true;
} elseif (($i===2) & !$flag ) {
	$r = 1;
	$c = 1;
	$flag = true;
} else

 {*/
	  $r = mt_rand(0, 4);
	  $c = mt_rand(0, 4);
//  }
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

//		  print('Vals: r='.$r.' c='.$c.'   rKey - '.$rKey.'  cKey - '.$cKey.';               ');	  
		  
		  if ( ($r===$rKey) & ($c === $cKey ) ){
			  $reDo = true;
//			  print_r('REDO====REDO====REDO');
			  break;
	      }//if
		  
      }//for
  	  
	} while ($reDo);


//ok, it is a unique set of coords, assign them, continue until all array 
//slots filled
	$Ar[0][$i] = $r;
	$Ar[1][$i] = $c;
//    print_r('^^^^^Assigned^^^^^');
	
  }//FOR master
  return $Ar;
}

function assignAryToAry($Ar, $Mas)
{
//used to assign the random picks to the master 2dim array, and 
//to assign the resulting user input into the slave 2dim array
  $rCnt = count($Ar[0]);
  for ($i=0;$i<$rCnt;$i++){
	$Mas[$Ar[0][$i]][$Ar[1][$i]] = $i+1;
  }
  return $Mas;
}

function testForNumber($test1){
  try {
	if  (!is_int($test1)) {
		throw new Exception('Bad INPUT: NOT Integer');
	} elseif ( ($test1 < 0) || ($test1 > 4) ) {
		throw new Exception('Bad INPUT: Out of RANGE');
	}
  }
  catch(Exception $e) {
  echo 'Message: _________________________' .$e->getMessage();
  //some one is filling in the hidden fields with crap
  //email this has happened
  die('Hacker attack');
  }
	
}

function assignAnswer($Ar) {

  global $row1,$col1,$row2,$col2,$row3,$col3,$row4,$col4,$row5,$col5;

//get user input coords from the FORM, assign values to 2x5 array to assign
//to slave 2dim array. Check to make sure everything is integers and in range
//cave man brute force, don't know any function that helps
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
    	
  return $Ar;
}

$row1 = 0;
$col1 = 0;
$row2 = 1;
$col2 = 1;
$row3 = 2;
$col3 = 2;
$row4 = 3;
$col4 = 3;
$row5 = 4;
$col5 = 4;


$answer = array
  (
  array(11,12,13,14,15),
  array(21,22,23,24,25),
  );

$rndPick = array
  (
  array(11,12,13,14,15),
  array(21,22,23,24,25),
  );


$slave = array
  (
  array(0,1,2,3,4),
  array(11,12,13,14,15),
  array(22,23,24,25,26),
  array(33,34,35,36,37),
  array(44,45,46,47,48)
  );

$master = array
  (
  array(0,1,2,3,4),
  array(11,12,13,14,15),
  array(22,23,24,25,26),
  array(33,34,35,36,37),
  array(44,45,46,47,48)
  );

/*echo $slave[0][0].": In stock: ".$slave[0][1].", sold: ".$slave[0][2].".<br>";
*/

print_r(array_map("myfunction",$slave));
$slave = clearArray($slave, 0);
print_r(array_map("myfunction",$slave));

print_r(array_map("myfunction",$master));
$master = clearArray($master, 0);
print_r(array_map("myfunction",$master));

print_r(array_map("myfunction",$rndPick));
$rndPick = clearArray($rndPick, -1);
print_r(array_map("myfunction",$rndPick));

print_r(array_map("myfunction",$answer));
$answer = clearArray($answer, -1);
print_r(array_map("myfunction",$answer));


$rndPick = generateSlots($rndPick, -1);
print_r(array_map("myfunction",$rndPick));

$master = assignAryToAry($rndPick, $master);
print_r(array_map("myfunction",$master));

$answer = assignAnswer( $answer );
print_r(array_map("myfunction",$answer));

$slave = assignAryToAry($answer, $slave);
print_r(array_map("myfunction",$slave));



$slave = array
  (
  array(0,2,0,0,0),
  array(0,0,0,0,0),
  array(5,0,1,4,0),
  array(0,0,0,0,0),
  array(0,0,0,0,3)
  );

$master = array
  (
  array(0,2,0,0,0),
  array(0,0,0,0,0),
  array(5,0,1,4,0),
  array(0,0,0,0,0),
  array(0,0,0,0,3)
  );


function areEqual($M, $S)
{
//element by element comparison between master 2dim array and slave 2dim array
  $theyAre = true;
  $r = count($M);
  $c = count($M[0]);
  for ($i=0;$i<$r;$i++){
  	for ($j=0;$j<$c;$j++) {
  		if ( $M[$i][$j] <> $S[$i][$j] ) {
			$theyAre = false;
			break;
		}
  	}
  }
  return $theyAre;
}

if ( areEqual($master, $slave) ) {
	print_r('Well done, POST');
} else {
	print_r('Whoops, made an error');
}

?> 

