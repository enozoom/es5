<?php
/*
 * 操作数据库
 */
namespace ES\Core\Model;

use ES\Core\Toolkit\ConfigStatic;
use ES\Core\Http\ResponseTrait;
abstract class ModelAbstract{
    use ResponseTrait;
    protected $db;
    protected $tableName;
    protected $primaryKey;
    
    public function __construct()
    {
        $conf = ConfigStatic::getConfigs('Database');
        if(in_array('',[$conf->host,$conf->user,$conf->dbname])){
            $this->show_503('config/database.php,数据库配置参数不足！');
        }
        $db = 'ES\\Core\\Database\\'.$conf->driver;
        $this->db = $db::get_instance();
        $this->tableName_primaryKey();
    }
    
    /**
     * 自填充数据库表名及主键
     */
    private function tableName_primaryKey(){
            if(empty($this->tableName) || empty($this->primaryKey)){
                 $cls = strtolower( substr(($cls = get_class($this)), strrpos($cls, '\\')+1) );
                 empty($this->tableName) && $this->tableName = $cls;
                 empty($this->primaryKey) && $this->primaryKey = $cls.'_id';
            }
    }
    
    public function __get(string $var)
    {
        switch($var){
                case 'tableName':
                        return $this->db->tablename( $this->$var );
                break;default:
                        return $this->$var;
        }
        return null;
    }
    
/**
 * 获取一组数据
 * @param string $where
 * @param string $select
 * @param bool $returnArray
 * @param string $orderby
 * @param array $limit 分页 $limit = [$per,$page]
 *
 * @return [obj,..]/[[],..]
 */
    public function _get(string $where='', string $select='*', string $orderby='',array $limit=[]):array
    {
        if(!empty($limit)){
            $per = $limit[0];
            if(count($limit)==1){
                $limit = $per;
            }elseif(count($limit)==2){
                $page = $limit[1];
                $limit = $page.','.$per;
            }
        }
        empty($orderby) && $orderby = "{$this->primaryKey} DESC";
        return $this->db->_get($this->tableName,$where,$select,$orderby,$limit);
    }
    
/**
 * 根据ID获取特定一条数据
 * @param string $where
 * @param string $select
 * @param bool $returnArray
 *
 * @return obj/null
 */
    public function _getByPKID(int $id=0,string $select='*'):\stdClass
    {
        return $this->db->_get_by_PKID($id,$this->primaryKey,$this->tableName,$select);
    }
    
/**
 * 根据条件获取
 * @param string $where
 *
 * @return int
 */
    public function _getTotalnum(string $where=''):int
    {
        return $this->db->_get_totalnum($where,$this->tableName);
    }
    
/**
 * 插入数据
 * @param array $data
 * @return 插入成功的主键ID
 */
    public function _insert(array $data):int
    {
        // 自动加入新增时间戳
        $timestamp = '';
        foreach($this->_attributes() as $k=>$t){
            if( strpos($k,'timestamp') ){
                $timestamp = $k;
                break;
            }
        }
        !empty($timestamp) && !key_exists($timestamp, $data) && $data[$timestamp] = time();
        return $this->db->_insert($this->_filterData($data),$this->tableName);
    }
    
/**
 * 更新一条数据
 * @param int $pkid
 * @param array $array
 *
 * @return
 */
    public function _update(int $pkid,array $array)
    {
        return $this->db->_update($pkid,$this->_filterData($array),$this->primaryKey,$this->tableName);
    }

/**
 * 删除数据
 * @return
 */
    public function _delete(string $where):int
    {
        return $this->db->_delete($where,$this->tableName);
    }
    
    /**
     * 通过主键进行删除（这里的主键是model类中设置的主键不一定是真实的表主键）
     * @param int $pkid
     */
    public function _deleteByPKID($pkid):int
    {
        return $this->_delete( "{$this->primaryKey} = {$pkid}" );
    }
    
/**
 * 开启事务
 */
    public function _transStart()
    {
        $this->db->transaction();
    }
/**
 * 事务回滚
 */
    public function _rollback()
    {
        $this->db->rollback();
    }
/**
 * 关闭事务并且提交
 */
    public function _transEnd()
    {
        $this->db->commit();
    }
    
    
/**
 * 过滤非本表字段的数据
 * @param array $data
 */
    protected function _filterData(array $data=[]):array
    {
        foreach($data as $k=>$v){
            if(! key_exists($k, $this->_attributes()) ){
                unset($data[$k]);
            }
        }
        return $data;
    }
    
/**
 * 表字段及对应的字段描述
 * @param string $attr
 * @return empty($attr)?[]:''
 */
    public abstract function _attributes($attr='');
}