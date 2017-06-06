<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/6
 * Time: 10:10
 * Author Mail : ryanpulu@outlook.com
 */
abstract class AliPay extends pay_lib{
    const public_key_file_name = 'rsa_public_key.pem';
    const private_key_file_name = 'rsa_private_key.pem';
    public abstract function action_pay($paramArr, $biz_content); //执行支付抽象方法
    protected abstract function getKeyPath();     //获取keyPath路径
    protected abstract function initProperty($paramArr,$biz_content);
    protected abstract function signArrAssembly();  //拼接签名数组

    /**
     * @desc 获取签名orderString
     * @param $signArr
     * @param $keyPath
     * @return string
     */

    protected function getSignOrderStr($signArr, $keyPath){
        $this->signArrSort($signArr);   //对待签名数组进行ascii升序排序
        $signStr = $this->signStrAssembly($signArr);
        $signArr['sign'] = $this->getSign($signStr,$keyPath);
        return $this->orderStrAssembly($signArr);
    }

    /**
     * @desc 按字母ascii排序数组
     * @param $signArr
     */

    protected function signArrSort(& $signArr){
        ksort($signArr);
        reset($signArr);
    }

    /**
     * @desc 签名方法
     * @param $signStr
     * @param $keyPath
     * @return string
     * @throws Exception
     */
    protected function getSign($signStr, $keyPath){
        if( ! file_exists($keyPath) ){
            i_log_message('error',__CLASS__,__FUNCTION__,0,'没有找到指定的key');
            throw new Exception('the key file '.$keyPath.' is not exits');
        }
        $priKey = file_get_contents($keyPath);//私钥文件路径
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
//        $res = openssl_get_privatekey($priKey);
        $res = openssl_pkey_get_private($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($signStr, $sign, $res);
        //释放资源
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * @desc  待签名字符串拼接
     * @param $signArr
     * @return string
     */
    protected function signStrAssembly($signArr){
        $signStrArrTemp = [];
        foreach ($signArr as $key=>$value){
            if($key=='sign' OR $value==null){
                continue;
            }
            $signStrArrTemp[] = $key.'='.$value;
        }
        return implode('&',$signStrArrTemp);
    }

    /**
     * @desc orderString 字符串拼接
     * @param $signArr
     */

    protected function orderStrAssembly($signArr){
        $signStrArrTemp = [];
        foreach ($signArr as $key=>$value){
            if($key=='sign' OR $value==null){
                $signStrArrTemp[] = $key.'='.$value;
                continue;
            }
            $signStrArrTemp[] = $key.'='.urlencode($value);
        }
        return implode('&',$signStrArrTemp);
    }

}
