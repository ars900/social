$(document).ready(function(){
	home_fixed_load();
	$(window).on('resize', function(){
		home_fixed_load();
	})
	
	
    setTimeout(function(){
        $('.for_hide').slideUp(500,function(){
            $(this).remove();
        });
    },2000)



function home_fixed_load(){
	
	var header_width = $('.header').outerWidth();
	var header_left = $('.header').offset().left;
	$('.home_fixed').each(function(){
		var width = $(this).parent().outerWidth();
		var left = $(this).parent().offset().left;
		$(this).css({
			'left':left+'px',
			'width':width+'px'
		})
	})
	$('.fixed-top').css({
		'width': header_width + 'px',
		'left': header_left + 'px'
	})
}

//--------------- Display Selected Image ----------//

$('#profile_image').change(function(){
    $('.profile_error_message').html('');
    var file = $('#profile_image').get(0).files[0];
    if (file) {
        var array = file.name.split('.');
        var format = array[array.length-1];
        if( format == 'jpg'  ||
            format == 'png'  ||
            format == 'jpeg' ||
            format == 'gif'  ||
            format == 'webp')
        {
            var fileReader = new FileReader();
            fileReader.readAsDataURL(file);
            fileReader.addEventListener("load", function () {
                $('.user_avatar_edit').attr('src', this.result);
            });
			$('.profile_error_message').html('');
        }else {
            $('.profile_error_message').append( '<div class="alert alert-danger" role="alert">\n' +
                                                '  File type is incorrect!\n' +
                                                '</div>');
        }

    }
})




//------------ Remove User Profile Image -----------//

$('.show_modal').click(function(){
    window.user_id = $(this).data('id');
})

$('.remove_avatar').click(function(){
    $.ajax({
        url: 'ajax/ajax.php',
        type: 'post',
        data: {user_id: user_id, key:'delete_profile_image'},
        success: function(res){
            $('.modal,.modal-backdrop').fadeOut(500, function(){
                $('.user_avatar_edit,.avatar_image').attr('src', res);
                $('.remove_avatar_btn_content').html('');
            });

        }
    })
})

//--------- Add Comment ------------//

$('.add_comment').click(function(){
    let post_id  = $(this).data('postid');
    let show_comments = $(this).closest('.row').find('.comment_area');
    let comment_textarea = $(this).parent().prev().children();
    let comment = $(this).closest('.add_comments_area').find('textarea').val();
    $.ajax({
        url: 'ajax/ajax.php',
        type: 'post',
        dataType: 'JSON',
        data: {'post_id':post_id, 'comment':comment, key:'add_post_comment'},
        success:function(res){
			if(res.status == 'success') {
                $(show_comments).append('<div class = "d-flex mt-2">' +
                    '<div class = "">' +
                    '   <img src = "uploads/avatars/' + res.data.avatar + '" class = "comment_profile rounded-circle">' +
                    '</div>' +
                    '<div class = "comment_bg ms-1">' +
                    '  <h5 class = "">' + res.data.first_name + ' ' + res.data.last_name + '</h5>' +
                    '  <p class = "">' + res.data.comment + '</p>' +
                    '  <p class = "comment_date fw-bold fst-italic text-end pt-1">' + res.data.created_at + '</p>' +
                    '</div>' +
                    '</div>');
                $(comment_textarea).val('');
                $(comment_textarea).attr('placeholder', 'Write a comment').removeClass('danger_placeholder border-danger');
            }
            else if(res.status == 'error'){

                $(comment_textarea).attr('placeholder', res.message).addClass('danger_placeholder border-danger');
            }
        }
    })
})

$('.like_icon').click(function(){
	var this_icon = $(this);
	var post_id = $(this).data('post_id');
	var show_like = $(this).parent().next();
	$.ajax({
		url:'ajax/ajax.php',
		type:'post',
		dataType:'json',
		data:{post_id:post_id,key:'for_like'},
		success:function(res){
			if(res.status == 'liked'){
				$(this_icon).addClass('like_icon_clicked')
			}
			else if(res.status == 'unlike') {
				$(this_icon).removeClass('like_icon_clicked')
			}
			$(show_like).text(res.num);
			
		}
	})
})

$('.del_post').click(function(){
	window.del_post_id = $(this).data('post_id');
	window.post = $(this).closest('.row');
	
})

$('.remove_post').click(function(){
	$.ajax({
		url:'ajax/ajax.php',
		type:'post',
		data:{post_id:del_post_id,key:'del_post'},
		success:function(res){
			if(res == 2){
				
				$('#post_delete').fadeOut(500, function(){
					$('#post_delete').modal('toggle');
					post.fadeOut(500,function(){
						post.remove();
					})
				})
			
			}
			
		}
	})
})

$('#search').keyup(function(){
	var value = $('#search').val();
	if(value != ''){
		$.ajax({
			url:'ajax/ajax.php',
			type:'post',
			dataType: 'json',
			data:{name:value,key:'for_search'},
			success:function(res){
				if(res.status == 'found'){
					$('#output').html('');
					for(i=0;i<=res.data.length;i++){
						if(res.data[i].avatar == null){
							var src = 'images/avatar/'+res.data[i].gender+'.jpg';
						}else{
							var src = 'uploads/avatars/'+res.data[i].avatar;
						}
						$('#output').append('<tr><td><a href="guest.php?user_id='+res.data[i].user_id+'"><img class="rounded-circle search_avatar" src = '+src+'><span>'+res.data[i].first_name+'</span><span> '+res.data[i].last_name+'</span></a></td></tr>');
					}
					
				}else{
					$('#output').html('<tr><td>User not found</td></tr>');
				}
			}
		})
	}
})

$(document).on('click', '.friend', function(){
	key = $(this).data('key');
	from = $(this).data('from_id');
	to = $(this).data('to_id');
	$.ajax({
		url:'ajax/ajax.php',
		type:'post',
		data:{from: from, to: to, key:key},
		success:function(res){
			$('.friend_div').html(res);
		}
	})
})

$('.seen').click(function(){
	$.ajax({
		url:'ajax/ajax.php',
		type:'post',
		dataType: 'json',
		data:{key:'seen'},
		success:function(res){
			$('.notification').addClass('show');
			for(i=0;i<res.length;i++){
				$('.notification').append('<div class="d-flex justify-content-between"><a href = "guest.php?user_id='+res[i].id+'"><img class = "rounded-circle search_avatar" src='+res[i].avatar+'><span>'+res[i].first_name+'<span> '+res[i].last_name+'</span></a></div>');
			}
		}
	})
})

})