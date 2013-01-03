<?php
#用来获取国内主流平台的flashbo播放地址#
#作者 aaron.tang#
#时间 2013/1/3#
class php_get_flash{
	private $url;
	private $site;
	private $sitelist=array('youku','ku6','tudou');
	function __construct($url=''){
		$this->url=$url;
	}
	#设置URL
	function set_url($url){
		$this->url=$url;
	}
	#获得URL
	function get_url(){
		return $this->url;
	}
	#设置网站
	function set_site($site){
		$this->site=$site;
	} 
	#获得网站
	function get_site(){
		return $this->site;
	}
	#定义内部方法
	function _curl_get_file_contents($URL)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
		if ($contents) return $contents;
		else return FALSE;
	}
	function _get_domain($url){
		if (preg_match("/^http\:\/\/\w+\.youku\.com.*/i",trim($url))){
			$this->site='youku';
		}else if (preg_match("/^http\:\/\/\w+\.tudou\.com.*/i",trim($url))){
			$this->site='tudou';
		}else if (preg_match("/^http\:\/\/\w+\.ku6\.com.*/i",trim($url))){
			$this->site='ku6';
		}

	}
	#获得连接的方法
	function doparse(){
		$this->_get_domain($this->url);
		$resultvod=array();
		if($this->url&&in_array($this->site,$this->sitelist)){
			switch($this->site){
				case 'youku':
					$data = $this->_curl_get_file_contents($this->url);
					preg_match_all('/\<a title="转发到百度贴吧"(.*)" target="_blank"\>\<i class="ico__baidu"\>\<\/i\>百度贴吧\<\/a\>/isU',$data,$src);
					preg_match_all('/\<input type="text" class="form_input form_input_s" id="link2" value="(.*)\>/isU',$data,$swf);
					$swf = $swf[0][0];
					$resultvod['swf']=str_replace('" >','',str_replace('<input type="text" class="form_input form_input_s" id="link2" value="','',$swf));//获得SWF地址
					$src = $src[0][0];
					preg_match_all('/href="(.*)&title=(.*)&desc=(.*)&pic=(.*)" target="_blank"\>/isU',$src,$nsrc);
					$resultvod['pic']=$nsrc[4][0];
				break;
				case 'tudou':
					$vod_url=trim($this->url);
					$filename = "http://api.tudou.com/v3/gw?method=repaste.info.get&appKey=mykey&format=json&url={$vod_url}";
					$result = $this->_curl_get_file_contents($filename);
					$array_result = json_decode($result,true);
					$resultvod['swf'] = $array_result['repasteInfo']['playlistInfo']['outerPlayerUrl'];
					$resultvod['pic'] =$array_result['repasteInfo']['itemInfo']['bigPicUrl'];
					if(preg_match("/^http\:\/\/.*[^\.html]$/i",$vod_url)){
						$vod_url=explode('/',$vod_url);
						$num=count($vod_url)-2;
						$vod_url=$vod_url[$num];
						$filename="http://api.tudou.com/v3/gw?method=item.info.get&appKey=myKey&format=json&itemCodes=$vod_url";
						$result = $this->_curl_get_file_contents($filename);
						$array_result = json_decode($result,true);
						$resultvod['swf'] = $array_result['multiResult']['results']['0']['outerPlayerUrl'];
						$resultvod['pic'] = $array_result['multiResult']['results']['0']['bigPicUrl'];
					}
				break;
				case 'ku6':
					$vod_url=trim($this->url);
					$pos = strrpos($vod_url,'/');
					$vid = substr($vod_url, $pos + 1, -5);
					//如果不是HTML结尾的话
					if(preg_match("/^http\:\/\/.*[^\.html]$/i",$vod_url)){
						$pos = strrpos($vod_url,'&v=');
						$vid = substr($vod_url, $pos + 3, -6);
					}
					//
					$filename="http://v.ku6.com/fetch.htm?t=getVideo4Player&vid=$vid";
					$result = $this->_curl_get_file_contents($filename);
					$array_result = json_decode($result,true);
					$resultvod['pic'] = $array_result['data']['bigpicpath'];
					$resultvod['swf'] = "http://player.ku6.com/refer/{$vid}/v.swf";
				break;
			}
			return $resultvod;
		}else{
			return '给出的网站不在列表之内';
		}
	}
}