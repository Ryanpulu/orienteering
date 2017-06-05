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
        exit(1);
    }

    private function _getOneMapAssembly($mapDetail,$pointArrDetail,$mapLine){
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




}