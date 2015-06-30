<?php
 
/****** Database Details *********/
 
$host = "localhost"; 
$user = "root"; 
$pass = "1"; 
$database = "myDB";
$con = mysql_connect($host,$user,$pass);

 
if (!$con) {
die('Could not connect: ' . mysql_error());
}
 
echo 'Connected successfully'; 
 
$db=mysql_select_db($database,$con);

if(!$db) {
	die('could not connect to ur database' . mysql_error());
}

echo 'database connected successfully';

 
/*******************************/
 
?>