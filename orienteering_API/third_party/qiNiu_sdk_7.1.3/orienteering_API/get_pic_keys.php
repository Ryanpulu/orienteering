<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/16 0016
 * Time: 下午 3:33
 */
defined('BASEPATH') OR exit('No direct script access allowed');
use Qiniu\Auth;
function get_pic_keys($bucket_name,$picNumber=1,$length=28){
    if( ! is_int($picNumber) ){
        throw new Exception('the param picNumber must be an int in the func:'.__FUNCTION__.'--class:'.__CLASS__);
    }
    if( ! is_int($length) OR $length > 32 ){
        throw new Exception('the param length is invalid in the func:'.__FUNCTION__.'--class:'.__CLASS__);
    }
    $resData = [];
    for ($i = 0;$i<$picNumber;$i++){
        $resData[] = strtoupper( substr( md5(time().$bucket_name.mt_rand()),0,$length ) );
    }
    return $resData;
}