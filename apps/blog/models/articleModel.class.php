<?php

class articleModel extends model {
	
	public function get_hot_article_list(){
		$db = self::__instance();
		$article_list = $db->select($this->get_table_name('blog_article'),'*',array('state'=>1,'ORDER'=>'countView,countReply,countFavorite'));
		 
		$article_list = $this->filter_article($article_list);
		return $article_list;
	}
	
	public function get_by_id($id){
		$db = self::__instance();
		$v =  $db->get($this->get_table_name('blog_article'),'*',array('id'=>$id));
		return $this->article_item($v);
	}
	
	public function get_list_by_cateid($cate_id){
		$db = self::__instance();
		$article_list = $db->select($this->get_table_name('blog_article'),'*',array('state'=>1,'ORDER'=>'countView,countReply,countFavorite','AND'=>array('cateId'=>$cate_id)));
		return  $this->filter_article($article_list);
	}
	
	private function filter_article($article_list){ 
		foreach($article_list as &$v){ 
			$v = $this->article_item($v);
		}
		
		return $article_list;
	}
	
	private function article_item($v){
		$v['createTime'] = date('Y-m-d',$v['createTime']);
		$v['summary']	=  mc_cut_str($v['summary'], 180);
		//TODO,如何保证查询比较快
		$v['authorName'] = '高效查询';
		$v['tags'] = '科技|创业|资讯';
		if($v['photo']){
			$arr = explode(',', $v['photo']);
			if(is_array($arr)){
				$v['photo'] = $arr[0];
			}
		}
		return $v;
	}
	
}