<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/13 0013
 * Time: 下午 5:45
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity extends CI_Controller{
    /**
     * @desc 活动中上传点接口
     */
    public function uploadPoint(){
        $this->request_lib->checkReqData(['activityId','pointTime','token']);
        $this->load->model(['activity_model','user_activity_model']);
        $this->load->library('transform_data_lib');
        $activityId = (integer)$this->request_lib->reqData['activityId'];
        $userId = (integer)$this->request_lib->reqData['userId'];
        $pointTime = $this->transform_data_lib->strToDyadicArr($this->request_lib->reqData['pointTime'],',',':');
        $activityDesc = $this->transform_data_lib->stdClassTrans($this->activity_model->getLineOngoing($activityId));
        $userActivityDesc = $this->transform_data_lib->stdClassTrans($this->user_activity_model->getUpPointDesc($userId,$activityId));
        $data = $this->_uploadPoint($activityDesc,$userActivityDesc,$pointTime,$userId,$activityId);
        echo $this->response_msg_lib->jsonResp(0,$data);
    }











    //--------------------  以下是处理数据显示逻辑函数，均为私有函数，不提供web访问  -------------------------------------
    //----------------------------------------------------  private   -------------------------------------------------------


    /**
     * @desc 上传点接口逻辑处理部分
     * @param $activityDesc
     * @param $userActivityDesc
     * @param $userId
     * @param $activityId
     * @param array $pointTime
     * @return null|void
     */
    private function _uploadPoint($activityDesc, $userActivityDesc, array $pointTime, $userId, $activityId){
        if( empty($activityDesc) ){
            echo $this->response_msg_lib->jsonResp(30001);
            exit(0);
        }
        if( ! isset($userActivityDesc['attend']) OR $userActivityDesc['attend'] != 1 ){
            echo $this->response_msg_lib->jsonResp(30002);
            exit(0);
        }
        $data = [
            'activityDesc'      => & $activityDesc,
            'userActivityDesc'  => & $userActivityDesc,
            'pointTime'         => & $pointTime,
            'userId'            => & $userId,
            'activityId'        => & $activityId
        ];
        switch ( $activityDesc['type'] )
        {
            case 1:
                $respData = $this->_personalUpPoint($data);
                break;
            case 2:
                $respData = $this->_integralUpPoint($data);
                break;
            case 3:
                $respData = $this->_teamUpPoint($data);
                break;
            default:
                i_log_message('error',__CLASS__,__FUNCTION__,0,'错误的活动类型,活动ID:'.$this->request_lib->reqData['activityId'].'该活动类型为：'.$activityDesc['type']);
                echo $this->response_msg_lib->jsonResp(50001);
                exit(0);
        }
        return $respData;
    }

    /**
     * @desc 个人赛上传点接口逻辑
     * @param $data
     * @return array
     */
    private function _personalUpPoint($data){
        $lineArr = explode(',',$data['activityDesc']['line']);
        $reachedArr = is_null($data['userActivityDesc']['reach']) ? [] : (array)explode(',',$data['userActivityDesc']['reach']);
        $upPoint = $this->_getPersonalUpPoint($lineArr,$reachedArr,$data['pointTime']);
        //没有需要更新的点则返回null;
        if( ! empty($upPoint) ){
            if($upPoint['startTime'] > $upPoint['reachTime']){
                echo $this->response_msg_lib->jsonResp(30003);
                exit(0);
            }
            //reach 为空则表示，第一次上传点，开始寻宝
            $upStatus = isset($upPoint['pointArr']) && is_array($upPoint['pointArr']) && in_array($lineArr[count($lineArr)-1],$upPoint['pointArr']) ? 2 : 1;     //终点在上传点数组中则更新status 为 2 否则 为 1；
            $upReach = implode(',',$upPoint['pointArr']);
            $reach = empty($data['userActivityDesc']['reach']) ? $upReach : implode(',',$reachedArr).','.$upReach;
            $curStatus = empty($data['userActivityDesc']['reach']) ? 0 : 1;
            $score = count(explode(',',$reach)) * CI_Config::$Conf['Activity']['PersonalPointScore'];
            $startTimeFlag = $data['userActivityDesc']['startTime'] == null ? TRUE : FALSE;
            $upRes = $this->user_activity_model->upReached($data['userId'],$data['activityId'],$upPoint['startTime'],$reach,$upPoint['reachTime'],$upStatus,$curStatus,$score,$startTimeFlag);
            if( ! $upRes ){
                i_log_message('error',__CLASS__,__FUNCTION__,$data['userId'],'更新数据库错误,活动ID：'.$data['activityId'].' 数据库错误信息：'.$this->db->i_error_info());
                echo $this->response_msg_lib->jsonResp(50001);
                exit(0);
            }
        }
        $rank = $this->user_activity_model->getPersonalCurRank($data['activityId'],$data['userId']);
        if( ! is_int($rank) ){
            i_log_message('error',__CLASS__,__FUNCTION__,$data['userId'],'获取用户排名失败');
            echo $this->response_msg_lib->jsonResp(50001);
            exit(0);
        }
        return ["rank"=>$rank];
    }

    /**
     * @desc 积分赛上传点接口逻辑部分
     * @param $data
     * @return array
     */
    private function _integralUpPoint($data){
        $lineArr = array_keys($this->transform_data_lib->irregularJson($data['activityDesc']['line']));
        $reachedArr = is_null($data['userActivityDesc']['reach']) ? [] : (array)explode(',',$data['userActivityDesc']['reach']);
        $upPoint = $this->_getIntegrationUpPoint($lineArr,$reachedArr,$data['pointTime']);
        if( ! empty($upPoint) ){
            //reach 为空则表示，第一次上传点，开始寻宝
            $checkPointEnd = array_diff(array_diff($lineArr,(array)$reachedArr),$upPoint['pointArr']);
            $upStatus = empty($checkPointEnd) && isset($upPoint['pointArr']) && ! empty($upPoint['pointArr']) ? 2 : 1;     //终点在上传点数组中则更新status 为 2 否则 为 1；
            $upReach = implode(',',$upPoint['pointArr']);
            $reach = empty($data['userActivityDesc']['reach']) ? $upReach : implode(',',$reachedArr).','.$upReach;
            $curStatus = empty($data['userActivityDesc']['reach']) ? 0 : 1;
            $score = (count(explode(',',$reach)) - $upStatus ) * CI_Config::$Conf['Activity']['IntegralPointScore'];
            $startTimeFlag = $data['userActivityDesc']['startTime'] == null ? TRUE : FALSE;
            $upRes = $this->user_activity_model->upReached($data['userId'],$data['activityId'],$upPoint['startTime'],$reach,$upPoint['endTime'],$upStatus,$curStatus,$score,$startTimeFlag);
            if( ! $upRes ){
                i_log_message('error',__CLASS__,__FUNCTION__,$data['userId'],'更新数据库错误,活动ID：'.$data['activityId'].' 数据库错误信息：'.$this->db->i_error_info());
                echo $this->response_msg_lib->jsonResp(50001);
                exit(0);
            }
        }
        $rank = $this->user_activity_model->getIntegralCurRank($data['activityId'],$data['userId']);
        if( ! is_int($rank) ){
            i_log_message('error',__CLASS__,__FUNCTION__,$data['userId'],'获取用户排名失败');
            echo $this->response_msg_lib->jsonResp(50001);
            exit(0);
        }
        return ['rank'=>$rank];
    }

    /**
     * @desc 团体赛
     * @param $data
     */
    private function _teamUpPoint($data){
        echo $this->response_msg_lib->jsonResp(40009);
        exit(0);
    }

    /**
     * @desc 个人赛中，比较已到达点，活动所有点，上传点，得出需要更新的到达点信息
     * @param array $line
     * @param array $reach
     * @param array $pointTimeArr
     * @return array|null
     */
    private function _getPersonalUpPoint(array $line, array $reach, array $pointTimeArr){
        $unReach = array_diff($line,$reach);
        foreach ($unReach as $unReachPoint){
            $flag = FALSE;
            foreach($pointTimeArr as $pointTime){
                if( isset($pointTime[0]) && isset($pointTime[1]) && (integer)$pointTime[1] == $unReachPoint ){
                    $upPointArr[] = $pointTime[1];
                    $starTime = isset($starTime) && $starTime != null ? $starTime : $pointTime[0];
                    $reachTime = $pointTime[0];
                    $flag = TRUE;
                    break;
                }
            }
            if ( ! $flag ){
                break;
            }
        }
        return isset($reachTime) ? ['pointArr'=>$upPointArr,'startTime'=>$starTime,'reachTime'=>$reachTime] : [];
    }

    /**
     * @desc 积分赛待更新点获取
     * @param array $line
     * @param array $reach
     * @param array $pointTimeArr
     * @return array
     */

    private function _getIntegrationUpPoint(array $line, array $reach, array $pointTimeArr){
        $unReach = array_diff($line,$reach);
        foreach($pointTimeArr as $pointDetail){
            if( isset($pointDetail[0]) && isset($pointDetail[1]) && in_array($pointDetail[1],$unReach) ){
                if( isset($upReach[$pointDetail[0]]) ){
                    i_log_message('error',__CLASS__,__FUNCTION__,0,'传入了重复pointId的数据');
                    echo $this->response_msg_lib->jsonResp(30003);
                    exit(0);
                }
                $upReachOne['pointId'] = $pointDetail[1];
                $upReachOne['reachTime'] = $pointDetail[0];
                $upReach[$pointDetail[1]] = $upReachOne;
            }
        }
        if ( isset($upReach) && array_multisort(array_column($upReach,'reachTime'),SORT_ASC,$upReach) ){
            $reachTime = array_column($upReach,'reachTime');
            $upReach['startTime'] = $reachTime[0];
            $upReach['endTime'] = array_pop($reachTime);
            $upReach['pointArr'] = array_column($upReach,'pointId');
            return $upReach;
        }else{
            return [];
        }
    }
}