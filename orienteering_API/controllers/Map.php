<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 14:33
 * Author Mail : ryanpulu@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Map extends CI_Controller {

    const limitDistance = 20;           //限制地图列表显示地图信息的限定距离，单位为km

    /**
     * @desc 获取单张地图活动详情
     */

    public function getOneMap(){
        $this->request_lib->checkReqData(['mapId']);
        $mapId = (integer)$this->request_lib->reqData["mapId"];
        $this->load->model(['activity_map_model','activity_point_model','map_line_model']);
        $mapDetail = $this->activity_map_model->getMapDetail($mapId);
        $mapPoint = $this->activity_point_model->getMapPoint($mapId);
        $pointArrDetail = $this->activity_point_model->getPointArrDetail($mapPoint);
        $mapLine = $this->map_line_model->getAllLine($mapId);
        $data = $this->_getOneMapAssembly($mapDetail,$pointArrDetail,$mapLine);
        echo $this->response_msg_lib->jsonResp(0,$data);
    }

    /**
     * @desc 获取活动地图详情列表
     */

    public function getMapList(){
        $this->request_lib->checkReqData();
        $this->load->model(['activity_map_model','activity_model']);
        $this->load->library('math_lib');
        $activityMapId = $this->activity_model->getMapIdOngoing();
        $coordinate = isset($this->request_lib->reqData['coordinate']) && $this->request_lib->reqData['coordinate'] != null ? $this->request_lib->reqData['coordinate'] : null;
        $order = isset($this->request_lib->reqData['order']) && $this->request_lib->reqData['order'] != null ? (integer)$this->request_lib->reqData['order'] : 0;
        $mapData = $this->activity_map_model->getAllMapList();
        $respData = $this->_getMapList($activityMapId,$mapData,$coordinate,$order);
        echo $this->response_msg_lib->jsonResp(0,$respData);
    }








    //--------------------  以下是处理数据显示逻辑函数，均为私有函数，不提供web访问  -------------------------------------
    //----------------------------------------------------  private   -------------------------------------------------------
    /**
     * @desc 获取单个地图详细信息
     * @param $mapDetail
     * @param $pointArrDetail
     * @param $mapLine
     * @return mixed
     */
    private function _getOneMapAssembly($mapDetail, $pointArrDetail, $mapLine){
        if( !$mapDetail OR !$pointArrDetail OR !$mapLine  ){
            i_log_message('error',__CLASS__,__FUNCTION__);
            echo $this->response_msg_lib->jsonResp(50001);
            exit(1);
        }else{
            foreach($mapLine as &$line){
                $lineArr = explode(',',$line['line']);
                if( ! is_array($lineArr) ){
                    i_log_message('error',__CLASS__,__FUNCTION__,0,'推荐路线解析不是一个数组');
                    echo $this->response_msg_lib->jsonResp(50001,'没有找到对应的');
                    exit(1);
                }else{
                    foreach($lineArr as $key=>$pointId){
                        if( ! isset($pointArrDetail[$pointId]) ){
                        }else{
                            if($key==0){
                                $line['pointId'] = (integer)$pointId;
                                $line['location'] = $pointArrDetail[$pointId]['location'];
                                $line['pic'] = $pointArrDetail[$pointId]['pic'];
                            }
                            $line['pointLineArr'][] = $pointArrDetail[$pointId];
                        }
                    }
                }
                unset($line['line']);
            }
            $response = json_decode(json_encode($mapDetail),TRUE);
            $response['lineArr'] = $mapLine;
            $response['pointArr'] = array_values($pointArrDetail);
            return $response;
        }
    }

    /**
     * @desc 获取地图列表信息接口数据显示逻辑处理部分
     * @param $activityMapId
     * @param $mapData
     * @param null $coordinate
     * @param int $order
     * @return array|null
     * @throws Exception
     */

    private function _getMapList($activityMapId, $mapData, $coordinate=null , $order=0){
        if( $coordinate != null && ! isset($this->math_lib) ){
            $this->load->library('math_lib');
        }
        $coordinate = $coordinate==null ? null : explode(';',$coordinate);      //获取限定的距离
        $limitDistance = isset($coordinate[1]) ? 1000*(integer)$coordinate[1] : self::limitDistance*1000;
        if($coordinate != null){
            $coordinateUser = isset($coordinate[0]) ? explode(',',$coordinate[0]) : explode(',',$coordinate);
            if( ! is_array($coordinateUser) OR ! isset($coordinateUser[0]) OR ! isset($coordinateUser[1]) ){
                i_log_message('error',__CLASS__,__FUNCTION__,0,implode('=',$this->request_lib->reqData));
                throw new Exception('the coordinate is invalid');
            }
        }
        foreach( $mapData as & $mapDataDetail ){
            if( ! isset($mapDataDetail['mapId']) ){
                throw new Exception(' the index mapId is undefined');
            }
            if($coordinate != null && isset($mapDataDetail['center'])){
                $coordinateMap = explode(',',$mapDataDetail['center']);
                $distance = (integer)$this->math_lib->mathDistanceCoor($coordinateMap[1],$coordinateMap[0],$coordinateUser[1],$coordinateUser[0]);
            }else{
                $distance = 0;
            }
            if($distance > $limitDistance){
                continue;
            }else{
                $mapDataDetail['distance'] = $distance;
            }
            $activityNum = 0;
            foreach($activityMapId as $mapId){
                if( (integer)$mapId == (integer)$mapDataDetail['mapId'] ){
                    ++$activityNum;
                }
            }
            $mapDataDetail['activityNum'] = $activityNum;
            $newMapData[] = $mapDataDetail;
        }
        unset($mapData);
        if( $order == 0){
            array_multisort(array_column($newMapData,'activityNum'),SORT_DESC,$newMapData);
        }else{
            array_multisort(array_column($newMapData,'distance'),SORT_ASC,$newMapData);
        }
        return isset($newMapData[$this->request_lib->reqData['pageStart']]) ? array_slice($newMapData,$this->request_lib->reqData['pageStart'],$this->request_lib->reqData['pageSize']) : null;
    }
}