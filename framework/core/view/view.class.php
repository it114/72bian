<?php
if (!defined('ACCESS')) exit('Access Denied!');
/**
 * View.class.php
 *
 * doitPHP视图类, 注:本类仅用于php格式的视图文件
 * @author tommy <streen003@gmail.com>
 * @copyright  Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: View.class.php 1.3 2012-01-18 23:20:00Z tommy $
 * @package framework
 * @since 1.0
 */

if (!defined('ACCESS')) {
    exit();
}

class view   {
    /**
     * 视图布局名称(layout)
     *
     * @var string
     */
    protected $layout;

    /**
     * 视图主题风格名称(theme)
     *
     * @var string
     */
    protected $theme;

    /**
     * 视图变量数组
     *
     * @var array
     */
    protected $_options;

    /**
     * 视图缓存文件
     *
     * @var string
     */
    protected $cacheFile;

    /**
     * 视图缓存重写开关
     *
     * @var boolean
     */
    protected $cacheStatus;

    
	protected $cache_dir;
	protected $layout_dir;
	protected $view_dir;
    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {

        //定义变量
        $this->_options     = array();
        $this->cacheStatus = false;
        $this->view_dir = ROOT_PATH.'apps'.DS.$_GET['app'].DS.'views'.DS;
		$this->cache_dir = ROOT_PATH.'data'.DS.'cache'.DS.'template'.DS;
		$this->layout_dir = ROOT_PATH.'public'.DS.'layout'.DS;
		$this->theme = 'default';
		$this->layout = 'default';
        return true;
    }
    
  	

    /**
     * 设置视图的主题
     *
     * 用于变化网页风格的视图选择. 注:该主题的视图文件存放于项目themes目录内,而非项目controller目录内
     * @access public
     * @param string $themeName 所要设置的网页模板主题名称
     * @return string    视图的主题名
     */
    public function setTheme($themeName = 'default') {
        return $this->theme = $themeName;
    }
	
    /**
     * 设置视图文件布局结构的文件名(layout)
     *
     * layout默认为:main, 注:本方法无法被$this->getViewFile()所调用
     * @access public
     * @param string $layoutName    所要设置的layout名称
     * @return string                layout名称
     */
    public function setLayout($layoutName = null) {
        return $this->layout = $layoutName;
    }
	
    /**
     * 分析视图文件
     *
     * 获取视图的路径,便于程序进行include操作. 注:本方法不支持视图布局结构(layout)
     * @access publice
     * @param string $fileName 视图文件名，注:名称中不带.php后缀。
     * @return void
     *
     * @example
     * 法一：
     * include $this->getViewFile('list');
     *
     * 法二：
     * include $this->getViewFile();    //即:当前controller视图目录下,当前action所对应的视图名称
     * 如当前action为demo，则相当于$this->getViewFile('demo');
     *
     * 法三：
     * include $this->getViewFile('xx_controller/xx_action');
     */
    public function getViewFile($fileName = null) {
		
        //参数分析
        if (is_null($fileName)) {
            $fileName = application::get_controller_name() . DS . application::get_action_name();
        } else {
            $fileName = (strpos($fileName, '/') !== false) ? $fileName : application::get_controller_name() . '/' . $fileName;
        }
		
        //分析视图文件所在的目录,是否视图使用了主题
        $viewFile  =   $this->view_dir . $fileName;
        $viewFile .= '.html';
		 
        //分析视图文件是否存在
        if (!is_file($viewFile)) {
            //Controller::halt('The view file:' . $viewFile . ' is not exists!');
           die('The view file:' . $viewFile . ' is not exists!');
        }
		
        return $viewFile;
    }
	
    /**
     * 分析视图缓存文件名
     *
     * @access protected
     * @param string $cacheId 缓存ID
     * @return string
     */
    protected function parseCacheFile($cacheId) {
        return $this->cache_dir . 'html/' .application::get_app_name().DS. application::get_controller_name() . DS . md5($cacheId) . '.action.html';
    }
	
    /**
     * 分析视图缓存文件是否需要重新创建
     *
     * @access public
     * @param string $cacheId 缓存ID
     * @param integer $lifetime 缓存文件生存周期, 默认为一年 ,单位：秒
     * @return boolean
     */
    public function cache($cacheId = null, $lifetime = null) {
		
        //参数分析
        if (is_null($cacheId)||''==$cacheId) {
            $cacheId = application::get_action_name();
        }
        
        if (is_null($lifetime)) {
            $lifetime = config('default_html_cache_time')?config('default_html_cache_time'):31536000;
        }
		
        //获取缓存文件
        $cacheFile = $this->parseCacheFile($cacheId);
        if (is_file($cacheFile) && (filemtime($cacheFile) + $lifetime >= time())) {
            include $cacheFile;
            exit();
        }
 		
        $this->cacheStatus = true;
        $this->cacheFile   = $cacheFile;
			
        return true;
    }


    /**
     * 创建缓存文件
     *
     * @access protected
     * @param string $cacheFile 缓存文件名
     * @param string $content 缓存文件内容
     * @return boolean
     */
    protected function createCacheFile($cacheFile, $content = null) {

        //参数分析
        if (!$cacheFile) {
            return false;
        }
        if (is_null($content)) {
            $content = '';
        }

        //分析缓存目录
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        } else if (!is_writable($cacheDir)) {
            chmod($cacheDir, 0777);
        }
		
        return file_put_contents($cacheFile, $content, LOCK_EX);
    }

    /**
     * 视图变量赋值操作
     *
     * @access public
     * @param mixted $keys 视图变量名
     * @param string $value 视图变量值
     * @return mixted
     */
    public function assign($keys, $value = null) {

        //参数分析
        if (!$keys) {
            return false;
        }
		
        //当$keys为数组时
        if (!is_array($keys)) {
            $this->_options[$keys] = $value;
        } else {
            foreach ($keys as $handle=>$lines) {
                $this->_options[$handle] = $lines;
            }
        }
		
        return true;
    }
	
    /**
     *  
     * 包括视图页面中所含有的挂件(widget), 视图布局结构(layout), 及render()所加载的视图片段等
     * @param unknown_type $fileName 视图文件名，支持/分割加载其他controller下面的view
     * @param unknown_type $lifetime 缓存时间，默认不缓存
     * @param unknown_type $force	   是否启用缓存，默认false	
     */
    public function display($fileName = null,$cache = false,$lifetime=null) {
    	if($cache) {
	    	//如果有缓存提前使用缓存
			$this->cache('',$lifetime);
    	}
        //分析视图文件路径
        $viewFile = $this->getViewFile($fileName);
		
        //模板变量赋值
        if (!empty($this->_options)) {
            extract($this->_options, EXTR_PREFIX_SAME, 'data');
            //清空不必要的内存占用
            $this->_options = array();
        }
        //获取当前视图($fileName)的内容
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
		if(true!==strpos('<!--NOLAYOUT@PAGE-->',$content)){//注意这里没有空格
	        //分析,加载,显示layout视图内容
	        //$layoutFile = (!empty($this->theme)) ? $this->view_dir . $this->theme . '/layout/' . $this->layout . '.php' : $this->view_dir . 'layout/' . $this->layout . '.php';
	        $layoutFile =   $this->layout_dir.'theme'.DS.$this->theme.DS. $this->layout . '.html';
	        //分析layout文件是否存在.
	        if (is_file($layoutFile)) {
	            ob_start();
	            include $layoutFile;
	            $content = ob_get_clean();
	        }
		}
		
        //显示视图文件内容
        echo $content;
		
        //当缓存重写开关开启时,创建缓存文件
        if ($this->cacheStatus == true) {
            $this->createCacheFile($this->cacheFile, $content);
        }
    }

    /**
     * 加载并显示视图片段文件内容
     *
     * 相当于include 代码片段，当$return为:true时返回代码代码片段内容,反之则显示代码片段内容
     * @access public
     * @param string  $fileName 视图片段文件名称
     * @param array   $_data     视图模板变量，注：数组型
     * @param boolean $return    视图内容是否为返回，当为true时为返回，为false时则为显示. 默认为:false
     * @return mixed
     */
    public function render($fileName, $_data = array(), $return = false) {

        //参数分析
        if (!$fileName) {
            return false;
        }
		
        //分析视图文件的路径
        $viewFile = $this->getViewFile($fileName);

        //模板变量赋值
        if (!empty($_data) && is_array($_data)) {
            extract($_data, EXTR_PREFIX_SAME, 'data');
            unset($_data);
        }
			
        //获取$fileName所对应的视图片段内容
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
		
        if (!$return) {
            echo $content;
        } else {
            return $content;
        }
    } 
	
    /**
     * 加载视图文件的挂件(widget)
     *
     * 加载挂件内容，一般用在视图内容中(view)
     * @access public
     * @param string  $widgetName 所要加载的widget名称,注没有后缀名
     * @param array   $params 参数. 如array('id'=>23)
     * @return boolean
     */
    public static function widget($widgetName, $params = null) {

        return Controller::widget($widgetName, $params);
    }
    
    /**
     * 析构函数
     *
     * @access public
     * @return void
     */
    public function __destruct() {
        //释放$_options数组
        if ($this->_options) {
            $this->_options = array();
        }
        $this->cacheStatus = false;
    }
}