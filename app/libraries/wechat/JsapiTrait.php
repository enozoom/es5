<?php
namespace app\libraries\wechat;

trait JsapiTrait {
    /**
     * 获取JS签名
     * @param string $noncestr
     * @param string $timestamp
     * @param string $url
     * @return string
     */
    private function jsapiSign(string $noncestr,string $timestamp,string $url=''):string
    {
        $jsapi_ticket = $this->jsapiTicket($this->accessToken);
        empty($url) && $url = $this->currentURL();
        $args = ['noncestr'=>$noncestr,'jsapi_ticket'=>$jsapi_ticket,'timestamp'=>$timestamp,'url'=>$url];
        ksort($args);
        $query = str_replace(['%2F', '%3A', '%3F', '%3D', '%26'],['/'  ,   ':',   '?',   '=', '&'],http_build_query($args) );
        return sha1($query);
    }
    
    /**
     * 获取JS凭证
     * @return string
     */
    private function jsapiTicket():string
    {
        $key = 'ticket';
        if(!empty($ticket = $this->Cache->getItemByKey($key))){
            return $ticket;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
        $json = json_decode($this->curlGet(sprintf($url,$this->accessToken)));
        if( $json->errcode ){
            var_dump($json);exit();
        }else{
            $this->Cache->setItem($key, $json->ticket,$json->expires_in);
            return $json->ticket;
        }
        return '';
    }
}

