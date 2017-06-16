<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/31
 * Time: 10:44
 * Author Mail : ryanpulu@outlook.com
 */
class Request_lib{

    /*
     * @desc CodeIgniter 对象
     * @var object
     */

    protected  $CI;

    /*
     * @desc 检查数据完整性字段,默认为null即该接口不要求任何参数传入
     * @var null
     */

    private $ckDataFiled = null;

    /*
     * @desc 接口请求数据数组
     * @var array
     */

    public $reqData;

    /**
     * Request_lib constructor.
     */
    public function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * @desc 检查数据是否合法
     * @param array|null $ckDataFiled
     */
    public final function checkReqData(array $ckDataFiled=null){
        $_req_method = $this->CI->input->method();
        $_req_data = $_req_method == 'post' ? $this->CI->input->post() : null;
        $_req_data = $_req_method == 'get' ? $this->CI->input->get() : $_req_data;
        $this->reqData = $_req_data;
        $this->ckDataFiled = $ckDataFiled;
        //当需要token，客户端没有上传时，返回token错误
        if( is_array($this->ckDataFiled) && in_array('token',$this->ckDataFiled) && ! isset($this->reqData['token']) ){
            echo $this->CI->response_msg_lib->jsonResp(3);
            exit(0);
        }
        if( ! $this->_ckReqDataRational() ){
            if( ! isset($this->CI->response_msg_lib)){
                $this->CI->load->library('response_msg_lib');
            }
            echo $this->CI->response_msg_lib->jsonResp(7);
            i_log_message('error',__CLASS__,__FUNCTION__,0);
            die();
        }
        if(isset($this->reqData['token']) && ! $this->_checkToken() ){
            die();
        }
        $this->_trimData($this->reqData);
        //对传入的数据进行trim操作
        //检查page是否设定，没有则给定配置文件config.toml中的默认值
        $this->reqData['pageNumber'] = isset($this->reqData['pageNumber']) ? $this->reqData['pageNumber']  : CI_Config::$Conf["Api"]['PageNumber'];
        $this->reqData['pageSize'] = isset($this->reqData['pageSize']) ? $this->reqData['pageSize']  : CI_Config::$Conf["Api"]['PageSize'];
        $this->reqData['pageStart'] = ($this->reqData['pageNumber'] - 1) * $this->reqData['pageSize'];
    }

    /**
     * @desc 检查数据完整性，以及数据合理性
     * @return bool
     */
    private function _ckReqDataRational(){
        if( count($this->ckDataFiled) == 0){
            return true;
        }
        foreach($this->ckDataFiled as $key=>$value){
            if(is_array($value) && isset($this->reqData[$key]) && in_array($this->reqData[$key],$value) ){
                continue;
            }elseif ( ! is_array($value) ){
                if( ( is_int($key) && isset($this->reqData[$value]) ) OR ( is_string($key) && isset($this->reqData[$key]) && $this->reqData[$key] == $value  ) ){
                   continue;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     * @desc 对请求数据进行trim操作
     * @param $data
     */
    private function _trimData(& $data){
        if (is_array($data)){
            foreach ($data as & $value){
                $value = trim($value);
            }
            array_filter($data);    //删除为空的无效字段
        }
    }

    /**
     * @desc 检查token
     * @return bool
     * @throws Exception
     */
    private function _checkToken(){
        if( ! isset($this->CI->user_token_model)){
            $this->CI->load->model('user_token_model');
        }
        $userToken = $this->CI->user_token_model->getUserToken($this->reqData['token']);
        if($userToken==false){
            echo $this->CI->response_msg_lib->jsonResp(3);
            return false;
        }elseif (isset($userToken->expiration_time) && time() > $userToken->expiration_time){
            echo $this->CI->response_msg_lib->jsonResp(4);
            return false;
        }
        if( ! isset($userToken->userID) ){
            throw new Exception('missing the userId in the class user_token method getUserToken');
        }
        $this->reqData['userId'] = $userToken->userID;
        return true;
    }

}
