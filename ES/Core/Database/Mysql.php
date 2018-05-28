<?php
namespace ES\Core\Database;
use ES\Core\Toolkit\ConfigStatic;
use ES\Core\Database\DatabaseInterface;

class Mysql implements DatabaseInterface{
    protected $mysqli;
    protected static $instance;
    protected $last_query = '';
    protected $host = '';
    protected $user = '';
    protected $password = '';
    protected $dbname = '';
    protected $prefix = '';
    protected $port = 3306;

    public function __construct()
    {
        $configs = ConfigStatic::getConfigs('Database');
        foreach( get_class_vars(__CLASS__) as $var=>$val)
        {
            empty($configs->$var) || $this->$var = $configs->$var;
        }
        empty($configs->host) || $this->conn();
    }
    
    /**
     * 获取一个单例
     * @return ES_Mysqli
     */
    public static function get_instance():DatabaseInterface
    {
        self::$instance instanceof self || self::$instance = new self();
        return self::$instance;
    }
    
    /**
     * 连接数据库，返回一个mysqli对象
     * @return Mysqli
     */
    protected function conn():bool
    {
        if(!$this->mysqli instanceof \Mysqli)
        {
            $mysqli = new \Mysqli($this->host, $this->user, $this->password, $this->dbname,$this->port);
            if(mysqli_connect_error())
            {
                printf("E_Database connect failed: %s\n", mysqli_connect_error());
            }else{
                $this->mysqli = &$mysqli;
                $this->mysqli->set_charset("utf8");
            }
        }
        return $this->mysqli instanceof \Mysqli;
    }
    /**
     * 开启或者关闭事务
     * @param bool $io 默认关闭
     * @return void
     */
    public function transaction():bool
    {
        return $this->mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    }
    
    /**
     * 提交事务，技术事务
     * @return void
     */
    public function commit():bool
    {
        return $this->mysqli->commit();
    }
    
    /**
     * 回滚
     */
    public function rollback():bool
    {
        return $this->mysqli->rollback();
    }
    
    /**
     * 选择（更换）数据库
     * @param $dbname
     * @return void
     */
    public function select_db(string $dbname,string $prefix=''):bool
    {
        $this->dbname = $dbname;
        empty($prefix) || $this->prefix = $prefix;
        return $this->mysqli->select_db($dbname);
    }
    
    /**
     * 关闭连接
     * @return void
     */
    public function close():bool
    {
        if(!empty($this->mysqli))
        {
            $this->mysqli->close();
            $this->mysqli = NULL;
        }
        return empty($this->mysqli);
    }
    
    /**
     * 插入一条数据
     * @param array$data array(field=>val..)
     * @param string $tablename
     *
     * @return int 新插入的PKID,如果插入失败返回0，无自增长主键的始终返回1
     */
    public function _insert(array $data,string $tablename):int
    {
        if(empty($data)) return 0;
        $sql = "INSERT INTO `{$this->prefix}{$tablename}`";
        $fields = '';
        $vals = '';
        foreach($data as $field=>$val)
        {
            $fields .= ",`{$field}`";
            $vals .= ",'". $this->_escape($val) ."'";
        }
        
        $sql .= '('. substr($fields,1) .') VALUES ';
        $sql .= '('. substr($vals,1) .')';
        
        $result = $this->query($sql);
        
        // 以解决无自增长主键的表返回始终1
        $i = $this->mysqli->insert_id;
        $result && $i == 0 && $i = 1;
        
        return $result ? $i : 0;
    }
    
    /**
     * 更新一条数据
     * @param int $pkid
     * @param array $data
     * @param string$pkfield
     * @param string$tablename
     *
     * @return int 被更新的主键
     */
    public function _update(int $pkid,array $data,string $pkfield,string $tablename):int
    {
        $set = '';
        foreach($data as $field=>$val) $set .= ", `{$field}` = '".$this->_escape($val)."'";
        $set = substr($set,1);
        $where = "`{$pkfield}` = '".$this->_escape($pkid)."'";
        
        // 判断有满足更新条件的数
        $total = $this->_get_totalnum($where, $tablename);
        if($total == 0)return 1;// 无满足条件数据则直接返回更新成功
        
        $tablename = $this->tablename($tablename);
        $sql = "UPDATE `{$tablename}` SET {$set} WHERE {$where}";
        $result = $this->query($sql);
        //$this->getConfigs('logger')->debug( $sql );
        return $result?$pkid:0;
    }
    
    /**
     * 删除数据
     * @param string $where
     * @param string $tablename
     */
    public function _delete(string $where,string $tablename):int
    {
        $sql = "DELETE FROM `{$this->prefix}{$tablename}` WHERE {$where}";
        return $this->query($sql);
    }
    
    /**
     * 获取一组对象
     * @param string $tablename 数据表名
     * @param string $where 条件
     * @param string $select字段
     * @param string $orderby 排序
     * @param string $limit 数量
     *
     * @return array(object..)
     */
    public function _get(string $tablename,string $where='',string $select='*',string $orderby='',string $limit=''):array
    {
        $tablename = $this->tablename($tablename);
        
        empty($select) && $select = '*';
        if($select != '*' && strpos($select, 'AS') === FALSE )
        {
            $select = '`'.str_replace(',','`,`', preg_replace('/\s*/','',$select)).'`';
        }
        
        $sql = "SELECT {$select} FROM `{$tablename}`";
        
        empty($where) ||$sql .= " WHERE ".$this->_where($where);
        empty($orderby) ||$sql .= " ORDER BY $orderby";
        empty($limit) ||$sql .= " LIMIT $limit";
        return $this->query($sql);
    }
    
    /**
     * 获取一个对象
     * @param int$idID
     * @param string $pkfield 主键字段
     * @param string $tablename 数据表名
     * @param string $select字段
     *
     * @return object
     */
    public function _get_by_PKID(int $id,string $pkfield,string $tablename,string $select='*')
    {
        $where = sprintf("`%s` = '%s'",$pkfield,$this->_escape($id));
        $result = $this->_get($tablename,$where,$select,'',1);
        return empty($result)?NULL:$result[0];
    }
    
    /**
     * 按条件获取行数
     * @param string $where 条件
     * @param string $tableName 数据表名
     * @param string $distinct含过滤字段的唯一字段
     * @return int
     */
    public function _get_totalnum(string $where,string $tableName,string $distinct=''):int
    {
        $tableName = $this->tablename($tableName);
        $sql = 'SELECT COUNT(%s) AS count FROM %s';
        $sql = sprintf($sql,empty($distinct)?'*':"DISTINCT $distinct",$tableName);
        empty($where) ||$sql .= " WHERE {$where}";
        $count = $this->query($sql);
        if( is_bool($count)) return 0;
        return (int)$count[0]->count;
    }
    
    
    /**
     * 对where进行过滤
     * @param string $where
     *
     * @return string
     */
    public function _where(string $where):string
    {
        if(empty($where)) return '';
        // 基本过滤,条件句中不能含有DML,DDL语句
        foreach(array('select','delete','update','drop','create','alter') as $dl)
        {
            if(!stripos( strtolower($where) ,$dl)===FALSE)
            {
                die('SQL含非法字符');
            }
        }
        return $where;
    }
    
    /**
     * 转义特殊字符
     * @param string $str 要转义的字符，一般是条件句键值中的值
     * @return string
     */
    public function _escape(string $str=''):string
    {
        return empty($str)?(is_numeric($str)?0:''):$this->mysqli->real_escape_string($str);
    }
    
    /**
     * 执行SQL
     * @param string $sql
     * @return mixed
     */
    public function query(string $sql)
    {
        $this->last_query = $sql;
        if(!$_result = $this->mysqli->query($sql)){
            $this->log_error();
            return FALSE;
        }
    
        if(!$_result instanceof \MySQLi_Result ){// INSERT,UPDATE返回值不是Result
        empty( $_result ) && $this->log_error();
        return $_result;
        }
    
        $result = [];
        if($_result->num_rows>0){
            do{
            $obj = $_result->fetch_object();
            empty($obj) || $result[] = $obj;
            }while($obj);
        }
    
        $_result->close();
        return $result;
    }
    
    /**
     * 实验性
     * 生成一句SQL
     * @param string $table
     * @param string $select
     * @param string $where
     * @param string $orderby
     * @param string $limit
     */
    public function sql(string $table,string $select='',string $where='',string $orderby='',string $limit=''):string
    {
        empty($select) && $select = '*';
        $sql = sprintf('SELECT %s FROM %s',$select,$this->tablename($table));
        empty($where) || $sql .= ' WHERE '.$where;
        empty($orderby) || $sql .= ' ORDER BY '.$where;
        empty($limit) || $sql .= ' LIMIT '.$where;
        return $sql;
    }
    
    /**
     * 上次执行成功的SQL语句
     *
     * @return string
     */
    public function last_query():string
    {
        return $this->last_query;
    }
    /**
     * 实际表名
     * @param string $tablename
     * @param string $prefix 默认前缀
     * @return string
     */
    public function tablename(string $tablename,string $prefix='')
    {
        empty($prefix) && $prefix = $this->prefix;
        if(strpos($tablename,$prefix) === FALSE || strpos($tablename,$prefix) > 0){
            $tablename = $prefix.$tablename;
        }
        return $tablename;
    }
    /**
     * 当前数据库名
     */
    public function dbname():string
    {
        return $this->dbname;
    }
    
    /**
     * 记录发生数据库操作错误的调用方法及其控制器
     */
    protected function log_error()
    {
        ConfigStatic::getConfigs('Logger')->error(PHP_EOL.$this->mysqli->error.PHP_EOL.$this->last_query);
    }
}