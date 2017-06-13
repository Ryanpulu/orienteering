<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/13 0013
 * Time: 下午 5:45
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity extends CI_Controller{
    public function uploadPoint(){
        $this->request_lib->checkReqData(['activityId','pointTime']);
        $activityId = (integer)$this->request_lib->reqData['activityId'];
        $this->load->model(['activity_model','user_activity_model']);
        $activityDesc = $this->activity_model->getLineOngoing($activityId);
        print_r($activityDesc);
    }
}