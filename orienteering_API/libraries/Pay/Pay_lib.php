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
     * @desc 应用当前CI
     * @var object
     */

    public $CI;

    /*
     * @desc 支付类名称定义套接字符串
     * @var string
     */

    const joint = '_';

    /*
     * @desc 调用支付请求函数前缀名
     * @var string
     */

    const action_func_prefix = 'action_';

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

    /*
     * @desc 支付类APP名称，默认为LeXun
     * @var string
     */

    protected $appName;

    /**
     * Pay_lib constructor.
     * @param $param
     * @desc
     */

    public function __construct()
    {
        $this->CI = & get_instance();
    }

    public function action(array $param){
        $this->_propertyAssign($param);
        $this->_third_party_model = $this->_loadThirdPartyPay();
        $Conf = CI_Config::$Conf['Pay'][(string)ucfirst($this->_thirdParty)][(string)ucfirst($this->type)][(string)ucfirst($this->appName)];
        $Conf['appName'] = $this->appName;
        $actFuncName = self::action_func_prefix.strtolower($this->service);
        if( ! method_exists($this->_third_party_model,$actFuncName) ){
            throw new Exception(' the request pay method '.$actFuncName.' is not exist');
        }
        return  $this->_third_party_model->$actFuncName($Conf,$param);
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
        //检查是否有加载第三方支付抽象父类
        if( ! class_exists($this->_thirdParty.'Pay') ){
            $thirdFilePath = APPPATH.'libraries'.DIRECTORY_SEPARATOR.self::DIR_NAME.DIRECTORY_SEPARATOR.$this->_thirdParty.DIRECTORY_SEPARATOR.$this->_thirdParty.'Pay.php';
            if( file_exists($thirdFilePath) ){
                include($thirdFilePath);
            }else{
                i_log_message('error',__CLASS__,__FUNCTION__,0,'没有找到'.$thirdFilePath.'文件');
                throw new Exception('the thirdPartyPay file is not exits');
            }
        }
        //加载第三方支付类
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
        $this->service = isset($param['trade_service']) ? $param['trade_service'] : null;
        $this->_thirdParty = isset($param['thirdParty']) ? $param['thirdParty'] : null;
        $this->type = isset($param['trade_type']) ? $param['trade_type'] : null;
        $this->appName = isset($param['appName']) ? $param['appName'] : 'LeXun';
        $this->extra = isset($param['extra']) ? $param['extra'] : null;
    }

}