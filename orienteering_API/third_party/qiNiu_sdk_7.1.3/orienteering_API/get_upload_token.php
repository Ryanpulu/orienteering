<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/16 0016
 * Time: 下午 12:03
 */
defined('BASEPATH') OR exit('No direct script access allowed');
use Qiniu\Auth;

function get_upload_token($AccessKey,$SecretKey,$bucket){
    $auth = new Auth($AccessKey, $SecretKey);
    return $auth->uploadToken($bucket);
}
