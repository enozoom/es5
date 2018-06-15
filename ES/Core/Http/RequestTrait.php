<?php
namespace ES\Core\Http;
trait RequestTrait{

    /**
     * 当前URL
     * @return string
     */
    protected function currentURL():string
    {
        return sprintf('%s://%s%s',$_SERVER['REQUEST_SCHEME'],$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']);
    }
    
    /**
     * 获取当家uri中的host部分，与传入的参数组成网址
     * @param string $url
     * @return string
     */
    protected function baseUrl(string $url=''):string
    {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ?
            'https://' : 'http://';
            
            return
            ( strpos($url, 'http://') === FALSE && strpos($url, 'https://') === FALSE )?
            ( $http_type.str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$url) ):$url;
    }
    /**
     * 请求的方法
     * @param string $method 如果$method不为空则判断请求方法是否与$method一致。
     * @return string|bool
     */
    protected function reqestMethod(string $method=''):string
    {
        return empty($m = strtolower($_SERVER['REQUEST_METHOD']))?$m:$m==strtolower($method);
    }

    /**
     * 将提交的json转化成对象
     */
    protected function phpInputData():\stdClass
    {
            $data = file_get_contents('php://input');
            return empty($data)?null:json_decode($data);
    }
    
/**
 * 重写$_GET
 */
    protected function rewriteGet()
    {
        $uri = $_SERVER['REQUEST_URI'];
        isset($_SERVER['HTTP_X_ORIGINAL_URL']) && $uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
        $uri = parse_url($uri,PHP_URL_QUERY);
        $_get = [];
        if( !empty($uri) ){// $_GET有参值
            foreach( explode('&',str_replace('&amp', '&', $uri) ) as $kv ){
                if($_kv = explode('=',$kv)){
                    (!empty($_kv[0]) && !empty($_kv[1])) && $_get[$_kv[0]] = $_kv[1];
                }
            }
        }
        $_GET = empty($_get)?null:$_get;
    }

/**
 * 远程get获取
 * @param string $url
 */
    protected function curlGet($url):string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //        curl_setopt($ch, CURLOPT_SSLVERSION, 3); //设定SSL版本
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER ,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST ,FALSE);
        $output = curl_exec($ch);
        
        curl_close($ch);
        return $output;
    }
    
/**
 * 远程post提交
 * @param string $url
 * @param array|string $post
 * @param bool $is_json
 * @param array header
 */
    protected function curlPost(string $url,$post,bool $isJson=FALSE,array $header=[])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl($url));
        curl_setopt($ch, CURLOPT_POST,TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,is_array($post)?http_build_query($post):$post);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        empty($header) || curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $output = curl_exec($ch);
        if(curl_errno($ch)){// 出现异常
            $Log = \ES\Core\Log\Logger::getInstance();
            $Log->debug($output);
            $output = '';
        }
        curl_close($ch);
        $isJson && !empty($output) && $output = json_decode($output);
        return $output;
    }
}