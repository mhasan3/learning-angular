<?php

// print_r(json_encode($_POST['data']) );

include('config.php');



switch($_GET['action']) {

	case 'add_info' :
	print_r(json_encode($_POST['data']) );
	doSubmit();
	break;

	case 'get_info' :
	get_info();
	break;
}

function doSubmit() {
	$data = json_decode(json_encode($_POST['data']),true);
	//print_r($data );
	$name = $data['username']; 
	$email = $data['email'];
	$password = $data['password'];
	$password = md5($password);
	//print_r($email);

	$result = mysql_query("SELECT * FROM info WHERE name='$name'");
	$exists = mysql_query("SELECT * FROM info WHERE email='$email'");
	if(mysql_num_rows($result) == 1)
	{ 
		
		echo "  user exist try another one  ";
	}

	
	else if(mysql_num_rows($exists) == 1)
	{
		
		echo "  email exist try another one  ";
	}

	else
	{


		$qry = 'INSERT INTO info (name,email,password) values ("' . $name . '","' . $email . '","' .$password . '")';
 
		$qry_res = mysql_query($qry);
		print_r($qry_res);
		if ($qry_res) {
			$arr = array('msg' => "User Added Successfully!!!", 'error' => '');
			$jsn = json_encode($arr);
			print_r($jsn);
		} 
		else {
			$arr = array('msg' => "", 'error' => 'Error In inserting record');
			$jsn = json_encode($arr);
			print_r($jsn);
			}
		}
	}


function get_info() { 
	$data = json_decode(json_encode($_GET['data']),true);
	$name = $data['username']; 

	print_r($name);
	//echo 'name';
	$qry = mysql_query('SELECT * from info WHERE name = "abc" ');
	$data = array();
while($rows = mysql_fetch_array($qry))
{
$data[] = array(
	"id" => $rows['id'],
	"user" => $rows['name'],
	"email" => $rows['email'],
	//"password" => $rows['password'],
);
}

//print_r($data);

//print_r(json_encode($data));
return json_encode($data); 
}
 
?>