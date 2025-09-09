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
	$myay = $_SESSION['ay'];
	$myrole = $_SESSION['role'];
	$myid = $_SESSION['myid'];
	$myclass = $_SESSION['myclass'];

	if ($myrole == "admin") {
		
	}else{
	header("location:../?display=YOU MUST BE A ADMIN TO ACCESS!!");	
	
	}
}else{
	echo "<script>
    alert('You must login first.');
    window.location.href='../?display=YOU MUST LOGIN FIRST';
    </script>";
	
}

?>