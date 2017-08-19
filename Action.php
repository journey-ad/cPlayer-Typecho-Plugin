<?php
if(!defined('__TYPECHO_ROOT_DIR__'))exit;
class cPlayer_Action extends Typecho_Widget implements Widget_Interface_Do {
    public function execute(){}
    public function action(){
        $this->on($this->request->is('get=url'))->url();
    }
    private function url(){
        self::filterReferer();
        $url = "http://music.163.com/api/song/enhance/player/url?csrf_token=";
        $id = $this->request->get('id');
        $br = Typecho_Widget::widget('Widget_Options')->plugin('cPlayer')->bitrate*1000;
        $data['POST'] = array(
            'ids' => '["'.$id.'"]',
            'br' => $br,
            'csrf_token' => '',
        );
        $data['COOKIE'] = 'os=pc;';
        $cexecute = self::fetch_url($url, $data);
        if ($cexecute){
            $result = json_decode($cexecute);
            if ($result->code == 200){
                $url = preg_replace('/http:\/\/m(\d+)[a-zA-Z]*/', 'https://m$1', $result->data[0]->url);
                if(empty($url))$url = Helper::options()->pluginUrl.'/cPlayer/assets/empty.mp3';
                $this->response->redirect($url);
            }
        }
    }

    /**
     * url抓取,两种方式,优先用curl,当主机不支持curl时候采用file_get_contents
     * 参数$data为数组，结构类似于
     * $data = array(
     *     'POST'       => '',
     *     'COOKIE'     => 'appver=2.0.2',
     *     'REFERER'    => 'http://music.163.com/',
     *     'HTTPHEADER' => '',
     *     'USERAGENT'  => ''
     * );
     * 
     * @param unknown $url
     * @param array $data
     * @return boolean|mixed
     */
    private function fetch_url($url,$data = null){
        if(function_exists('curl_init')){
            $curl=curl_init();
            curl_setopt($curl,CURLOPT_URL,$url);
            if(isset($data['POST'])){
                if(is_array($data['POST'])) $data['POST'] = http_build_query($data['POST']);
                curl_setopt($curl,CURLOPT_POSTFIELDS,$data['POST']);
                curl_setopt($curl,CURLOPT_POST, true);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            if(isset($data['HTTPHEADER'])) curl_setopt($curl, CURLOPT_HTTPHEADER, $data['HTTPHEADER']);
            if(isset($data['REFERER'])) curl_setopt($curl,CURLOPT_REFERER, $data['REFERER']);
            if(isset($data['COOKIE'])) curl_setopt($curl,CURLOPT_COOKIE, $data['COOKIE']);
            if(isset($data['USERAGENT'])) curl_setopt($curl,CURLOPT_USERAGENT, $data['USERAGENT']);
            $result=curl_exec($curl);
            $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpCode != 200) return false;
            return $result;
        }else{
            //若主机不支持openssl则file_get_contents不能打开https的url
            if($result = @file_get_contents($url)){
                if (strpos($http_response_header[0],'200')){
                    return $result;
                }
            }
            return false;
        }
    }
    
    private function filterReferer(){
        if(isset($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false){
            http_response_code(403);
            die();
        }
    }
}