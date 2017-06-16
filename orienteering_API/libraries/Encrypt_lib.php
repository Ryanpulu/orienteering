<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/16 0016
 * Time: 下午 3:13
 */
class Encrypt_lib{

    /**
     * @desc md5加密十位时间戳,并截取所得字串，默认截取32位，即全部
     * @param int $length
     * @return bool|string
     */
    public function md5TimeRandStr($length = 32 ){
        return substr( md5( time() ),0,$length );
    }



}
