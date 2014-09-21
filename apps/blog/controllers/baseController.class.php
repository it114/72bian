<?php
class baseController extends  controller { 
	
	public function _before_action(){
		$nav = $this->render_nav();
		$this->assign('nav',$nav);
		
		$siderbar = $this->render_sidebar();
		$this->assign('siderbar',$siderbar);
	}
	
	public function render_nav(){
		$category_list = model('category')->get_category_list();
		return $this->render('/nav',array('category_list'=>$category_list),true);
	}
	
	public function render_sidebar(){
 		$options_model = model('userOption');
 		$user_id = session::get('user_id'); 
 		$options =$options_model->get_user_option($user_id);  print_r($options);
 		if($options){
 			$about_blog = $options['about_field'];
 		}else {
 			$about_blog = '这个人很懒哦，暂无博客介绍';
 		}	
 		$tag_list =  model('tag')->get_tag_list();  
		$hot_article_list = model('article')->get_hot_article_list();
		$data = array('about_blog'=>$about_blog,'tag_list'=>$tag_list,'hot_article_list'=>$hot_article_list);
		
		return $this->render('/sidebar',$data,true);
	}
	
}
