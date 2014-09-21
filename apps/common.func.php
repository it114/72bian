<?php
//用户是否处于登陆状态，否则跳转
function check_login(){
	$user_name = session::get('user_name');
	if(empty($user_name)){
		response::show_message('未登录','info',3,url('login','common','user'));
	}
}







?>