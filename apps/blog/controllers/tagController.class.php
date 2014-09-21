<?php
 
class tagController extends baseController {
	
	public function tagarticle_action(){
		if(isset($_GET['tagid'])){
		 	$tag = model('tag')->get_by_id($_GET['tagid']);
		 	$this->assign('tag',$tag);
		 	if($tag){
		 		$article_ids = model('tag')->get_article_ids_by_tag_id($_GET['tagid']);
		 		$article_list = array(); 
		 		foreach ($article_ids as $v){
		 			$article_list[] = model('article')->get_by_id($v['blogId']);
		 		}
		 		$this->assign('article_list',$article_list); 
		 	} else {
		 		response::response_404();
		 	}
		}
		$this->display('index');
	}
	
	
}