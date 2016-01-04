<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * cPlayer --A beautiful(maybe not) and clean(maybe not also) WEB Music Player by HTML5.
 * 
 * @package cPlayer
 * @author journey.ad
 * @version 0.2
 * @link https://imjad.cn
 */
class cPlayer_Plugin implements Typecho_Plugin_Interface
{
	protected static $playerID = 0;
	/**
	 * 激活插件方法,如果激活失败,直接抛出异常
	 * 
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function activate()
	{
		Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = array('cPlayer_Plugin','playerfilter');
		Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('cPlayer_Plugin','playerparse');
		Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('cPlayer_Plugin','playerparse');
		Typecho_Plugin::factory('Widget_Archive')->header = array('cPlayer_Plugin','header');
		Typecho_Plugin::factory('Widget_Archive')->footer = array('cPlayer_Plugin','footer');
		Typecho_Plugin::factory('admin/write-post.php')->bottom = array('cPlayer_Plugin', 'button');
		Typecho_Plugin::factory('admin/write-page.php')->bottom = array('cPlayer_Plugin', 'button');
	}
	
	/**
	 * 禁用插件方法,如果禁用失败,直接抛出异常
	 * 
	 * @static
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function deactivate() {
		$files = glob('usr/plugins/cPlayer/cache/*');
		foreach($files as $file){
			if (is_file($file)){
				unlink($file);
			}
		}
	}
	
	/**
	 * 获取插件配置面板
	 * 
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form 配置面板
	 * @return void
	 */
	public static function config(Typecho_Widget_Helper_Form $form)
	{
		if (isset($_GET['action']) && $_GET['action'] == 'deletefile')
		self::deletefile();
		$cache = new Typecho_Widget_Helper_Form_Element_Radio('cache',
			array('false'=>_t('否')),'false',_t('清空缓存'),_t('必要时可以使用'));
		$form->addInput($cache);
		$submit = new Typecho_Widget_Helper_Form_Element_Submit();
		$submit->value(_t('清空歌词缓存'));
		$submit->setAttribute('style','position:relative;');
		$submit->input->setAttribute('style','position:absolute;bottom:37px;');
		$submit->input->setAttribute('class','btn btn-s btn-warn btn-operate');
		$submit->input->setAttribute('formaction',Typecho_Common::url('/options-plugin.php?config=cPlayer&action=deletefile',Helper::options()->adminUrl));
		$form->addItem($submit);
	}
	/**
	 * 缓存清空
	 *
	 * @access private
	 * @return void
	 */
	private function deletefile()
	{
		$path = __TYPECHO_ROOT_DIR__ .'/usr/plugins/cPlayer/cache/';
		foreach (glob($path.'*') as $filename) {
			unlink($filename);
		}
		Typecho_Widget::widget('Widget_Notice')->set(_t('歌词缓存已清空!'),NULL,'success');
		Typecho_Response::getInstance()->goBack();
	}
	/**
	 * 个人用户的配置面板
	 * 
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form
	 * @return void
	 */
	public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
	/**
	 * 头部css挂载
	 * 
	 * @return void
	 */
    public static function header(){
	    echo "\n<link href=\"" . Helper::options()->pluginUrl . "/cPlayer/player.css\" rel=\"stylesheet\">\n";
    }
	/**
	 * 尾部js
	 *
	 *
	 * @return void
	 */
    public static function footer(){
	    echo "\n<script src=\"" . Helper::options()->pluginUrl . "/cPlayer/player.default.js\"></script>\n";
    }
	/**
	 * MD兼容性过滤
	 * 
	 * @param array $value
	 * @return array
	 */
	public static function playerfilter($value)
	{
		//屏蔽自动链接
		if ($value['isMarkdown']) {
			$value['text'] = preg_replace('/(?!<div>)\[(mp3)](.*?)\[\/\\1](?!<\/div>)/is','<div>[mp3]\\2[/mp3]</div>',$value['text']);
			//兼容JWPlayer
			$value['text'] = preg_replace('/(?!<div>)<(jw)>(.*?)<\/\\1>(?!<\/div>)/is','<div><jw>\\2</jw></div>',$value['text']);
		}
		return $value;
	}
	/* 插件实现方法 */
	public static function button(){
		?>
		<script>
			$(document).ready(function(){
				if($('#wmd-button-row').length !== 0){
					$('#wmd-button-row').append('<li class="wmd-button" id="wmd-music-button" title="音乐 - Alt+M"><span style="background: none;font-size: large;text-align: center;color: #999999;font-family: serif;">M</span></li>');
					$('#wmd-music-button').click(function(){
						var rs = "[mp3]歌曲网址|skin=white|lrc=lrc文件地址\n[lrc]\n[00:00.00]...\n[00:00.00]...\n...\n[02:43.35]...\n[/lrc]\n[/mp3]";
						grin(rs);
					})
				}

				function grin(tag) {
					var myField;
					if (document.getElementById('text') && document.getElementById('text').type == 'textarea') {
						myField = document.getElementById('text');
					} else {
						return false;
					}
					if (document.selection) {
						myField.focus();
						sel = document.selection.createRange();
						sel.text = tag;
						myField.focus();
					}
					else if (myField.selectionStart || myField.selectionStart == '0') {
						var startPos = myField.selectionStart;
						var endPos = myField.selectionEnd;
						var cursorPos = startPos;
						myField.value = myField.value.substring(0, startPos)
						+ tag
						+ myField.value.substring(endPos, myField.value.length);
						cursorPos += tag.length;
						myField.focus();
						myField.selectionStart = cursorPos;
						myField.selectionEnd = cursorPos;
					} else {
						myField.value += tag;
						myField.focus();
					}
				}

				$('body').on('keydown',function(a){
					if(a.altKey && a.keyCode == "77"){
						$('#wmd-music-button').click();
					}
				});
			});
</script>
<?php
}
	/**
	 * 内容标签替换
	 * 
	 * @param string $content
	 * @return string
	 */
	public static function playerparse($content,$widget,$lastResult)
	{
		$content = empty($lastResult) ? $content : $lastResult;
		if ($widget instanceof Widget_Archive) {
			$content = preg_replace_callback('/\[(mp3)](.*?)\[\/\\1]/si',array('cPlayer_Plugin','parseCallback'),$content);
		}
		return $content;
	}	
	/**
	 * 参数回调解析
	 * 
	 * @param array $matches
	 * @return string
	 */
	public static function parseCallback($matches)
	{
		$all = $matches[2];
		if( preg_match('/\[(lrc)](.*?)\[\/\\1]/si', $all ,$lyrics) ){
			$lyric = $lyrics[2];
		}
		$all = preg_replace('/\[(lrc)](.*?)\[\/\\1]/si', '', $all);
		$atts = explode('|',$all);
		
		//mp3链接
		$files = array_shift($atts);
		
		//其他参数
		$data = array();
		foreach ($atts as $att) {
			$pair = explode('=',$att);
			$data[trim($pair[0])] = trim($pair[1]);
		}
		//解析歌词，如果没有[lrc][/lrc]文本歌词但是有lrc的url的话直接从url中读取并缓存
		if(isset($data['lrc']) && !$lyric){
			if($c = self::getlrc($data['lrc']))
				$lyric = $c;
		}
		$data['lyric'] = $lyric;

		return self::getPlayer($files,$data);
	}

	/**
	 * 输出播放器实例
	 * 
	 * @param string $source
	 * @param string $lyric
	 * @param string $skin
	 * @return string
	 */
	public static function getPlayer($source,$playerOptions = array())
	{
		//播放器id
		$id = self::$playerID;
		self::$playerID++;
		
		//输出代码
		$playerCode =  '<div id="player'.$id.'" class="player '.$playerOptions['skin'].'" src="'.$source.'">';
		if ($playerOptions['lyric']) {
			$playerCode .= $playerOptions['lyric'];
		}
		$playerCode .= "</div>\n";

		return $playerCode;
	}
	//获取歌词函数
	private static function getlrc($url){
		//存放歌词缓存文件夹
		$cachedir = dirname(__FILE__)."/cache";
		$key = 'lrc_'.md5($url);
		if($g = self::cache_get($key)){
			if(!isset($g[0])) return false;
			return $g[0];
		}else{
			//缓存不存在时用url获取并存入缓存
			$lyric = self::fetch_url($url);
			//用array包裹这个变量就不会判断错误啦
			self::cache_set($key,array($lyric));
			return $lyric;
		}
		
	}
	//简单的文件缓存
	private static function cache_set($key, $value){
		$cachedir = dirname(__FILE__)."/cache";
		$fp = fopen($cachedir.'/'.$key,"w+");
		$status = fwrite($fp,serialize($value));
		fclose($fp);
		return $status;
	}
	//简单的文件缓存
	private static function cache_get($key){
		$cachedir = dirname(__FILE__)."/cache";
		//找到缓存直接读取缓存目录的文件
		if(file_exists($cachedir.'/'.$key)){
			return unserialize(file_get_contents($cachedir.'/'.$key));
		}else{
			return false;
		}
	}
	//加载文件
	private static function fetch_url($url){
		if(function_exists('curl_init')){
			$ch = curl_init($url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; 
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$output = curl_exec($ch);
			$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			if ($httpCode != 200) return false;
			return $output;
		}else{
			return false;
		}
	}
}