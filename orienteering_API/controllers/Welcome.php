<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        //$this->load->model('user_token_model');
        //var_dump($this->user_token_model->getUserToken(41));
        //$this->request_lib->checkReqData();
        //var_dump($this->request_lib->reqData);

        //$this->load->model('user_token_model');
        //print_r($this->user_token_model->getUserToken());
        //echo $this->response_msg->jsonResp(3);
        //var_dump($this->input);
	}

	/*public function testPay(){
        $this->load->library('Pay/pay_lib');
        //$this->request_lib->checkReqData(['activityId','']);
        $detail = ['goods_detail'=>'测试一下','goods_name'=>'哈哈','total_amount'=>'20.00','seller_id'=>''];
        $data = $this->pay_lib->action(['trade_service'=>'Pay','thirdParty'=>'Ali','trade_type'=>'APP'],$this->request_lib->reqData);
        echo $this->response_msg_lib->jsonResp(0,$data);
    }*/
}
