<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 11:41
 * Author Mail : ryanpulu@outlook.com
 */
class Ali_APP extends AliPay {
    const method = 'alipay.trade.app.pay';
    const format = 'JSON';
    const type = 'APP';
    const version = '1.0';
    const product_code = 'QUICK_MSECURITY_PAY';
    private $paramMaxLen = [
        'body'=>128,
        'subject'=>256,
        'out_trade_no'=>64,
        'timeout_express'=>6,
        'total_amount'=>9,
        'seller_id'=>16,
        'product_code'=>64,
        'goods_type'=>2,
        'passback_param'=>512,
        'promo_params'=>512,
        'enable_pay_channels'=>128,
        'disable_pay_channels'=>128,
        'store_id'=>32
    ];
    private $keyPath;
    private $app_id = null;
    private $charset = null;
    private $sign_type = null;
    private $sign = null;
    private $timestamp = null;
    private $notify_url = null;
    private $biz_content = null;


    /**
     * @param $paramArr
     * @param null $biz_content
     */
    public function action_pay($paramArr, $biz_content)
    {
        $this->initProperty($paramArr,$biz_content);
        return $this->getSignOrderStr($this->signArrAssembly(),$this->getKeyPath().parent::private_key_file_name);
    }

    /**
     * @return mixed
     */
    protected function getKeyPath()
    {
        return $this->keyPath;
    }

    /**
     * @初始化 支付类属性值
     * @param $paramArr
     * @param null $biz_content
     */

    protected function initProperty($paramArr, $biz_content){
        $this->app_id = isset($paramArr['AppId']) ? $paramArr['AppId'] : null;
        $this->charset = isset($paramArr['Charset']) ? $paramArr['Charset'] : null;
        $this->sign_type = isset($paramArr['SignType']) ? $paramArr['SignType'] : null;
        $this->notify_url = isset($paramArr['NotifyUrl']) ? $paramArr['NotifyUrl'] : null;
        if($biz_content!=null){
            $this->biz_content = $this->_getGoodsDetail($biz_content);
        }
        $appName = isset($paramArr['appName']) ? $paramArr['appName'] : 'LeXun';
        $this->keyPath = APPPATH.'key'.DIRECTORY_SEPARATOR.'pay'.DIRECTORY_SEPARATOR.strtolower($appName).DIRECTORY_SEPARATOR.'ali'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR;
        $this->timestamp = (string)time();
    }

    /**
     * @desc 拼装biz_content数组
     * @param $biz_content
     */
    private function _getGoodsDetail($reqData){
        $biz_content = [];
        $biz_content['body'] = isset($reqData['goods_detail']) ? $reqData['goods_detail'] : null;
        $biz_content['subject'] = isset($reqData['goods_name']) ? $reqData['goods_name'] : null;
        $biz_content['out_trade_no'] = isset($reqData['out_trade_no']) ? $reqData['out_trade_no'] : null;
        $biz_content['timeout_express'] = isset($reqData['timeout_express']) ? $reqData['timeout_express'] : null;
        $biz_content['total_amount'] = isset($reqData['total_amount']) ? $reqData['total_amount'] : null;
        $biz_content['seller_id'] = isset($reqData['seller_id']) ? $reqData['seller_id'] : null;
        $biz_content['product_code'] = self::product_code;
        $biz_content['goods_type'] = isset($reqData['goods_type']) ? $reqData['goods_type'] : null;
        $biz_content['passback_param'] = isset($reqData['passback_param']) ? $reqData['passback_param'] : null;
        $biz_content = array_filter($biz_content);  //取出为空的无效元素
        $this->_checkBizContent($biz_content);
        return json_encode($biz_content);
        //$biz_content['promo_params'] = isset($biz_content['promo_params']) ? $biz_content['promo_params'] : null;
    }

    /**
     * @desc 检查biz_content参数长度是否合法
     * @param $biz_content
     */
    private function _checkBizContent($biz_content){
        foreach($biz_content as $key=>$value){
            if( isset($this->paramMaxLen[$key]) && isset($value{$this->paramMaxLen[$key]+1}) ){
                i_log_message('error',__CLASS__,__FUNCTION__,0,$key.' 对应的值超过了指定长度___值为：'.$value);
                echo $this->CI->response_msg_lib->jsonResp(7);
                exit(1);
            }
        }
    }


    /**
     * @return array
     */
    protected function signArrAssembly(){
        $signArr = [
            'app_id'=>$this->app_id,
            'method'=>self::method,
            'format'=>self::format,
            'charset'=>$this->charset,
            'sign_type'=>$this->sign_type,
            'timestamp'=>$this->timestamp,
            'version'=>self::version,
            'notify_url'=>$this->notify_url,
            'biz_content'=>$this->biz_content
        ];
        return $signArr;
    }

}
