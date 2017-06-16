<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/16 0016
 * Time: 上午 9:27
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class QiNiu extends CI_Controller{
    public function getQiNiuKeys(){
        $this->request_lib->checkReqData(['token']);
        $this->load->add_package_path(APPPATH.'third_party/qiNiu_sdk_7.1.3/')->library(['qi_niu']);
        $picNumber = isset($this->request_lib->reqData['picNumber']) ? $this->request_lib->reqData['picNumber'] : 1;
        if ( $picNumber > CI_Config::$Conf['Qiniu']['PicKeysMaxNum'] ){
            i_log_message('error',__CLASS__,__FUNCTION__,$this->request_lib->reqData['userId'],'一次请求获取过多的图片keys，请求的数量为：'.$picNumber);
            echo $this->response_msg_lib->jsonResp(60101);
            exit(0);
        }
        $picKeys = $this->qi_niu->getPicKeys($picNumber);
        $uploadToken = $this->qi_niu->getUploadToken();
        $respData = ['keys'=>$picKeys,'uploadToken'=>$uploadToken];
        echo $this->response_msg_lib->jsonResp(0,$respData);
    }
}