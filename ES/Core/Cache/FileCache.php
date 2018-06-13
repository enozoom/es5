<?php
namespace ES\Core\Cache;

use ES\Core\Toolkit\ConfigStatic;
use ES\Core\Toolkit\FileStatic;

class FileCache
{
    private $cachePath;
    public function __construct($cachePath=''){
        $cmdq = ConfigStatic::getConfigs('Cmdq');
        empty($cachePath) && $cachePath = $cmdq->d.'/'.$cmdq->c;
        $this->cachePath = str_replace(['\\','//'], '/', APPPATH.'cache/'.$cachePath.'.json');
    }

    /**
     * 键或值是否存在有效
     * @param string $key
     * @return bool 当键不存在，或者值为空，或者值已经过期均返回false
     */
    public function isHit(string $key):bool
    {
        if(file_exists($this->cachePath) &&
           !empty($str = file_get_contents($this->cachePath)) &&
           !empty($json = json_decode($str)) &&
           !empty($json->$key) &&
           !empty($json->$key->value)){
            if(empty($json->$key->expires) || $json->$key->expires>time()){
                return true;
            }
        }
        return false;
    }

    /**
     * 根据键获取值，默认自动调用isHit判断键值是否有效
     * @param string $key 键
     * @return mixed 当无值时返回false
     */
    public function getItemByKey(string $key)
    {
        if($this->isHit($key)){
            return json_decode( file_get_contents($this->cachePath) )->$key->value;
        }
        return false;
    }

    /**
     * 设置一条缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param number $expires 该缓存存活时间
     * @return bool
     */
    public function setItem(string $key,$value,int $expires=0):bool
    {
        $data = (object)[$key=>['value'=>$value,'expires'=>empty($expires)?0:$expires+time()]];
        $fpath = FileStatic::mkdir(substr($this->cachePath, 0,strripos($this->cachePath, '/')));
        if(file_exists($this->cachePath)){
            if(!empty($str = file_get_contents($this->cachePath))){
                $json = json_decode($str);
                $json->$key = $data->$key;
                $data = $json;
            }
        }
        return file_put_contents($this->cachePath, json_encode($data,JSON_UNESCAPED_UNICODE))>0;
    }
}
