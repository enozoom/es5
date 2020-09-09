<?php
namespace ES\Core\Toolkit;
final class AryStatic{
    /**
     * $keys中的值是否存在于$data的键中
     * @param array $keys
     * @param array $data
     */
    public static function isRequired(array $keys,array $data=[]):bool
    {
        empty($data) && $data = $_POST;
        foreach($keys as $k)
        {
            if( !key_exists($k, $data) )
            {
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * $keys中的值对应在$data的键不是空值
     * @param array $keys
     * @param array $data
     * @return bool
     */
    public static function unBlank(array $keys, array $data=[]):bool
    {
        foreach($keys as $k) {
            if(!key_exists($k, $data) || empty($data['k'])){
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * 将一组对象中的某两个字段简化成一维键值数组
     * @param array $objs
     * @param string $k     要转成key的字段
     * @param string $v     要转成value的字段
     */
    public static function obj2kvArray(array $objs,string $k,string $v):array
    {
        $ary = [];
        if(!empty($objs)){
            foreach($objs as $o)
            {
                if( isset($o->$k) && isset($o->$v) )
                {
                    $ary[$o->$k] = $o->$v;
                }
            }
        }
        return $ary;
    }
}
