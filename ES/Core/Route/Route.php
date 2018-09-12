<?php
namespace ES\Core\Route;
use ES\Core\Http\ResponseTrait;
//use ES\Core\Cache\Cache;
use ES\Core\Toolkit\ConfigStatic;

class Route{
    use ResponseTrait;
    public function initController(){
        // 开始缓存
        //$cache = new Cache();
        //$cache->get();
        
        $cmdq = ConfigStatic::getConfigs('Cmdq');
        
        $ctl_cls = ucfirst($cmdq->c);
        try{
            $file = APPPATH.sprintf('controllers/%s/%s.php',$cmdq->d,$ctl_cls);
            if( !file_exists($file) ){
                throw new \Exception();
            }
            $reflector = new \ReflectionClass(sprintf('\\app\\controllers\\%s\\%s',$cmdq->d,$ctl_cls));
        }catch (\Exception $e){
            $this->show_503("无法通过反射，获取类{$ctl_cls}！",'文件不存在');
        }
        $args = [];
        if(strlen($cmdq->q)){// 如果使用汇总压缩css,js,则不能用,分割参数
            $args = $cmdq->c == 'min' ? [$cmdq->q] : explode(',',$cmdq->q);
            foreach($args as $_k=>$_v){
                if(!strlen($_v)){
                    unset($args[$_k]);
                }
            }
        }
        $reflector->isFinal() ||
        $this->show_503($reflector->getName().'类修饰符必须是final','类修饰符错误' );
        $cls = $reflector->newInstance();
        $parent_cls = '\ES\Core\Controller\ControllerAbstract';
        is_subclass_of($cls,$parent_cls) ||
        $this->show_503('控制器必须是'.$parent_cls.'的子类','非控制器实例类');
        $hook = ConfigStatic::getConfigs('Hook');
        $hook->afterController();

        $rMethod = null;
        try{// 这里产生的异常是反射异常，用Exception捕获
            $rMethod = $reflector->getMethod($cmdq->m);
        }catch(\Exception $e){
            $this->show_503("控制器{$cmdq->c}方法{$cmdq->m}不存在",'方法不存在');
        }
        $this->disable_method( $rMethod );
        $rMethod->invokeArgs($cls,$args);
       
        $hook->afterControllerMethod();
        
        $cls->closeDB();// 关闭数据库
        
        $html = $cls->output->display(1);// 输出
        
        //$cache->save($html);
        
        $this->render($html);

    }
    /**
     * 禁止前台直接访问的方法们
     * @param ReflectionMethod $rMethod
     */
    private function disable_method( \ReflectionMethod $rMethod ){
        if(!$rMethod->isPublic() || strpos($rMethod->name, '_') === 0 )
        {
            $this->show_403();
        };
    }
}