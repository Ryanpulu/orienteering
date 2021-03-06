<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 2.1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PDO Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_driver extends CI_DB {

	/**
	 * Database driver
	 *
	 * @var	string
	 */
	public $dbdriver = 'pdo';

	/*
	 * PDO::statement 对象
	 *
	 */
	public $stat;

	/**
	 * PDO Options
	 *
	 * @var	array
	 */
	public $options = array();

	// --------------------------------------------------------------------
    /*
     * PDO execute status
     *
     * @var boolean
     */
    private $i_status = TRUE;
	/**
	 * Class constructor
	 *
	 * Validates the DSN string and/or detects the subdriver.
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if (preg_match('/([^:]+):/', $this->dsn, $match) && count($match) === 2)
		{
			// If there is a minimum valid dsn string pattern found, we're done
			// This is for general PDO users, who tend to have a full DSN string.
			$this->subdriver = $match[1];
			return;
		}
		// Legacy support for DSN specified in the hostname field
		elseif (preg_match('/([^:]+):/', $this->hostname, $match) && count($match) === 2)
		{
			$this->dsn = $this->hostname;
			$this->hostname = NULL;
			$this->subdriver = $match[1];
			return;
		}
		elseif (in_array($this->subdriver, array('mssql', 'sybase'), TRUE))
		{
			$this->subdriver = 'dblib';
		}
		elseif ($this->subdriver === '4D')
		{
			$this->subdriver = '4d';
		}
		elseif ( ! in_array($this->subdriver, array('4d', 'cubrid', 'dblib', 'firebird', 'ibm', 'informix', 'mysql', 'oci', 'odbc', 'pgsql', 'sqlite', 'sqlsrv'), TRUE))
		{
			log_message('error', 'PDO: Invalid or non-existent subdriver');

			if ($this->db_debug)
			{
				show_error('Invalid or non-existent PDO subdriver');
			}
		}

		$this->dsn = NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Database connection
	 *
	 * @param	bool	$persistent
	 * @return	object
	 */
	public function db_connect($persistent = FALSE)
	{
		if ($persistent === TRUE)
		{
			$this->options[PDO::ATTR_PERSISTENT] = TRUE;
		}

		try
		{
			$pdoObj =  new PDO($this->dsn, $this->username, $this->password, $this->options);
			$pdoObj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);   //设置关闭本地预处理
			return $pdoObj;
		}
		catch (PDOException $e)
		{
			if ($this->db_debug && empty($this->failover))
			{
				$this->display_error($e->getMessage(), '', TRUE);
			}

			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Database version number
	 *
	 * @return	string
	 */
	public function version()
	{
		if (isset($this->data_cache['version']))
		{
			return $this->data_cache['version'];
		}

		// Not all subdrivers support the getAttribute() method
		try
		{
			return $this->data_cache['version'] = $this->conn_id->getAttribute(PDO::ATTR_SERVER_VERSION);
		}
		catch (PDOException $e)
		{
			return parent::version();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	$sql	SQL query
	 * @return	mixed
	 */
	protected function _execute($sql)
	{
		return $this->conn_id->query($sql);
	}

    /**
     * PDO prepare
     * @param $sql
     */
    public function i_prepare($sql){
        $this->stat = $this->conn_id->prepare($sql);
    }

    /**
     * @param array $bindArr
     * @return mixed
     * @throws Exception
     */
    public function i_execute(array $bindArr){
        if($this->stat===null){
            throw new Exception("stat is not initialized");
        }
        $this->i_status = $this->stat->execute($bindArr);
        return $this->i_status;
    }

    /**
     * PDO fetchAll
     * @return mixed
     * @param $fetchAssoc
     * @throws Exception
     */
    public function i_fetchAll($fetchAssoc=TRUE){
        if($this->stat===null){
            throw new Exception("stat is not initialized or the stat execute is false");
        }
        $_data = $fetchAssoc ? $this->stat->fetchAll(PDO::FETCH_ASSOC) : $this->stat->fetchAll();
        $this->init_Stat();
        return $_data;
    }

    public function i_fetch($fetchAssoc=TRUE){
        if($this->stat === null){
            throw new Exception("stat is not initialized or the stat execute is false");
        }
        $_data = $fetchAssoc ? $this->stat->fetch(PDO::FETCH_ASSOC) : $this->stat->fetch();
        $this->init_stat();
        return $_data;
    }


    /**
     * PDO fetchObject
     * @return mixed
     * @throws Exception
     */
    public function i_fetchObject(){
        if($this->stat===null){
            throw new Exception("stat is not initialized or the stat execute is false");
        }
        $_data = $this->stat->fetchObject();
        $this->init_Stat();
        return $_data;
    }

    /**
     * @desc 返回上一条sql语句影响的行数
     * @return mixed
     * @throws Exception
     */
    public function i_rowCount(){
        if($this->stat===null){
            throw new Exception("stat is not initialized or the stat execute is false");
        }
        return $this->stat->rowCount();
    }

    /**
     * init stat,exStatus   //初始化属性stat ，exStatus ，避免影响其他模型类操作中的判断
     */
    private function init_Stat(){
        $this->stat = null;
    }
	// --------------------------------------------------------------------

    /**
     *
     * 开启一个事务
     */
    public function i_trans_start(){
        $this->conn_id->beginTransaction();
    }

    /**
     *
     * 回滚一个事务
     */
    private function i_trans_rollback(){
        $this->conn_id->rollBack();
    }

    /**
     *
     * 提交一个事务
     */
    private function i_trans_commit(){
        $this->conn_id->commit();
    }

    /**
     * 返回最近一次数据库操作的错误信息
     * @return mixed
     */

    public function i_error_info(){
        return json_encode($this->conn_id->errorInfo());
    }


    /**
     * 获取跟数据库句柄上一次操作相关的 SQLSTATE
     * @return mixed
     */
    public function i_error_code(){
        return $this->conn_id->errorCode();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @return mixed
     */
    public function i_last_insertId($name=NULL){
        return $this->conn_id->lastInsertId($name);
    }

    /**
     * 完成一个事务 将事务结果返回
     * @return bool
     */
    public function i_trans_complete(){
        if($this->i_status === FALSE){
            $this->i_trans_rollback();
        }else{
            $this->i_trans_commit();
        }
        return $this->i_status;
    }

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_begin()
	{
		return $this->conn_id->beginTransaction();
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_commit()
	{
		return $this->conn_id->commit();
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	protected function _trans_rollback()
	{
		return $this->conn_id->rollBack();
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependent string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		// Escape the string
		$str = $this->conn_id->quote($str);

		// If there are duplicated quotes, trim them away
		return ($str[0] === "'")
			? substr($str, 1, -1)
			: $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return is_object($this->result_id) ? $this->result_id->rowCount() : 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @param	string	$name
	 * @return	int
	 */
	public function insert_id($name = NULL)
	{
		return $this->conn_id->lastInsertId($name);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _field_data($table)
	{
		return 'SELECT TOP 1 * FROM '.$this->protect_identifiers($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Returns an array containing code and message of the last
	 * database error that has occurred.
	 *
	 * @return	array
	 */
	public function error()
	{
		$error = array('code' => '00000', 'message' => '');
		$pdo_error = $this->conn_id->errorInfo();

		if (empty($pdo_error[0]))
		{
			return $error;
		}

		$error['code'] = isset($pdo_error[1]) ? $pdo_error[0].'/'.$pdo_error[1] : $pdo_error[0];
		if (isset($pdo_error[2]))
		{
			 $error['message'] = $pdo_error[2];
		}

		return $error;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the TRUNCATE statement,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'TRUNCATE TABLE '.$table;
	}

}
