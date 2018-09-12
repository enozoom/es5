<?php
namespace ES\Core\Model;
/**
 * 用户注册登录相关类
 */
use ES\Core\Toolkit\AuthTrait;
use ES\Core\Toolkit\AryStatic;
abstract class UsrModelAbstract extends ModelAbstract
{
    use AuthTrait;
    /**
     * 注册一个用户
     * 配合isRequired对必需项进行过滤后新增
     * @param array $data
     * @return int 注册成功返回新增用户ID，失败返回小于0的错误码
     */
    public abstract function register(array $data=[]):int;
    /**
     * 用户登录
     * 配合isRequired对必需项进行过滤后新增
     * @param array $data
     * @return int 登录成功返回用户ID，失败返回小于0的错误码
     */
    public abstract function login(array $data=[]):int;
    /**
     * 用户登出
     * @param array $sessionKeys 存放在SESSION中关于用户的信息key
     * @return void
     */
    public abstract function logout(array $sessionKeys=[]);
    /**
     * 是否具备必需项
     * @param array $keys
     * @param array $data
     * @return bool 具备为ture，缺少为false
     */
    protected function isRequired(array $keys,array $data=[]):bool
    {
        return AryStatic::isRequired($keys, $data);
    }
}

