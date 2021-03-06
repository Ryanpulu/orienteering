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
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {
    /*
     * 模型类中的数据表名称
     *
     * @var string
     */
    protected $table;
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		log_message('info', 'Model Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string	$key
	 */
	public function __get($key)
	{
		// Debugging note:
		//	If you're here because you're getting an error message
		//	saying 'Undefined Property: system/core/Model.php', it's
		//	most likely a typo in your model code.
		return get_instance()->$key;
	}

    /**
     * @desc 如果要在子模型中使用该方法，则子模型中必须有魔法方法__set，或者使用该方法设置的属性是子模型中的一个公有属性
     * @notice 不建议使用该方法
     * @param $name
     * @param $value
     * @throws Exception
     */

    public final function setNature($name, $value){
        var_dump(ucfirst($this->table.'_model'));
        if( ! property_exists(ucfirst($this->table.'_model'),$name) ){
            throw new Exception("the nature $name in the class ".__CLASS__." is not declared ");
        }
        $this->$name = $value;
    }

    /**
     * @desc 为从数据库取出的数据建立以某个字段值为索引的数组
     * @param array $data
     * @param $indexName
     * @return array
     * @throws Exception
     */

    public final function setIndex(array $data, $indexName){
        $newData = [];
        foreach( $data as $value ){
            if ( ! isset($value["$indexName"]) ){
                continue;
            } else {
                $newData[$value["$indexName"]] = $value;
            }
        }
        unset($data);
        return $newData;
    }

    /**
     * @desc 获取一个某个数据库中某个表格的下一个自增ID
     * @param $table_name
     * @param string $db_name
     * @return mixed
     */
    public final function getTableUpdateTime($table_name, $db_name=''){
        $db_name = $db_name=='' && isset($this->db->database) ? $this->db->database : $db_name;
        $res = $this->db->query("SELECT `UPDATE_TIME` FROM `information_schema`.`TABLES` WHERE `information_schema`.`TABLES`.`TABLE_SCHEMA` = '$db_name' AND `information_schema`.`TABLES`.`TABLE_NAME` = '$table_name'");
        return $res->row()->UPDATE_TIME;
    }

}
