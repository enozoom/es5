<?php
namespace App\Controllers\Common;

use ES\Core\Controller\DataController;

final class Min extends DataController{
    private $dir;
    private $suffix;

    public function __construct()
    {
        parent::__construct();
        $this->dir = BASEPATH.'public/theme/';
    }
 
    public function index(string $files='')
    {
        $this->suffix = substr($files,strrpos($files,'.')+1);// 获取后缀
        $output = $this->cache($files);
        empty($output) || $this->compress($output,$this->suffix);
    }

    /**
     * 对合并文件进行css输出
     * @param string $str
     * @return string
     */
    private function _css(string $str=''):string
    {
        $str = preg_replace(array('/{\s*([^}]*)\s*}/','/\s*:\s*/','~\/\*[^\*\/]*\*\/~s'),array('{$1}',':',''),$str);
        $str = preg_replace(array('/'.PHP_EOL.'/','/\n*/'),'',$str);
        return $str;
    }

    /**
     * 对合并文件进行js输出
     * @param string $str
     * @return string
     */
    private function _js(string $str=''):string
    {
        return $str;
    }

    /**
     * 压缩字符串
     * @param string $str
     * @return void 直接输出到页面
     */
    private function compress($str,$suffix='css')
    {
        $this->render($str,$suffix);
    }

    /**
     * 读取或将合并文件包含字符写入缓存
     * @param string $files
     * @return string
     */
    private function cache(string $files=''):string
    {
        $output = '';
        $files = str_replace(".{$this->suffix}",'',$files);// 去后缀
        foreach(explode(',',$files) as $f){
            $path = $this->dir.$this->suffix."/{$f}.".$this->suffix;
            file_exists($path) && $output .= file_get_contents($path);
        }
        if(!empty($output)){
            $method = "_{$this->suffix}";
            $output = $this->$method($output);
        }
        return $output;
    }
}