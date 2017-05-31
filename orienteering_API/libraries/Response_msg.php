<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/31
 * Time: 11:42
 * Author Mail : ryanpulu@outlook.com
 */
class Response_msg{

    /*
     * @desc code数组
     * @var array
     */

    const CodeArr = array(
        3=>'token错误',
        4=>'token过期',
        5=>'参数缺失',
        7=>'非法参数',
        40003=>'您没有该操作权限',
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
        return self::CodeArr;
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
     * @param int $code
     * @return string
     * @throws Exception
     */
    public function jsonResp($code,$data=null){
        $headerStr = implode(';',['json'=>$this->_jsonHeader(),'charset'=>$this->_charsetHeader()]);
        if(!array_key_exists($code,self::CodeArr)){
            throw new Exception("该code码没有被设置");
        }
        $this->_respHeader($headerStr);
        return $data===null ? json_encode(['code'=>$code,'desc'=>self::CodeArr[$code]]) : json_encode(['code'=>$code,'desc'=>self::CodeArr[$code],'data'=>$data]);
    }
}


