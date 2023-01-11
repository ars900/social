    
<?php
	
	require_once 'connect.php';
	require 'header.php';
	require_once 'functions.php';
	$file_name = null;
	$has_error = false;
	$error_message = '';
	if(isset($_POST['add_post'])){
		if(empty($_POST['post_text'])){
			$post_text = '';
			$error_message = '<div class="alert alert-danger" role="alert">
                        Please, Fill Post Text!!!
                     </div>';
		}else {
			$post_text = cleanPosts($_POST['post_text']);
			if(!empty($_FILES['post_image']['name'])){
				$upload = upload_file($_FILES['post_image']['tmp_name'],$_FILES['post_image']['name'],'uploads/posts/');
				if($upload['status'] == 'error'){
					$error_message = '<div class="alert alert-danger" role="alert">
                        '.$upload['message'].'
                     </div>';
				}
				else if($upload['status'] == 'success'){
					$file_name = $upload['data'];
					$has_error = true;
				}
			}else{
				$has_error = true;
			}
			if($has_error == true){
				$insert = mysqli_query($con, "INSERT INTO posts 
											(user_id,post_text,post_image) VALUES 
											('$user_id','$post_text','$file_name')
						");
				$post_text = '';
			}
			
		}
	}
    //$post_result = mysqli_query($con,"SELECT * FROM posts ORDER BY id DESC LIMIT 5");


	
	$result = mysqli_query($con, "SELECT posts.*, post_comments.*, post_comments.id as comment_id,post_comments.created_at as comment_created_at, 
                posts.id as post_id,posts.created_at as post_created_at, 
                
                post_user.first_name as p_first_name, post_user.last_name as p_last_name,post_user.avatar as p_avatar,
				post_user.id as p_user_id,
                comment_user.first_name as c_first_name, comment_user.last_name as c_last_name, comment_user.avatar as c_avatar
                
                
                    FROM (SELECT * FROM `posts`) as posts
                    
                        LEFT JOIN post_comments 
                        ON posts.id = post_comments.post_id
                        
                        LEFT JOIN users as comment_user
                        ON comment_user.id = post_comments.user_id                                                                                                                            

                        LEFT JOIN users as post_user
                        ON post_user.id = posts.user_id  

                ORDER BY posts.id DESC");


    $data = [
                'posts' => [],
                'for_check' => '',
                'post_like_user' => ''
    ];
    $index = -1;
    foreach ($result as $key=>$value){

        if($data['for_check'] == $value['post_id']){

            $comment_arr = [
                'first_name' => $value['c_first_name'],
                'last_name' => $value['c_last_name'],
                'comment' => $value['comment'],
                'comment_created_at' => $value['comment_created_at'],
                'avatar' => $value['c_avatar'],
            ];
           

            array_push($data['posts'][$index]['comments'],$comment_arr);
			

        }else {

            $index++;
			

            $comment_arr = [
                'first_name' => $value['c_first_name'],
                'last_name' => $value['c_last_name'],
                'comment' => $value['comment'],
                'comment_created_at' => $value['comment_created_at'],
                'avatar' => $value['c_avatar'],
            ];
           
            array_push($data['posts'],$value);

            $data['posts'][$index]['comments'] = [$comment_arr];
            unset($data['posts'][$index]['comment']);
            $data['for_check'] = $value['post_id'];
			
			
            
        }

    }
	
   


?>
	<div class = "container">
		<div class = "row">
			<div class = "col-lg-3">
				<div class = "home_fixed"></div>
			</div>
			<div class = "col-lg-6">
				<form action = "" method = "post" enctype = "multipart/form-data">
					<div class = "row mt-2">	
						<div class = "col-lg-3">
							<div class="">
							  <input class="form-control" type="file" name = "post_image" id="formFile" >
							</div>
						</div>
						<div class = "col-lg-6">
							<div class="input-group">
							  <input type="text" placeholder = "What's Your Mind <?= $_SESSION['user']['first_name']; ?>" value = "<?= (isset($_POST['add_post'])) ? $post_text : ''; ?>"name = "post_text" class="form-control <?= (isset($_POST['add_post']) && $_POST['post_text'] == '') ? 'border-danger' : ''; ?>">
							</div>
						</div>
						<div class = "col-lg-3">
							<button type="submit" class="btn btn-success d-block w-100" name = "add_post">Add Post</button>
						</div>
					</div>
					<div class = "row mt-2">
						<div class = "col-lg-12">
							<?= $error_message ?>
						</div>
					</div>
				</form>
				<?php
				    foreach ($data['posts'] as $key => $value){
				 ?>
					<div class = "row mt-1">
						<div class = "d-flex">
							<div class = "">
								<img src = "<?= "uploads/avatars/".$value['p_avatar']; ?>" class = "post_avatar rounded-circle">
							</div>
							<div class = "ps-3 my-auto">
								<h3 class = "">
									<?= $value['p_first_name'].' '.$value['p_last_name']; ?>
								</h3>
							</div>
							<div class = "my-aut ps-2 align-middle">
								<span class = "post_date fw-light d-block"><?=date_difference($value['post_created_at']); ?></span>
							</div>
							<?php if($_SESSION['user']['id'] == $value['p_user_id']){ ?>
								<div class = "ms-auto my-auto pt-3 pe-2 post_settings dropdown position-relative">
									<i class="fa-solid fa-ellipsis" type = "button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></i>
									<ul class="dropdown-menu post_drop_down" aria-labelledby="dropdownMenuButton1">
										<li><a data-post_id = "<?=$value['post_id'];?>"class="dropdown-item del_post" href = "#" data-bs-toggle="modal" data-bs-target="#post_delete">Delete Post</a></li>
									   
									 </ul>
								</div>
							<?php } ?>
						</div>
						<div class = "post_text mt-3">
							<h4 class = "fw-normal fs-6"><?=$value['post_text']; ?></h4>
						</div>
						<?php if($value['post_image'] != null){ ?>
							<div class = "post_image mt-2">
								<img src = "uploads/posts/<?=$value['post_image']; ?>" class = "rounded w-100">
							</div>
						<?php } ?>
						<div class = "like_area text-start mt-2 ps-3">
							<?php
								$post_id = $value['post_id'];
								$ids = [];
								$post_likes = mysqli_query($con, "SELECT * FROM post_likes WHERE post_id = '$post_id'");
								while($fetch = mysqli_fetch_assoc($post_likes)){
									array_push($ids, $fetch['user_id']);
								}
								$num_likes = mysqli_num_rows($post_likes);
							?>
							<span><i data-post_id = "<?=$value['post_id']; ?>"class="fa-solid fa-thumbs-up like_icon like_icon_css <?= (in_array($_SESSION['user']['id'], $ids)) ? 'like_icon_clicked' : '';?>"></i></span>
							<span><?=$num_likes; ?></span>
						</div>
						<div class = "comment_area mt-3">
                            <?php for($i = 0;$i < count($value['comments']); $i++){ ?>
                                <?php if($value['comments'][$i]['comment'] != null){ ?>
                                    <div class = "d-flex mt-2">
                                        <div class = "">
                                            <img src = "uploads/avatars/<?=$value['comments'][$i]['avatar']; ?>" class = "comment_profile rounded-circle">
                                        </div>

                                        <div class = "comment_bg ms-1">
                                            <h5 class = ""><?=$value['comments'][$i]['first_name'].' '.$value['comments'][$i]['last_name'] ?></h5>

                                                <p class = ""><?=$value['comments'][$i]['comment']; ?></p>

                                            <p class = "comment_date fw-bold fst-italic text-end pt-1"><span><?=date_difference($value['comments'][$i]['comment_created_at']); ?></span></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
						<div class = "add_comments_area">
							<div class="my-3">
							  <textarea class="form-control" id="comment_area" rows="1" placeholder = "Write a comment"></textarea>
							</div>
							<div class = "text-end">
								<button type="button" class="btn btn-secondary btn-sm add_comment" data-postid = "<?=$value['post_id']; ?>">Add Comment</button>
							</div>
						</div>
						<hr class="bg-secondary border-5 border-top  my-3">
					</div>
				<?php } ?>
			</div>
			<div class = "col-lg-3">
				<div class = "home_fixed"></div>
			</div>
		</div>
	</div>




<?php
	require 'footer.php';
?>
