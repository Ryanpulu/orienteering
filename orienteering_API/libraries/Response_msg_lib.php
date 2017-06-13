<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/31
 * Time: 11:42
 * Author Mail : ryanpulu@outlook.com
 */
class Response_msg_lib{

    /*
     * @desc code数组
     * @var array
     */

    private static $CodeArr = array(
        0=>'SUCCESS',               //接口调用成功
        3=>'token错误',             //用户token错误，调用接口时传入了不存的token
        4=>'token过期',             //用户token失去时效，需要重新登录获取新的token
        5=>'参数缺失',              //已被废弃
        7=>'非法参数',              //请求参数缺失，或者传入了非法参数
        40003=>'您没有该操作权限',  //用户调用特别的接口时权限校验未通过，（通常为非法调用）
        50001=>'系统错误'           //系统发生了逻辑错误
    );

    /*
     * @desc 输出数据的字符编码格式
     * @var string
     */
    const Charset = 'utf-8';

    /**
     * @return array
     */
    public function getCodeMsgArr(){
        return self::$CodeArr;
    }

    /**
     * @return string
     */
    private function _jsonHeader(){
        return 'Content-type: application/json';
    }

    /**
     * @desc html header 头
     * @return string
     */
    private function _htmlHeader(){
        return 'Content-type: text/html';
    }

    /**
     * @desc charset header 头
     * @return string
     */
    private function _charsetHeader(){
        return 'charset='.self::Charset;
    }

    /**
     * @desc 输出header string
     * @param $headerStr
     */
    private function _respHeader($headerStr){
        header($headerStr);
    }

    /**
     * @param $code
     * @param null $data
     * @return string
     * @throws Exception
     */

    public function jsonResp($code, $data=null){
        $headerStr = implode(';',['json'=>$this->_jsonHeader(),'charset'=>$this->_charsetHeader()]);
        if(!array_key_exists($code,self::$CodeArr)){
            throw new Exception("该code码没有被设置");
        }
        $this->_respHeader($headerStr);
        return $data===null ? json_encode(['code'=>$code,'desc'=>self::$CodeArr[$code]]) : json_encode(['code'=>$code,'desc'=>self::$CodeArr[$code],'data'=>$data]);
    }
}


