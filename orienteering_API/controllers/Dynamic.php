<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12 0012
 * Time: 下午 6:09
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Dynamic extends CI_Controller{
    public function getDynamicList(){
        $this->request_lib->checkReqData();
        $this->load->model(['dynamic_model','userInfo_model','dynamic_comments_model','dynamic_praise_model']);
        $dynamicList = $this->dynamic_model->getList();
        $dynamicIdArr = array_keys($dynamicList);
        $userIdArr = array_column($dynamicList,'userId');
        $userInfo = $this->userInfo_model->getInfoFromIdArr($userIdArr,['userId','nickname','headIcon']);
        $commentsNum = $this->dynamic_comments_model->mathDynamicNum($dynamicIdArr);
        $praiseDetail = $this->dynamic_praise_model->getPraiseAll($dynamicIdArr);
        $resqData = $this->_getDynamicList($userInfo,$dynamicList,$commentsNum,$praiseDetail);
        echo $this->response_msg_lib->jsonResp(0,$resqData);
    }



    //--------------------  以下是处理数据显示逻辑函数，均为私有函数，不提供web访问  -------------------------------------
    //----------------------------------------------------  private   -------------------------------------------------------

    private function _getDynamicList($userInfo,array $dynamicList,$commentsNum,$praiseDetail){
        foreach ($dynamicList as & $value){
            $value['nickname'] = isset($userInfo[$value['userId']]) && isset($userInfo[$value['userId']]['nickname']) ? $userInfo[$value['userId']]['nickname'] : null;
            $value['headIcon'] = isset($userInfo[$value['userId']]) && isset($userInfo[$value['userId']]['headIcon']) ? $userInfo[$value['userId']]['headIcon'] : null;
            $value['praise_number'] = isset($praiseDetail[$value['dynamicId']]) ? count($praiseDetail[$value['dynamicId']]) : 0;
            $value['praise_status'] = isset($praiseDetail[$value['dynamicId']]) && is_array($praiseDetail[$value['dynamicId']]) && isset($this->request_lib->reqData['userId']) && in_array($this->request_lib->reqData['userId'],$praiseDetail[$value['dynamicId']]) ? 1 : 0;
            $value['commentsNumber'] = isset($commentsNum[$value['dynamicId']]) ? $commentsNum[$value['dynamicId']] : 0;
        }
        return array_values($dynamicList);
    }
}

