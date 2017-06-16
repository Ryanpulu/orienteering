<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/16 0016
 * Time: 上午 11:18
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Qi_niu {

    /*
     * @desc qiNiuSDK根目录
     */

    public $rootDir;

    /*
     * QiNiu 下 APP_name
     */

    const APP_NAME = 'orienteering_API';

    /*
     * @desc 引用CI对象
     * @var object
     */

    public $CI;

    /*
     * @desc bucket_name  索引值
     * @var string
     */

    public $bucket_name_ind;

    /**
     * @desc 自动加载autoload文件
     * Qi_niu_autoload constructor.
     */

    public function __construct()
    {
        $this->CI = & get_instance();

        $root_dir = $this->getRootDir();

        $this->rootDir = & $root_dir;

        require($root_dir.DIRECTORY_SEPARATOR.'autoload.php');

        $this->ini_properties($this->CI->request_lib->reqData);
    }

    /**
     * @desc 获取七牛根目录路径
     * @return string
     */

    public final function getRootDir(){
        return APPPATH.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'qiNiu_sdk_7.1.3';
    }

    /**
     * @desc 获取七牛AccessKey
     * @return mixed
     */

    public static final function getAccessKey(){
        return CI_Config::$Conf['Qiniu']['AccessKey'];
    }

    /**
     * @desc 获取bucketName
     * @return mixed
     * @throws Exception
     */

    public final function getBucketName(){
        $Bucket_Name_Arr = [
            'AT'    =>  'activitypic',           //活动图片上传bucket_name
            'DY'    =>  'dynamics-pic',          //动态图片上传bucket_name
            'HD'    =>  'headIcon',              //头像图片上传bucket_name
            'MP'    =>  'mappic',                //地图图片上传bucket_name
            'TS'    =>  'test'                   //测试图片上传bucket_name
        ];
        if( ! isset($Bucket_Name_Arr[$this->bucket_name_ind]) ){
            i_log_message('error',__CLASS__,__FUNCTION__,0,'请求了不存在的bucket_name:'.$this->bucket_name_ind);
            echo $this->CI->response_msg_lib->jsonResp(60103);
            exit(0);
        }
        return $Bucket_Name_Arr[$this->bucket_name_ind];
    }

    /**
     * @desc 获取七牛SecretKey
     * @return mixed
     */

    public static final function getSecretKey(){
        return CI_Config::$Conf['Qiniu']['SecretKey'];
    }

    /**
     * @desc 获取上传token
     * @return string
     */

    public function getUploadToken(){
        if( ! function_exists('get_upload_token') ){
            require($this->rootDir.DIRECTORY_SEPARATOR.self::APP_NAME.DIRECTORY_SEPARATOR.'get_upload_token.php');
        }
        return get_upload_token(self::getAccessKey(),self::getSecretKey(),self::getBucketName($this->bucket_name_ind));
    }

    /**
     * @desc 获取图片Keys
     * @param $picNumber
     * @param int $length
     * @return array
     */
    public function getPicKeys($picNumber, $length=28){
        if ( ! function_exists('get_pic_keys') ){
            require($this->rootDir.DIRECTORY_SEPARATOR.self::APP_NAME.DIRECTORY_SEPARATOR.'get_pic_keys.php');
        }
        return get_pic_keys(self::getBucketName($this->bucket_name_ind),(integer)$picNumber,$length);
    }

    /**
     * @desc 对象初始化
     * @param array $reqData
     */
    public function ini_properties(array $reqData){
        $this->bucket_name_ind = isset($reqData['bucket_name']) ? $reqData['bucket_name'] : 'DY';
    }

}