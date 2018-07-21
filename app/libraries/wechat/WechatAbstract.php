<?php
namespace app\libraries\wechat;

use ES\Core\Toolkit\ConfigStatic;
use ES\Core\Http\RequestTrait;
use  \ES\Core\Cache\FileCache;
abstract class WechatAbstract
{
    use RequestTrait;
    protected $accessToken;
    protected $appid;
    protected $appSecret;
    protected $encoding_aes_key;
    protected $Cache;
    public function __construct(){
        $configs = ConfigStatic::getConfig('wechat','Param');
        foreach($configs as $k=>$v) $this->$k = $v;
        $this->Cache = new FileCache('wechat/'.$this->appid);
        $this->accessToken = $this->accessToken();
    }
    public function accessToken():string
    {
        $key = 'accessToken';
        $token = '';
        if(!empty($accessToken = $this->Cache->getItemByKey($key))){
            $token = $accessToken;
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $json = json_decode($this->curlGet(sprintf($url,$this->appid,$this->appSecret)));
            if(empty($json->errcode)){
                 $this->Cache->setItem($key, $json->access_token,$json->expires_in);
                 $token = $json->access_token;
            }
        }
        return $token;
    }
    
    protected function log($msg){
        \ES\Core\Toolkit\ConfigStatic::getConfigs('Logger')->debug($msg);
    }
}

