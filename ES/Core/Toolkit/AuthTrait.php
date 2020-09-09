<?php
namespace ES\Core\Toolkit;

trait AuthTrait{
    /**
     * 生成签名
     * @param string $str
     * @return string
     */
    protected function generateSign($str):string
    {
        return password_hash($str,PASSWORD_DEFAULT);
    }
    /**
     * 验证签名
     * @param string $str
     * @param string $hash
     * @return bool
     */
    protected function validateSign(string $str,string $hash):bool
    {
        return password_verify($str,$hash);
    }
    
    /**
     * 表单签名
     * @param string $salt
     * @return string
     */
    protected function formSign(string $salt = ''):string{
        return password_hash(date('Y-m-dW').$salt,PASSWORD_DEFAULT);
    }
    
    /**
     * 表单验证
     * @param string $hash
     * @param string $str
     * @return bool
     */
    protected function validateFormSign(string $hash,string $salt = ''):bool
    {
        return password_verify(date('Y-m-dW').$salt,$hash);
    }
}
