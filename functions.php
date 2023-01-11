<?php
	require_once "connect.php";
	
	function cleanPosts($data){
		$data = trim($data);
		$data = htmlspecialchars($data);
		$data = mysqli_real_escape_string($GLOBALS['con'], $data);
		return $data;
		
	}

    function change_file_name(){
        $file_name = md5(date("Y h:i:s A"));
        return $file_name;
    }

    function get_file_format($file_name){
        $array = explode('.', $file_name);
        $ext = end($array);
        return $ext;
    }
	
	function upload_file($tmp_name,$file_name,$path){
		$response = [
			'status' => '',
			'message' => '',
			'data' => ''
		];
		$allowed_exts = ['jpg','jpeg','png','gif','webp'];
		$file_format = get_file_format($file_name);
		if(in_array($file_format, $allowed_exts)){
			$new_file_name =change_file_name().'.'.$file_format;
			$upload = move_uploaded_file($tmp_name,$path.$new_file_name);
			if($upload){
				$response['status'] = 'success';
				$response['message'] = 'File has been uploaded';
				$response['data'] = $new_file_name;
			}else {
				$response['status'] = 'error';
				$response['message'] = 'Server Problems';
			}
		}else {
			$response['status'] = 'error';
			$response['message'] = 'File Type Is Incorrect!!!';
		}
		
		return $response;
		
	}

    function date_difference($from_time){

        //$date = new DateTime("now", new DateTimeZone('Asia/Yerevan') );
        //$to_time = strtotime($date->format('Y-m-d H:i:s'));
        date_default_timezone_set("Asia/Yerevan");
        $to_time = strtotime(date("Y-m-d H:i:s"));
        $from_time = strtotime($from_time);
        $diff = abs($to_time - $from_time);

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24)
            / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 -
                $months*30*60*60*24)/ (60*60*24));
        $hours = floor(($diff - $years * 365*60*60*24
                - $months*30*60*60*24 - $days*60*60*24)
            / (60*60));
        $minutes = floor(($diff - $years * 365*60*60*24
                - $months*30*60*60*24 - $days*60*60*24
                - $hours*60*60)/ 60);
        $seconds = floor(($diff - $years * 365*60*60*24
            - $months*30*60*60*24 - $days*60*60*24
            - $hours*60*60 - $minutes*60));
		if($days >= 1){
			return $days.'d';
		}
        else if($hours < 24 && $hours>0){
            return $hours.'h '.$minutes.' min ago';
        }
        else if($hours < 1 && $minutes > 0 ){
            return $minutes.'min ago';
        }else {
            return 'just now';
        }


    }

?>