<?php namespace ES\Core\Hook;
/**
 * 框架执行前与执行后运行
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 */
interface HookInterface{
  /**
   * 控制器未初始化前
   */
  public abstract function beforeController();
  /**
   * 控制器实例化成功后
   */
  public abstract function afterController();
  /**
   * 控制器实例化成功后调用响应方法
   */
  public abstract function afterControllerMethod();
  
}