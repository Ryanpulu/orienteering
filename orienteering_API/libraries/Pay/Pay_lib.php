<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/1
 * Time: 16:13
 * Author Mail : ryanpulu@outlook.com
 */

class Pay_lib{

    /*
     * @desc 支付类名称定义套接字符串
     * @var string
     */

    const joint = '_';

    /*
     * @desc 该支付类目录名称
     * @var string
     */

    const DIR_NAME = 'Pay';

    /*
     * @desc 第三方支付类对象
     * @var object
     */

    private $_third_party_model;

    /*
     * @desc 服务类型 退款 ？ 取消订单 ？ 支付 ？ ....
     * @var string;
     */

    private $service;

    /*
     * @desc 第三方支付平台名称
     * @var string
     */

    private $_thirdParty;

    /*
     * @desc APP ? web ? 线下支付 ？
     * @var string
     */

    private $type;

    /*
     * @desc 希望补充的参数，以便支付子类应用这些参数,默认为null,接受 array
     * @var string
     */

    public $extra=null;

    /**
     * Pay_lib constructor.
     * @param $param
     * @desc
     */

    public function __construct()
    {

    }

    public function action($param){
        $this->_propertyAssign($param);
        $this->_third_party_model = $this->_loadThirdPartyPay();
        var_dump($this);
    }

    /*
     * @desc 加载第三方支付模型类，并实例化
     * @void $this->_third_party_model
     */

    private function _loadThirdPartyPay(){
        $thirdPartyClassPath = APPPATH.'libraries'.DIRECTORY_SEPARATOR.self::DIR_NAME.DIRECTORY_SEPARATOR.$this->_thirdParty.DIRECTORY_SEPARATOR.$this->type.DIRECTORY_SEPARATOR.$this->type.'.php';
        if( ! file_exists($thirdPartyClassPath) ){
            log_message('error', 'cannot find the pay file：'.$thirdPartyClassPath);
            show_error('cannot find the pay file：'.$thirdPartyClassPath);
        }
        return $this->_LoadThirdPartyClass($thirdPartyClassPath);
    }

    /**
     * @desc 加载，并实例化一个第三方支付类
     * @param $classFilePath
     * @param bool $loadStatus
     * @return mixed
     */
    private function _LoadThirdPartyClass($classFilePath, $loadStatus=FALSE){
        if( ! class_exists($this->_thirdParty.self::joint.$this->type) && $loadStatus===FALSE ){
            include($classFilePath);
            $loadStatus = TRUE;
            return $this->_LoadThirdPartyClass($classFilePath,$loadStatus);
        }elseif( $loadStatus === TRUE && ! class_exists($this->_thirdParty.self::joint.$this->type) ){
            log_message('error', 'Unable to locate the pay-thirdParty ：'.$this->_thirdParty.self::joint.$this->type);
            show_error('Unable to locate the pay-thirdParty ：'.$this->_thirdParty.self::joint.$this->type);
        }else{
            $thirdPartyClassName = $this->_thirdParty.self::joint.$this->type;
            return new $thirdPartyClassName();
        }
    }

    /**
     * @desc 为当前类属性赋值
     * @param $param
     */

    private function _propertyAssign($param){
        $this->service = isset($param['service']) ? $param['service'] : null;
        $this->_thirdParty = isset($param['thirdParty']) ? $param['thirdParty'] : null;
        $this->type = isset($param['type']) ? $param['type'] : null;
        $this->extra = isset($param['extra']) ? $param['extra'] : null;
    }

}