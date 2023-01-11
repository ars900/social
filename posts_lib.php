<?php 
	require_once 'connect.php';
	
	function all_posts($con){
		$res = mysqli_query($con, "SELECT * FROM posts ORDER BY id DESC LIMIT 4");
		$n = mysqli_num_rows($res);
  		
		$posts = array();
		
		for($i=0; $i<$n; $i++){
			$row = mysqli_fetch_assoc($res);
			$posts[] = $row;
		}
		return $posts;
	}
?>