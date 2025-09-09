<?php
// check whether the user is student or not.
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
	$myfname = $_SESSION['first_name'];
	$mylname = $_SESSION['last_name'];
	$mygender = $_SESSION['gender'];
	$myemail = $_SESSION['email'];
	$myay = $_SESSION['ay'];
	$myrole = $_SESSION['role'];
	$myid = $_SESSION['myid'];
	$myclass = $_SESSION['myclass'];
	$myfees = $_SESSION['myfees'];
	
	if ($myrole == "student") {
		
	}else{
	header("location:../?display=YOU MUST BE A STUDENT TO ACCESS THE EXAMS");	
	}
}else{
	echo "<script>
    alert('You must login first.');
    window.location.href='../?display=YOU MUST LOGIN FIRST';
    </script>";
}

?>