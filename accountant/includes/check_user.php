<?php
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
	// checks whether the variable is set or not

	// $_SESSION variable actually holds the data across all pages. 
	// also this var holds only one user information.
	$myfname = $_SESSION['first_name'];
	$mylname = $_SESSION['last_name'];
	$mygender = $_SESSION['gender'];
	$myemail = $_SESSION['email'];
	$myrole = $_SESSION['role'];
	$myphone = $_SESSION["contact"];
	

	
		$mytid = $_SESSION['myid'];
	
	if ($myrole == "accountant") {
		
	}else{
	header("location:../?display=YOU MUST BE A ACCOUNTANT TO ACCESS!!");	
	
	}
}else{
	header("location:../?display=YOU MUST LOGIN FIRST!!");
	
}

?>