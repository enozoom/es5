<?php
namespace ES\Core\Toolkit;
/**
 * 控制器的前端显示的源码
 * @author Joe
 *
 */
class Output{
    protected $final_output;
    
 /**
    * 追加字符串
    * @param string $output
    */
    public function append_output(string $output){
        $this->final_output = empty($this->final_output) ? $output : $this->final_output.$output;
    }
    
    /**
     * 显示最终结果
     * @param bool $return 是否返回前端字符串
     */
    public function display(bool $return=FALSE){
        if($return) return $this->final_output;
        echo $this->final_output;
    }
    
    /**
     * 前端字符串的字节数
     * @return number
     */
    public function length():int{
        return mb_strlen($this->final_output,'UTF-8');
    }
}
