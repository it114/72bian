<?php
 
class categoryController extends  baseController { 
	
	public function index_action(){
		if(isset($_GET['cateId'])){
			$article_list = model('article')->get_list_by_cateid($_GET['cateId']);//echo model('article')->last_query();
			$this->assign('article_list',$article_list);
		}
		$this->display('/index/index');
	}
	
	
	
}
