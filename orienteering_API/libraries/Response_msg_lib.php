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
     * 30000-40000              //客户端请求数据异常导致程序错误
     * 40000-50000              //服务端主动拒绝服务导致的错误
     * 50000-60000              //系统错误
     * 60000-70000              //第三方错误
     */

    private static $CodeArr = array(
        0=>"SUCCESS",                                                   //接口调用成功
        3=>"token错误",                                                 //用户token错误，调用接口时传入了不存的token
        4=>"token过期",                                                 //用户token失去时效，需要重新登录获取新的token
        5=>"参数缺失",                                                  //已被废弃
        7=>"非法参数",                                                  //请求参数缺失，或者传入了非法参数
        30001=>"无效的活动",                                            //请求活动类接口时，请求到了一个已经失去时效或不存在的活动则返回该状态码
        30003=>"非法数据",                                              //客户端传入了非法数据，导致服务器拒绝处理
        30002=>"您还没有报名该活动，无法开始寻宝",                      //用户没有报名该活动时，返回该状态码
        30014=>"该动态不存在或已被删除",                                //用户请求到一条已经被删除的动态时，返回该状态码
        30015=>'发布内容非法',                                          //用户请求发布一个内容和图片均为空的动态，此时拒绝处理，返回该状态码
        40003=>"您没有该操作权限",                                      //用户调用特别的接口时权限校验未通过，（通常为非法调用）
        40009=>"服务暂未开通,敬请期待",                                 //当客户端请求了开发中的服务时，返回该状态码
        50001=>"系统错误",                                              //系统发生了逻辑错误
        50005=>"系统繁忙",                                              //系统更新数据时出错返回该状态码
        60101=>"请求获取图片key过多",                                   //请求七牛上传图片keys时，一次请求获取的量超出服务器允许的范围
        60103=>"没有该图片空间",                                        //七牛储存中，请求了不存在的bucket_name
        99999=>""
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
        return $data===null ? json_encode(["code"=>$code,"desc"=>"".self::$CodeArr[$code]]) : json_encode(["code"=>$code,"desc"=>self::$CodeArr[$code],"data"=>$data]);
    }
}


