<?php 
if (!defined('ACCESS')) exit('Access Denied!');
/**
 * file 缓存
 */
class cacheFileHandler extends base{
	public $default_expire = 31536000;
	public $default_folder = 1024;
	public $default_dir;
	
	public function __construct(){
		$this->default_dir = ROOT_PATH.'data'.DS.'filecache'.DS;
		if(!is_dir($this->default_dir)){
			if(!mkdir($this->default_dir,0777,true)){
				echo 'Cache file is not permission!';exit;
				return false;
			}
		}
	}
	
	public function getDir($key){
		$hashKey = $this->hashKey($key);
        $dir = $this->hashDir($hashKey);
        return $dir;
	}
	
	/**
	 * 生成路径
	 *
	 * @param unknown_type $file_num
	 */
	function hashDir($hashKey,$folder_num=1024,$m=2){
		$dir = $this->default_dir;
		for ($i=1;$i<=$m;$i++){
			$dir .= "/".ceil(fmod($hashKey,pow($folder_num,$i)));
			if(!is_dir($dir)){
				mkdir($dir);
			}
		}
		return $dir;
	}
	
    function hashKey($key){
    	$hashCode = 0;
    	for ($i = 0, $len = strlen($key); $i < $len; $i++) {
        	$hashCode = (int)(($hashCode*33)+ord($key[$i])) & 0x7fffffff;
    	}
    	return $hashCode;
	}
	
	public function set($key,$val,$expire='',$compress=0,$suffix='.php'){
		$dir = $this->getDir($key);
		$filename = $dir.DS.md5($key).$suffix;
		$expire = $expire ? $expire : $this->default_expire;
		$data = serialize(array(
					'expire'=>time()+$expire,
					'val'=>$val,
					)
				);
		if($compress) $data = gzcompress($data, 9);
		if(file_put_contents($filename, '<?php exit();/*'.$data.'*/?>')){
			return true;
		}else{
			return false;
		}
	}
	
	public function setMulti($items,$expire='',$compress=0,$suffix='.php'){
		if(!is_array($items)) return false;
		foreach($items as $key=>$val){
			$this->set($key,$val,$expire,$compress,$suffix);
		}
	}
	
	public function get($key,$compress=0,$suffix='.php'){ 
		$dir = $this->getDir($key);
		$filename = $dir.DS.md5($key).$suffix;
		if(!file_exists($filename)) return false;
		$data = substr(file_get_contents($filename),15,-4);
		if($compress) $data = gzuncompress($data);
		$data = unserialize($data);
		if($data['expire'] < time()) return false;
		return $data['val'];
	}
	
	public function getMulti($keys){
		if(!is_array($keys)) return false;
		$vals = array();
		foreach($keys as $key){
			$vals[$key] = $this->get($key);
		}
		return $vals;
	}
	
	public function delete($key,$suffix='.php'){
		$dir = $this->getDir($key);
		$filename = $dir.DS.md5($key).$suffix;
		return @unlink($filename);
	}
	
	
}?>