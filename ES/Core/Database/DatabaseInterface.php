<?php
namespace ES\Core\Database;
interface DatabaseInterface{

/**
 * 获取一个单例
 * @return DatabaseInterface 实现类
 */
  public static function get_instance():DatabaseInterface;
/**
 * 重新选择当前数据库
 * @param string $dbname 数据库名
 * @param string $prefix 数据库表前缀
 * @return bool
 */
  public function select_db(string $dbname,string $prefix=''):bool;
/**
 * 关闭数据库连接
 * @return void
 */
  public function close():bool;
/**
 * 最后一条SQL执行语句，无论是否执行成功
 * @return string
 */
  public function last_query():string;
/**
 * 当前数据库名称
 * @return string
 */
  public function dbname():string;
  
/*====================================|
|
|  DML
|
=====================================*/
  
/**
 * 插入一条数
 * @param array  $data      插入的内容
 * @param string $tablename
 * @return int 成功返回新插入的数据的主键ID，失败返回0
 */
  public function _insert(array $data,string $tablename):int;
/**
 * 更新一条数
 * @param int $pkid         被更新数据的主键ID
 * @param array $data       更新的内容
 * @param string $pkfield   被更新表的主键字段名
 * @param string $tablename 被更新表名
 * @return int 成功返回被修改的数据行主键ID，失败返回0
 */
  public function _update(int $pkid,array $data,string $pkfield,string $tablename):int;
/**
 * 删除数据
 * @param string $where
 * @param string $tablename
 * @return int 受影响的行数
 */
  public function _delete(string $where,string $tablename):int;
/**
 * 获取一组数据
 * @param sting $tablename 要查询的表
 * @param string $where    查询条件
 * @param string $select   查询字段
 * @param string $orderby  排序规则
 * @param string $limit    行数限制
 * @return array [obj,..]
 */
  public function _get(string $tablename,string $where='',string $select='*',string $orderby='',string $limit=''):array;
/**
 * 根据主键获取一条数据
 * @param int $id           主键ID
 * @param string $pkfield   主键字段
 * @param string $tablename 表名
 * @param string $select    查询字段
 * @return obj
 */
  public function _get_by_PKID(int $id,string $pkfield,string $tablename,string $select='*'):\stdClass;
/**
 * 获取满足条件的总行数
 * @param string $where     查询条件
 * @param string $tableName 表名
 * @param string $distinct  根据某字段去重复
 * @return int
 */
  public function _get_totalnum(string $where,string $tableName,string $distinct=''):int;

/**
 * 执行一条sql语句
 * @param string $sql
 * @return mixed
 */
  public function query(string $sql);
  
/*====================================|
|
|  DCL
|
=====================================*/
  
/**
 * 事务的开启
 * @param string $io
 * @return void
 */
  public function transaction():bool;
/**
 * 操作提交,事务关闭
 * @return void
 */
  public function commit():bool;
/**
 * 操作回滚
 */
  public function rollback():bool;
}