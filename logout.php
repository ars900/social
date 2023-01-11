<?php

	session_start();
	if(isset($_SESSION['user'])){
		unset($_SESSION['user']);
		if(isset($_COOKIE['log_email']) || isset($_COOKIE['log_pass'])){
			setcookie('log_email',$log_email,time()-3600*24);
			setcookie('log_pass',$log_pass,time()-3600*24);
		}
		header('Location:http://localhost/social/');
	}
		else{
			header('Location:404.php');
		}
?>