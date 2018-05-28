<?php
namespace ES\Core\Toolkit;

/**
 * 获取全局变量$CONFIGS的相关值
 * 
 * 已知$CONFIGS包含有
 * 
 * [ // 基本配置文件
 *   
 *   Config=>{},
 *   // 当前的控制器，方法，控制器文件夹，方法参数
 *   
 *   Cmdq=>{},
 *   // 数据库配置文件
 *   
 *   Database=>{},
 *   // 钩子类
 *   
 *   Hook=>{},
 *   // 日志类
 *   
 *   Logger=>{},
 *   // 路由配置文件
 *   Route=>[],
 * ]
 *
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 */
final class ConfigStatic{
/**
 * 通过键获取对应的键的值
 * @param string $key
 * @param string $configName
 */
    public static function getConfig(string $key,string $configName='Config')
    {
        global $CONFIGS;
        return (isset( $CONFIGS->$configName ) && isset( $CONFIGS->$configName->$key ))?
            $CONFIGS->$configName->$key:null;
    }
  
/**
 * 通过文件名获取文件所有的配置信息
 * @param string $configName
 */
    public static function getConfigs(string $configName='Config')
    {
        global $CONFIGS;
        return $CONFIGS->$configName??null;
    }
/**
 * 设置当前的配置信息
 * @param string $val        新值
 * @param string $key        键
 * @param string $configName 配置文件
 * @return 当前对象  $this->setConfig(1,'cache')->setConfig('es_','prefix','database');
 */
    public static  function setConfig(string $val,string $key,string $configName='Config')
    {
        global $CONFIGS;
        $CONFIGS->{$configName}->{$key} = $val;
        return $CONFIGS;
    }
}