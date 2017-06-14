<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/14 0014
 * Time: 上午 10:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Transform_data_lib{

    /**
     * @desc 为键名缺失双引号的字符串，匹配补充双引号还原正常的json
     * @param $irrJson
     * @param bool $mode
     * @return mixed
     * @throws Exception
     */

    public function irregularJson($irrJson, $mode=TRUE){
        if( ! is_bool($mode) ){
            i_log_message('error',__CLASS__,__FUNCTION__,0,'mode只能是一个bool类型');
            throw new Exception('the functionName:'.__FUNCTION__.' under the className:'.__CLASS__.' had a invalid param, the param mode must be a boolean ');
        }
        if(preg_match('/\w:/', $irrJson)){
            $formalJson = preg_replace('/(\w+):/is', '"$1":', $irrJson);
        }
        return json_decode($formalJson, $mode);
    }

    /**
     * @desc 转换stdclass 对象为一个数组 或者一个json字符串
     * @param stdClass $stdObject
     * @param bool $mode
     * @return mixed|string
     * @throws Exception
     */

    public function stdClassTrans(stdClass $stdObject, $mode=TRUE){
        if( ! is_bool($mode) ){
            i_log_message('error',__CLASS__,__FUNCTION__,0,'mode只能是一个bool类型');
            throw new Exception('the functionName:'.__FUNCTION__.' under the className:'.__CLASS__.' had a invalid param, the param mode must be a boolean ');
        }
        return $mode === TRUE ? json_decode(json_encode($stdObject),$mode) : json_encode($stdObject);
    }

    /**
     * @desc 将一个字符串切割为二维数组，如果第一次切割为空，则返回一个为空的一维数组
     * @param $str
     * @param $cutterOuter
     * @param $cutterInside
     * @return array
     * @throws Exception
     */

    public function strToDyadicArr($str, $cutterOuter, $cutterInside){
        if( ! is_string($str) OR ! is_string($cutterOuter) OR ! is_string($cutterInside) ){
            throw new Exception('the param str,cutterOuter,cutterInside must be a string in the className:'.__CLASS__.' ->funcName:'.__FUNCTION__);
        }
        $container = (array)explode($cutterOuter,$str);
        if ( ! empty($container) ){
            foreach ($container as & $value){
                $value = (array)explode($cutterInside,$value);
            }
        }
        return $container;
    }
}
