<?php
/**
 * 数据库操作类
 *
 * @copyright (c) Hcent All Rights Reserved
 * $Id: Mysql.php 1796 2012-03-05 11:58:06Z hcent $
 */

/**
 * MYSQL数据操方法封装类
 */

class Db_Mysql {
	/**
	 * 内部数据连接对象
	 * @var resourse
	 */
	private $conn;

	/**
	 * 内部数据结果
	 * @var resourse
	 */
	private $result;

	/**
	 * 内部实例对象
	 * @var object MySql
	 */
	private static $_instance = array();

	/**
	 * 构造函数
	 */
    public function __construct($id='default') {
    	if (!function_exists('mysql_connect')) {
			$this->error_msg('not function:mysql_connect!');
		}
        
        $conf = App_Info::config('DB_CONFIG');
        $id = 'mysql_'.$id;
        if(!isset($conf[$id])){
            $this->error_msg('not mysql config:'.$id);
        }
		$conf = $conf[$id];
		if (!$this->conn = mysql_connect($conf['host'].':'.$conf['port'], $conf['user'], $conf['pswd'])) {
			$this->error_msg("db connect error:".mysql_error());
		}
		
		if ($this->get_mysql_version() > '4.1') {
			mysql_query("SET NAMES '".$conf['charset']."'");
		}
		@mysql_select_db($conf['db'], $this->conn) || $this->error_msg("not found database:".$conf['db']);
    }

    /**
	 * 静态方法，返回数据库连接实例
	 */
    public static function get_instance($id='default') {
        if (!isset(self::$_instance[$id])) {
            self::$_instance[$id] = new Db_Mysql($id);
        }
        return self::$_instance[$id];
    }
    
    /**
     * 返回数据库连接
     * 从http服务器读取配置，格式： SetEnv DB_$dbid PDO_MYSQL/$ip/$port/$db/$user/$passwd/$charset
     * @param   mixed   $dbid   数据库连接代号
     * @return  object  Zend_Db对象
     */
    public static function getDb($dbid) {
		if (Esun_ConfigCenter::isExsitConfigcenter()) {
			$confinfo = Esun_ConfigCenter::getconfig("DB_{$dbid}");
		} else {
			$confinfo = $_SERVER['DB_'.$dbid];
		}
    
    	$conf = explode('/',$confinfo);
		$params = array(
			'host'      => $conf[1],
			'port'      => (int)$conf[2],
			'dbname'    => $conf[3],
			'username'  => $conf[4],
			'password'  => $conf[5],
			'charset'   => $conf[6],
			'options'   => array(Zend_Db::AUTO_QUOTE_IDENTIFIERS => false)
		);
		if (preg_match('/PDO_MSSQL/i', $conf[0])) {
			$params['pdoType'] = 'dblib';
			$params['options'][Zend_Db::CASE_FOLDING] = Zend_Db::CASE_UPPER;
		} elseif (preg_match('/ORACLE/i', $conf[0])) {
			if ($conf[1] && $conf[2])
			$params['dbname'] = "//{$conf[1]}:{$conf[2]}/{$conf[3]}";
		}
		$db = Zend_Db::factory($conf[0], $params);
		if (preg_match('/PDO_MYSQL/i', $conf[0])) {
			$db->query("SET NAMES ".$conf[6]);
			$db->query("set session transaction isolation level read committed");
		} elseif (preg_match('/ORACLE|PDO_OCI/i', $conf[0])) {
			$db->query("alter session set nls_date_format='yyyy-mm-dd hh24:mi:ss'");
		}

		return $db;
    }

	/**
	 * 关闭数据库连接
	 */
	function close() {
		return mysql_close($this->conn);
	}

	/**
	 * 发送查询语句
	 *
	 */
	function query($sql, $data = array()) {
		$xsql = $this->prepare($sql, $data);
        if(!$xsql){
            $this->error_msg("Prepare SQL Error: " .mysql_error());
            return NULL;
        }
        
		$result = mysql_query($xsql, $this->conn);
		$this->query_count++;
		if (!$result) {
			$this->error_msg("SQL Query Error: " .mysql_error());
			return array();
		}else {
			return $this->fetch_result($result);
		}
	}
	
	/**
	 * 过滤特殊字符
	 */
	function escape_by_ref($string){
		if ($this->conn)
			return mysql_real_escape_string($string, $this->conn);
		else
			return addslashes($string);
	}
	
	/**
	 * 预处理sql语句
	 */
	function prepare( $query = null ) { // ( $query, *$args )
		if ( is_null( $query ) )
			return;
		
		$args = func_get_args();
		array_shift( $args );
		
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		$query = str_replace( "'%s'", '%s', $query ); 
		$query = str_replace( '"%s"', '%s', $query ); 
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query );
		array_walk( $args, array( &$this, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}
	
	/**
	 * 执行包括更新、删除、插入等操作
	 */
	function execute($sql, $data = array()){
		$sql = $this->prepare($sql, $data);
		$result = mysql_query($sql, $this->conn);
		
		if (!$result) {
            //$this->error_msg("SQL执行错误!");
			$this->error_msg("SQL execute error:<br />".mysql_error());
			return 0;
		}else {
			return mysql_affected_rows();
		}
	}
	

	/**
	 * 从结果集中取得结果集
	 *
	 */
	function fetch_result($result) {
		$ret = array();
        while($row = mysql_fetch_assoc($result)){
             $ret[] = $row;
        }
		return $ret;
	}
	
	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 */
	function insert_id() {
		return mysql_insert_id($this->conn);
	}

	
	/**
	 * 取得数据库版本信息
	 */
	function get_mysql_version() {
		return mysql_get_server_info();
	}
    
    private function error_msg($msg){
        throw new Core_Exception($msg, App_Key::$ERR_DB);
    }
}
