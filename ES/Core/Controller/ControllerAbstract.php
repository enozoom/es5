<?php
namespace ES\Core\Controller;
use ES\Core\Load\Load;
use ES\Core\Toolkit\{Output,ConfigStatic};
use ES\Core\Model\ModelAbstract;

abstract class ControllerAbstract{
    private static $instance;
    public $load;
    public $output;
    public $cmdq;
    
    public function __construct(){
        self::$instance =& $this;
        $this->load = new Load();
        $this->output = new Output();
        isset($_SESSION) || session_start();
        $this->cmdq = ConfigStatic::getConfigs('Cmdq');
    }
    
    public static function &getInstance():ControllerAbstract{
        return self::$instance;
    }
    
    public function closeDB(){
        foreach( get_object_vars($this) as $var=>$val ){
            if($val instanceof ModelAbstract){
                $this->$var->db->close();
                break;
            }
        }
    }
}