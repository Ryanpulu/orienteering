<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/6
 * Time: 18:52
 * Author Mail : ryanpulu@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Pay extends CI_Controller{

    public function testPay(){
        $this->load->library('Pay/pay_lib');
        //$this->request_lib->checkReqData(['productId','service','trade_service'=>'Pay','thirdParty'=>'Ali','trade_type'=>'APP']);
        //$detail = ['goods_detail'=>'测试一下','goods_name'=>'哈哈','total_amount'=>'20.00','seller_id'=>''];
        $data = $this->pay_lib->action(['trade_service'=>'Pay','thirdParty'=>'Ali','trade_type'=>'APP','goods_detail'=>'测试一下','goods_name'=>'哈哈','total_amount'=>'20.00']);
        echo $this->response_msg_lib->jsonResp(0,$data);
    }

}