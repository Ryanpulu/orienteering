<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12 0012
 * Time: 下午 6:09
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Dynamic extends CI_Controller{
    /**
     * @desc 获取动态列表接口
     */
    public function getDynamicList(){
        $this->request_lib->checkReqData();
        $this->load->model(['dynamic_model','userInfo_model','dynamic_comments_model','dynamic_praise_model']);
        $dynamicList = $this->dynamic_model->getList();
        $dynamicIdArr = array_keys($dynamicList);
        $userIdArr = array_column($dynamicList,'userId');
        $userInfo = $this->userInfo_model->getDyListUserInfo($userIdArr);
        $commentsNum = $this->dynamic_comments_model->mathDynamicNum($dynamicIdArr);
        $praiseDetail = $this->dynamic_praise_model->getPraiseAll($dynamicIdArr);
        $resqData = $this->_getDynamicList($userInfo,$dynamicList,$commentsNum,$praiseDetail);
        echo $this->response_msg_lib->jsonResp(0,$resqData);
    }

    /**
     * @desc 获取单个动态详情接口
     */
    public function getDynamicDetail(){
        $this->request_lib->checkReqData(['dynamicId']);
        $dynamicId = (integer)$this->request_lib->reqData['dynamicId'];
        $this->load->model(['dynamic_model','userInfo_model','dynamic_comments_model','dynamic_praise_model']);
        $respData = $this->_getDynamicDetail($dynamicId);
        echo $this->response_msg_lib->jsonResp(0,$respData);
    }

    public function giveThumbsUp(){
        $this->request_lib->checkReqData(['dynamicId','token']);
        $this->load->model(['dynamic_praise_model']);
        $userId = (integer)$this->request_lib->reqData['userId'];
        $dynamicId = (integer)$this->request_lib->reqData['dynamicId'];
        $praiseStatus = $this->dynamic_praise_model->ckUPraiseStatus($dynamicId,$userId);
        if( $praiseStatus === FALSE ){
            $res = $this->dynamic_praise_model->givePraiseNew($dynamicId,$userId);
            $status = 1;
        }else{
            $status = $praiseStatus == 0 ? 1 : 0;
            $res = $this->dynamic_praise_model->upPraise($dynamicId,$userId,$status);
        }
        if( ! $res){
            i_log_message('error',__CLASS__,__FUNCTION__,$userId,'数据更新错误，错误信息为：'.$this->db->i_error_info());
            echo $this->response_msg_lib->jsonResp(50005);
            exit(0);
        }else{
            $respData['praise_status'] = $status;
            echo $this->response_msg_lib->jsonResp(0,$respData);
        }
    }

    public function publish(){
        $this->request_lib->checkReqData(['token']);
        $this->load->model('dynamic_model');
        $userId = (integer)$this->request_lib->reqData['userId'];
        $pic = isset($this->request_lib->reqData['pic']) ? $this->request_lib->reqData['pic'] : '';
        $contents = isset($this->request_lib->reqData['contents']) ? $this->request_lib->reqData['contents'] : '';
        $picInfo = isset($this->request_lib->reqData['picInfo']) ? $this->request_lib->reqData['picInfo'] : '';
        if( empty($pic) && empty($contents) ){
            echo $this->response_msg_lib->jsonResp(30015);
            exit(0);
        } elseif ( ! $this->dynamic_model->publish($userId,$contents,$pic,$picInfo) ){
            i_log_message('error',__CLASS__,__FUNCTION__,$userId,'发布动态失败，更新数据错误信息：'.$this->db->i_error_info());
            echo $this->response_msg_lib->jsonResp(50005);
            exit(0);
        }else{
            echo $this->response_msg_lib->jsonResp(0);
        }
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

    private function _getDynamicDetail($dynamicId){
        $dynamicDet = $this->dynamic_model->getOneDetail($dynamicId);
        if( ! isset($dynamicDet['dynamicId']) OR empty($dynamicDet['dynamicId']) ){
            echo $this->response_msg_lib->jsonResp(30014);
            exit(0);
        }
        if( ! isset ($dynamicDet['userId']) ){
            throw new Exception('the index userId is undefined in the class:'.__CLASS__.'-func:'.__FUNCTION__);
        }
        $dyUserId = $dynamicDet['userId'];
        $dynamicId = $dynamicDet['dynamicId'];
        $userInfo = $this->userInfo_model->getDyDetUserInfo($dyUserId);
        $praiseUserIdArr = $this->dynamic_praise_model->getDyDetPraise($dynamicId);
        $praiseUser = $this->userInfo_model->getDyDetPraiseInfo($praiseUserIdArr);
        $commentDet = $this->dynamic_comments_model->getDyDetComments($dynamicId);
        $commentNum = $this->dynamic_comments_model->mathCommentNum($dynamicId);
        if( ! empty($commentDet) ){
            $writerIdArr = (array)array_column($commentDet,'writerId');
            $writerDet = $this->userInfo_model->getDyDetWriterInfo($writerIdArr);
            foreach ($commentDet as & $commentOneDet){
                if(isset($commentOneDet['writerId']) && isset($writerDet[$commentOneDet['writerId']]) ){
                    $commentOneDet['nickname'] = $writerDet[$commentOneDet['writerId']]['nickname'];
                    $commentOneDet['headIcon'] = $writerDet[$commentOneDet['writerId']]['headIcon'];
                }
            }
        }
        $dynamicDet['commentsNumber'] = $commentNum;
        $dynamicDet['praise_number'] = is_array($praiseUser) ? count($praiseUser) : 0;
        $dynamicDet['comments'] = $commentDet;
        $dynamicDet['nickname'] = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
        $dynamicDet['headIcon'] = isset($userInfo['headIcon']) ? $userInfo['headIcon'] : '';
        $dynamicDet['praiseUserNickname'] = implode(',',(array)array_column($praiseUser,'nickname'));
        $dynamicDet['praise_status'] = is_array($praiseUserIdArr) && isset($this->request_lib->reqData['userId']) && in_array($this->request_lib->reqData['userId'],$praiseUserIdArr) ? 1 : 0;
        return $dynamicDet;
    }
}

