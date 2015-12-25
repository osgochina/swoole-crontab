<?php
/*
 * Create 2015.06.27 20:25
 * Author: dengsgo
 * Email: deng@fensiyun.com
 */
class EasyDB extends PDO{

    private $db_config ;//数据库配置
    private $lastsql = '';//最后一次执行的sql语句
    private $fetch_type = PDO::FETCH_ASSOC;//查询语句返回的数据集类型
    private $sql_stmt = '';//组装的sql语句
    private $query_type = '';//当前正在执行语句类型
    private $error_info = null;//错误信息
    private $log_path = './sql-error.log';//日志存储路径

    public function __construct($config = array()){
        $this->db_config = array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'root',
            'password' => '',
            'dbname' => 'test',
            'charset' => 'utf8'
        );
        $this->db_config = array_merge($this->db_config, $config);
        try {
            $dsn = 'mysql:host='.$this->db_config['host'].';port='.$this->db_config['port'].';dbname='.$this->db_config['dbname'];
            parent::__construct($dsn, $this->db_config['username'], $this->db_config['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->db_config['charset']));
            $this->exec('set names '.$this->db_config['charset']);
        } catch (PDOException $e) {
            echo '<p style="color:red">db connect has error!</p><br/><b>错误原因:</b>'.$e->getMessage().'<br/><b>错误报告:</b>';
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit();
        }
    }

    /*
     * 执行一条SQL语句，适用于比较复杂的SQL语句
     * 如果是增删改查的语句，建议使用下面进一步封装的语句
     * 返回值：执行后的结果对象
     */
    public function queryObj($sql, $data = array()){
        $this->lastsql = $sql;
        $stmt = $this->prepare($sql);
        $stmt->execute($data) ? true : $this->error_info = $stmt->errorInfo();
        return $stmt;
    }

    public function executeSql($sql){
        $stmt = $this->prepare($sql);
        return $stmt->execute(array());
    }

    //查询语句，返回单条结果
    //返回值：一维数组
    public function queryOne($sql, $data = array(), $type = ''){
        $type = !empty($type) ? $type : $this->fetch_type;
        return $this->queryObj($sql, $data)->fetch($type);
    }

    //查询语句，返回所有结果
    //返回值：二维数组
    public function queryAll($sql, $data = array(), $type = ''){
        $type = !empty($type) ? $type : $this->fetch_type;
        return $this->queryObj($sql, $data)->fetchAll($type);
    }

    //执行结果为影响到的行数,只能是insert/delete/update语句
    //返回值：数字，影响到的行数
    public function querySql($sql, $data = array()){
        return $this->queryObj($sql, $data)->rowCount();
    }

    //查询总条目
    public function count($table, $where = '', $data = array()){
        $sql = 'select count(*) as total from `'.$table.'` ';
        $sql .= !empty($where) ? ' WHERE '.$where : '';
        $r = $this->queryOne($sql, $data);
        return isset($r['total']) ? (int)$r['total'] : 0;
    }

    //插入方法，返回值为影响的行数
    //$idata为键值对数组，如array('name'=>'test','age'=>18);其中键为表字段，值为数值
    public function insert($table, $idata){
        $key = array_keys($idata);
        $set = '';
        foreach ($key as $v){
            $set .= $v.'=?,';
        }
        $set = !empty($set) ? trim($set,',') : '';
        $value = array_values($idata);
        return $this->table_insert($table)->setdata($set)->go($value);
    }

    //删除语句，返回值同上
    //$idata为条件键值对,如array('name'=>'test','age'=>18);其中键为表字段，值为数值.条件之间的关系为and
    public function delete($table, $idata){
        $key = array_keys($idata);
        $where = '';
        foreach ($key as $v){
            $where .= '`'.$v.'`=? AND';
        }
        $where = !empty($where) ? trim($where, 'AND') : '1=2';
        $value = array_values($idata);
        return $this->table_delete($table)->where($where)->go($value);
    }

    //更新语句，返回值同上
    /* public function update($sql, $data = array()){
        return $this->querySql($sql, $data);
    } */

    public function update($table, $set, $where){
        $set_ = '';
        $set_key = array_keys($set);
        $value = array_values($set);
        foreach ($set_key as $v){
            $set_ .= $v.'=?,';
        }
        $set_ = trim($set_, ',');
        $where_ = '';
        $where_key = array_keys($where);
        $where_value = array_values($where);
        foreach ($where_key as $v){
            $where_ .= '`'.$v.'`=? AND';
        }
        $where_ = !empty($where_) ? trim($where_, 'AND') : '1=2';
        $value = array_merge($value, $where_value);
        return $this->table_update($table)->setdata($set_)->where($where_)->go($value);
    }

    /*
     * 下面是链式操作的一些方法
     * 使用方式类似于   $db->table_select('mytable')->where('id=2')->go();
     * 注意：
     * 链式的第一个方法必须是table_????()
     * 链式的最后一个方法必须是go(),如果在链式中使用了预编译占位符，需要在go($data)传入参数
     */

    //查询链式起点，$table：表名
    public function table_select($table){
        $this->sql_stmt = 'SELECT $field$ FROM `$table$` $where$ $other$';
        $this->sql_stmt = str_replace('$table$', $table, $this->sql_stmt);
        $this->query_type = 'select';
        return $this;
    }

    //更新链式起点，$table：表名
    public function table_update($table){
        $this->sql_stmt = 'UPDATE `$table$` $set$ $where$';
        $this->sql_stmt = str_replace('$table$', $table, $this->sql_stmt);
        $this->query_type = 'update';
        return $this;
    }

    //删除链式起点，$table：表名
    public function table_delete($table){
        $this->sql_stmt = 'DELETE FROM `$table$` $where$';
        $this->sql_stmt = str_replace('$table$', $table, $this->sql_stmt);
        $this->query_type = 'delete';
        return $this;
    }

    //插入链式起点，$table：表名
    public function table_insert($table){
        $this->sql_stmt = 'INSERT INTO `$table$` $set$';
        $this->sql_stmt = str_replace('$table$', $table, $this->sql_stmt);
        $this->query_type = 'insert';
        return $this;
    }

    //链式执行结点，如果链式中使用了预编译占位符，需要在$data参数中传入
    //$data:占位符数据，
    //$multi:true,false 返回数据是多条还是一条，只适用于select查询,默认多条
    //$fetch_type:返回数据集的格式,默认索引
    public function go($data = array(), $multi = true, $fetch_type = ''){
        switch ($this->query_type){
            case 'select':
                $this->sql_stmt = str_replace('$field$', '*', $this->sql_stmt);
                $this->sql_stmt = str_replace(array(
                    '$other$','$where$'
                ), '', $this->sql_stmt);
                if ($multi){
                    return $this->queryAll($this->sql_stmt, $data, $fetch_type);
                }else{
                    return $this->queryOne($this->sql_stmt, $data, $fetch_type);
                }
                break;

            case 'insert':
            case 'delete':
                $this->sql_stmt = str_replace('$set$', '', $this->sql_stmt);
                $this->sql_stmt = str_replace('$where$', ' WHERE 1=2', $this->sql_stmt);
                return $this->querySql($this->sql_stmt, $data);
                break;

            case 'update':
                $this->sql_stmt = str_replace('$set$', '', $this->sql_stmt);
                $this->sql_stmt = str_replace('$where$', ' WHERE 1=2', $this->sql_stmt);
                $r = $this->queryObj($this->sql_stmt, $data)->errorInfo();
                return isset($r[2]) ? false : true;
                break;

            default:break;
        }
    }

    //链式操作的一些方法
    //field(),where(),order(),group(),limit(),setdata()
    public function __call($name, $args){

        switch (strtoupper($name)){
            case 'FIELD':
                $field = !empty($args[0]) ? $args[0] : '*';
                $this->sql_stmt = str_replace('$field$', $field, $this->sql_stmt);
                break;
            case 'WHERE':
                $where = !empty($args[0]) ? ' WHERE '.$args[0] : '';
                $this->sql_stmt = str_replace('$where$', $where, $this->sql_stmt);
                break;
            case 'ORDER':
                $order = !empty($args[0]) ? ' ORDER BY '.$args[0].' $other$' : '$other$';
                $this->sql_stmt = str_replace('$other$', $order, $this->sql_stmt);
                break;
            case 'GROUP':
                $group = !empty($args[0]) ? ' GROUP BY '.$args[0].' $other$' : '$other$';
                $this->sql_stmt = str_replace('$other$', $group, $this->sql_stmt);
                break;
            case 'LIMIT':
                $limit = !empty($args) ? ' $other$ LIMIT '.implode(',', $args) : '$other$';
                $this->sql_stmt = str_replace('$other$', $limit, $this->sql_stmt);
                break;
            case 'SETDATA':
                $set = !empty($args[0]) ? ' SET '.$args[0] : '';
                $this->sql_stmt = str_replace('$set$', $set, $this->sql_stmt);
                break;
        }
        return $this;
    }

    //获取正在执行的sql语句
    public function getLastSql(){
        return $this->lastsql;
    }

    //设置查询结果集类型
    public function setFetchType($type){
        $this->fetch_type = $type;
    }

    //获取错误信息
    public function getErrorInfo($writeLog = false){
        return $writeLog ? $this->log() : array_merge($this->error_info, array('sql'=>$this->lastsql));
    }

    //记录日志
    private function log(){
        try {
            $log = "[".date('Y-m-d H:i:s')."]\n";
            $log .= '执行语句：'.$this->lastsql."\n";
            $log .= '错误代码：'.$this->error_info[0]."\n";
            $log .= '错误类型：'.$this->error_info[1]."\n";
            $log .= '错误描述：'.$this->error_info[2]."\n\n";
            file_put_contents($this->log_path, $log, FILE_APPEND);
            return '';
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }




}