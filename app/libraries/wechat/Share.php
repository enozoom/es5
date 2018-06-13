<?php
namespace app\libraries\wechat;

use ES\Core\Toolkit\StrStatic;

class Share extends WechatAbstract
{
    use JsapiTrait;
    public function shareData(){
        $timestamp = time();
        $nonceStr = StrStatic::randomString(16);
        return ['appId' => $this->appid,
                    'timestamp' => $timestamp,
                    'nonceStr' => $nonceStr,
                    'signature' => $this->jsapiSign($nonceStr, $timestamp)
                   ];
    }
}

