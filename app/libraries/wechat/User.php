<?php
namespace app\libraries\wechat;
class User extends WechatAbstract
{
    // snsapi_base 静默模式，snsapi_userinfo 需手动确定
    public $scope = 'snsapi_base';
    /**
     * 返回当前用户的openid,
     * 本方法受限于info(),info()受限于openidLink()
     * @return string
     */
    public function openid():string
    {
        return empty($u=$this->info())?'':$u['openid'];;
    }
    
    /**
     * 获取当前微信访客的用户信息
     * 使用这个方法的前提已经用openidLink($redirect,$scope)生成的链接跳转回来并带有code码且未过期,
     * @return array 这里的返回结果的信息和跳转链接中的$scope参数有关
     */
    public function info():array
    {
        if( !empty($_GET['code']) ){
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
            $url = sprintf($url,$this->appid,$this->appSecret,$_GET['code']);
            $json = json_decode( $this->curlGet($url),true );
            if(empty($json->errcode) ){
                var_dump($this->scope , 'snsapi_userinfo');
                if($this->scope == 'snsapi_userinfo'){
                    $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
                    $url = sprintf($url,$json['access_token'],$json['openid']);
                    $json = json_decode( $this->curlGet($url),true );
                }
                return $json;
            }
        }
        return [];
    }
    
    /**
     * 生成一个微信获权跳转回来的地址
     * @param  string $redirect 跳转地址 为空则为当前url，可以不写HOST部分会自动填写
     * @return void
     */
    public function openidLink($redirect=''):string
    {
        $https = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=ES#wechat_redirect';
        if(empty($redirect)){
            $redirect = $this->currentURL();
        }else{
            if(strpos($redirect, 'http') !== 0){
                $redirect = $this->baseUrl($redirect);
            }
        }
        return sprintf($https,$this->appid,$redirect,$this->scope);
    }
}

