 <?php
    /*------------------ Delete Profile Image    ---------------------*/

    if(isset($_POST['key']) && $_POST['key'] == 'delete_profile_image'){
		$user_id = $_POST['user_id'];
        session_start();
        require_once "../connect.php";

        $profile_image = ($_SESSION['user']['avatar'] != null) ? $_SESSION['user']['avatar'] : null;
        if(file_exists('../uploads/avatars/'.$profile_image)){
            unlink('../uploads/avatars/'.$profile_image);
            $update = mysqli_query($con, "UPDATE users SET avatar = null WHERE id = '$user_id'");
            if($update){
                $_SESSION['user']['avatar'] = null;
                $src = 'images/avatar/'.$_SESSION['user']['gender'].'.jpg';
                echo $src;
            }
        }
    }

    /*------------------ Add Post Comments    ---------------------*/

    if(isset($_POST['key']) && $_POST['key'] == 'add_post_comment'){
        $res = [
            'status' => '',
            'message' => '',
            'data' => ''
        ];
        session_start();
        require_once "../connect.php";
        require_once "../functions.php";
        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user']['id'];
        $comment = cleanPosts($_POST['comment']);
        if(empty($comment)){
            $res = [
                'status' => 'error',
                'message' => 'This Field is required!!!',
                'data' => ''
            ];
        }else {
            $insert = mysqli_query($con, "INSERT INTO post_comments 
                                                                    (post_id,user_id,comment) VALUES
                                                                    ('$post_id','$user_id','$comment')
                                                    ");
            if ($insert) {
                $last_id = mysqli_insert_id($con);
                $result = mysqli_query($con, "SELECT * FROM post_comments 
                                                INNER JOIN users
                                                ON users.id = post_comments.user_id
                                                WHERE post_comments.id = '$last_id'
                                            ");
                $fetch = mysqli_fetch_assoc($result);
                unset($fetch['password']);
                $fetch['created_at'] = 'just now';
                $res = [
                    'status' => 'success',
                    'message' => 'Your comment has been added',
                    'data' => $fetch
                ];

            }
        }
        echo json_encode($res);
    }
	if(isset($_POST['key']) && $_POST['key'] == 'for_like'){
		session_start();
		require_once "../connect.php";
        require_once "../functions.php";
		$res = [
			'status' => '',
			'num' => ''
		];
		$post_id = $_POST['post_id'];
		$user_id = $_SESSION['user']['id'];
		$result = mysqli_query($con, "SELECT * FROM post_likes WHERE user_id = '$user_id' AND post_id = '$post_id'");
		if(mysqli_num_rows($result) == 0){
			$insert = mysqli_query($con, "INSERT INTO post_likes 
												(post_id,user_id) VALUES 
												('$post_id','$user_id')");
			if($insert){
				$result = mysqli_query($con, "SELECT * FROM post_likes WHERE post_id = '$post_id'");
				$num = mysqli_num_rows($result);
				$res = [
					'status' => 'liked',
					'num' => $num
				];
				echo json_encode($res);
			}
		}else {
			$delete = mysqli_query($con, "DELETE FROM post_likes WHERE user_id = '$user_id' AND post_id = '$post_id'");
			if($delete){
				$result = mysqli_query($con, "SELECT * FROM post_likes WHERE post_id = '$post_id'");
				$num = mysqli_num_rows($result);
				$res = [
					'status' => 'unlike',
					'num' => $num
				];
				echo json_encode($res);
			}
		}
		
	}
	
	 if(isset($_POST['key']) && $_POST['key'] == 'del_post'){
		session_start();
		require_once "../connect.php";
		require_once "../functions.php";
		$post_id = $_POST['post_id'];
		$del_comments = mysqli_query($con, "DELETE FROM post_comments WHERE post_id = '$post_id'");
		if($del_comments){
			$del_likes = mysqli_query($con, "DELETE FROM post_likes WHERE post_id = '$post_id'");
			if($del_likes){
				$result = mysqli_query($con, "SELECT * FROM posts WHERE id = '$post_id'");
				$fetch = mysqli_fetch_assoc($result);
				if($fetch['post_image'] != ''){
					if(file_exists('../uploads/posts/'.$fetch['post_image'])){
						unlink('../uploads/posts/'.$fetch['post_image']);
					}
					
				}
				$del_post = mysqli_query($con, "DELETE FROM posts WHERE id = '$post_id'");
				if($del_post){
					
					echo 2;
				}
			}
		}
	 }
	
	if(isset($_POST['key']) && $_POST['key'] == 'for_search'){
		session_start();
		require_once "../connect.php";
		require_once "../functions.php";
		$result = mysqli_query($con, "SELECT * FROM users WHERE first_name LIKE '%".$_POST['name']."%'");
		if(mysqli_num_rows($result)>0){
			$res = [
					'status' => 'found',
					'data' => []
			];
			
			while($row = mysqli_fetch_assoc($result)){
				$data = [
					'first_name' => $row['first_name'],
					'last_name' => $row['last_name'],
					'user_id' => $row['id'],
					'gender' => $row['gender'],
					'avatar' => $row['avatar'],
				];
			array_push($res['data'],$data);
			}
			
			echo json_encode($res);
		}else{
			$res = [
					'status' => 'not found',
					'data' => null
			];
			echo json_encode($res); 
		}
	}
	
	if(isset($_POST['key']) && $_POST['key'] == 'add_friend'){
		session_start();
		require_once "../connect.php";
		require_once "../functions.php";
		$from = $_POST['from'];
		$to = $_POST['to'];
		$insert = mysqli_query($con, "INSERT INTO friends (from_id, to_id) VALUES ('$from', '$to')");
		if($insert){
			echo  '<button  data-from_id = "'.$from.'" data-to_id ="'.$to.'" data-key = "remove_request" class="btn friend btn-secondary">Remove Request</button>';
		}
	}
	if(isset($_POST['key']) && $_POST['key'] == 'remove_request'){
		session_start();
		require_once "../connect.php";
		require_once "../functions.php";
		$from = $_POST['from'];
		$to = $_POST['to'];
		$delete = mysqli_query($con, "DELETE FROM friends WHERE from_id = '$from' ANd to_id  = '$to'");
		if($delete){
			echo '<button  data-from_id = "'.$from.'" data-to_id ="'.$to.'" data-key = "add_friend" class="btn friend btn-success">Add friend</button>';
		}
	}
	
	if(isset($_POST['key']) && $_POST['key'] == 'seen'){
		session_start();
		require_once "../connect.php";
		require_once "../functions.php";
		$data = [];
		$user_id = $_SESSION['user']['id'];
		$result = mysqli_query($con, "SELECT * FROM friends WHERE to_id = '$user_id' AND seen = 0");
		while($fetch = mysqli_fetch_assoc($result)){
			$from_id = $fetch['from_id'];
			$query = mysqli_query($con, "SELECT * FROM users WHERE id = '$from_id'");
			$fetch_user = mysqli_fetch_assoc($query);
			if($fetch_user['avatar'] == null){
				$fetch_user['avatar'] = 'images/avatar/'.$fetch_user['gender'].'.jpg';
			}else {
				$fetch_user['avatar'] = 'uploads/avatars/'.$fetch_user['avatar'];
			}
			array_push($data,$fetch_user);
		}
		$update = mysqli_query($con, "UPDATE friends SET seen = 1 WHERE to_id = '$user_id'");
		if($update){
			echo json_encode($data);
		}
	}
?>