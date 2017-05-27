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
    public $cache;
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct(CI_Cache &$cache)
	{
	    $this->cache = $cache;
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
     * @param $curClassName
     * @param $curMethodName
     * @param string $paraString    //请求参数字符串
     * @return string
     */
    public final static function getRedisKey($curClassName, $curMethodName, $paraString=""){
	    return $paraString=="" ? $curClassName.'>'.$curMethodName : $curClassName.'>'.$curMethodName.'?'.$paraString;
    }

    /**
     * @param $redisKey //存到到redis的键名
     * @param $data     //需要存储的数据
     * @param int $expirationTime   //设置该条存储的过期时间
     * @return boolean      返回处理结果 成功为true，失败是false
     */
    public final function redis_SaveData( $redisKey, $data, $expirationTime=0){
        $redisTimeExp = is_int($expirationTime)&&$expirationTime>0 ?  $expirationTime : CI_Config::$Conf["Redis"]["ExpirationTime"];
        return $this->cache->redis->save($redisKey,$data,$redisTimeExp);
    }
}
