<?php

class articleController  extends   baseController { 
	
	public function index_action(){
		$siderbar = $this->render_sidebar(); 
		$this->assign('siderbar',$siderbar);  
		$this->display();
	}
	
	public function details_action(){ 
		if(isset($_GET['id'])){
			$article = model('article')->get_by_id($_GET['id']);
			if($article){
				$this->assign('article',$article);
			}
			$this->display();
		}else {
			response::response_404();
		}
		
	}
	
	
}
