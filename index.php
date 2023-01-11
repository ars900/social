<?php
	session_start();
	require_once 'connect.php';
	require_once 'functions.php';
	$reg_errors = $log_errors = $log_success = '';
	$reg_success = '';
	$red_border = '';
	$empty_fields = [];
	if(isset($_COOKIE['log_email']) || isset($_COOKIE['log_pass'])){
		$log_email = $_COOKIE['log_email'];
		$log_pass = $_COOKIE['log_pass'];
		$result = mysqli_query($con, "SELECT * FROM users 
										WHERE email = '$log_email' AND
											  password = '$log_pass'
										");
		$_SESSION['user'] = mysqli_fetch_assoc($result);
		header('Location:home.php');
	}
	
	
	
	if(isset($_POST['reg_submit'])){
		foreach($_POST as $key=>$value){
			$$key = cleanPosts($value);
			if(empty($value)){
				array_push($empty_fields, $key);
			}
		}
		if(count($empty_fields) > 0){
			$reg_errors = '<div class="alert alert-warning" role="alert">
							  Please Fill All Fields!
							</div>';
		}
		else if($_POST['reg_pass'] != $_POST['reg_con_pass']){
			$reg_errors = '<div class="alert alert-warning" role="alert">
							  Passsword And Confirm Password Does not Match!!!
							</div>';
			$red_border = 'border-danger';
		}
			else{
				$result = mysqli_query($con, "SELECT email from users WHERE email = '$reg_email'");
				if(mysqli_num_rows($result) == 1){
					$reg_errors = '<div class="alert alert-warning" role="alert">
							  Email Already Exists
							</div>';
				}
					else{
						$new_pass = md5($reg_pass);
						$insert_users = mysqli_query($con, "INSERT INTO users 
										(first_name,last_name,email,password,gender) VALUES 
										('$first_name','$last_name','$reg_email','$new_pass','$gender')
									");
						if($insert_users){
							$reg_success = '<div class="alert alert-success" role="alert">
												You Are Registrated :)
											</div>';
						}
							else{
								$reg_errors = '<div class="alert alert-danger" role="alert">
												Something Went Wrong :(
											</div>';
							}
					}
				
			}
	}
	if(isset($_POST['log_submit'])){
		$log_email = $_POST['log_email'];
		$log_pass = $_POST['log_pass'];
		if(empty($log_email) || empty($log_pass)){
			$log_errors = '<div class="alert alert-danger" role="alert">
												Fill All Fields
											</div>';
		}
			else{
				$log_pass = md5($_POST['log_pass']);
				$result = mysqli_query($con, "SELECT * FROM users 
										WHERE email = '$log_email' AND
											  password = '$log_pass'
										");
				if(mysqli_num_rows($result) == 0){
					$log_errors = '<div class="alert alert-danger" role="alert">
														Wrong Email or Password
													</div>';
				}else{
					$_SESSION['user'] = mysqli_fetch_assoc($result);
					if(isset($_POST['remember'])){
						setcookie('log_email',$log_email,time()+3600*24);
						setcookie('log_pass',$log_pass,time()+3600*24);
					}
					$log_success = '<div class="alert alert-success" role="alert">
														You Are LogIn
													</div>';
					header("refresh:2; url=home.php");
					
				}
			}
		
		
	}
	
	
	
	
	
	
	$count = count($empty_fields);


?>



<html>
	<head>
		<link rel = "stylesheet" href = "css/bootstrap.min.css" />
		<link rel = "stylesheet" href = "css/style.css" />
	</head>
	<body>
		<div class = "container mt-5">
			<div class = "row">
				<div class = "col-lg-5 col-md-6 col-sm-12 border border-secondary">
					<div class = "col-lg-12 col-md-12 col-sm-12">
						<h2 class="display-5 text-center pt-3">Registration Form</h2>
					</div>
					<form action = "" method = "post">
						<div class="form-row row mt-5">
							<div class="col col-md-6">
							  <input type="text" class="form-control <?php if($count > 0 && in_array('first_name', $empty_fields)){ echo 'border-danger'; }?>" placeholder="First name" name = "first_name" value = "<?php if(isset($_POST['reg_submit']) && $first_name != ''){ echo $first_name; }?>">
							</div>
							<div class="col col-md-6">
							  <input type="text" class="form-control <?php if($count > 0 && in_array('last_name', $empty_fields)){ echo 'border-danger'; }?>" placeholder="Last name" name = "last_name" value = "<?php if(isset($_POST['reg_submit']) && $last_name != ''){ echo $last_name; }?>">
							</div>
						</div>
						<div class="form-group mt-3">
							<input type="email" name = "reg_email" class="form-control <?php if($count > 0 && in_array('reg_email', $empty_fields)){ echo 'border-danger'; }?>" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email" value = "<?php if(isset($_POST['reg_submit']) && $reg_email != ''){ echo $reg_email; }?>">
						</div>
						<div class = "row mt-3">
							<div class="form-group col-md-6">
								<input type="password" name = "reg_pass" class="form-control <?php echo $red_border; if($count > 0 && in_array('reg_pass', $empty_fields)){ echo 'border-danger'; }?>" id="exampleInputPassword1" placeholder="Password">
							</div>
							<div class="form-group col-md-6">
								<input type="password" name = "reg_con_pass" class="form-control <?php echo $red_border; if($count > 0 && in_array('reg_con_pass', $empty_fields)){ echo 'border-danger'; }?>" id="exampleInputPassword1" placeholder="Confirm Password">
							</div>
						</div>
						<div class="form-check form-check-inline">
						  <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="male">
						  <label class="form-check-label" for="inlineRadio1">Male</label>
						</div>
						<div class="form-check form-check-inline mt-3">
						  <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="female" checked>
						  <label class="form-check-label" for="inlineRadio2">FeMale</label>
						</div>
						<div class = "form-group mt-4">
							<button type="submit" name = "reg_submit" value = "reg_submit" class="btn btn-success btn-lg d-block w-100">Registration</button>
						</div>
						<div class = "form-group mt-4">
							<?= $reg_errors.$reg_success; ?>
						</div>
						
					</form>
				</div>
				<div class = "col-lg-2"></div>
				<div class = "col-lg-5 col-md-6 col-sm-12 border border-secondary">
					<div class = "col-lg-12 col-md-12 col-sm-12">
						<h2 class="display-5 text-center pt-3">Login Form</h2>
					</div>
					<form action = "" method = "post">
						<div class="form-group mt-3">
							<input type="email" name = "log_email" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp" placeholder="Email">
						</div>
						<div class="form-group mt-3">
							<input type="password" name = "log_pass" class="form-control" id="exampleInputEmail3" aria-describedby="emailHelp" placeholder="Password">
						</div>
						<div class="form-check mt-3">
							<input class="form-check-input" name = "remember" type="checkbox" value="login_remember" id="flexCheckDefault">
							<label class="form-check-label" for="flexCheckDefault">
								remember me?
							</label>
						</div>
						<div class = "form-group mt-4">
							<button type="submit" name = "log_submit" class="btn btn-primary btn-lg d-block w-100">Login</button>
						</div>
						<div class = "form-group mt-4">
							<?= $log_errors.$log_success; ?>
						</div>
					</form>
					
				</div>
			</div>
		</div>
	
	
	
	
		<script src = "js/jquery.js"></script>
		<script src = "js/bootstrap.min.js"></script>
	</body>
</html>