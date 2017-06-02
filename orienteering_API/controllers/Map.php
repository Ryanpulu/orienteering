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
        echo $this->response_msg_lib->jsonResp(0,$mapLine);
    }

    private function _getOneMapAssembly($mapDetail,$mapPoint,$pointArrDetail,$mapLine){
        if( !$mapDetail OR !$mapPoint OR !$pointArrDetail OR !$mapLine  ){
            i_log_message('Error',__CLASS__,__FUNCTION__);
            $this->response_msg_lib->jsonResp(50001);
        }else{
            foreach($mapLine as $line){
                $lineArr = explode(',',$line);
            }
        }
    }




}