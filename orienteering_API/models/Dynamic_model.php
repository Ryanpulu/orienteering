<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12 0012
 * Time: 下午 5:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Dynamic_model extends CI_Model {

    /*
     * @desc 当前模型类操作表名称
     * @var string
     */

    protected $table = 'dynamic';

    /*
     * @desc 动态ID
     * @var int
     */

    private $dynamicId;

    /*
     * @desc 发表该条动态的用户ID
     * @var int
     */

    private $userId;

    /*
     * @desc 动态内容
     * @var string
     */

    private $contents;

    /*
     * @desc 动态图片
     * @var string
     */

    private $pic;

    /*
     * @desc 动态图片详情
     * @var string
     */

    private $picInfo;

    /*
     * @desc 该条动态浏览次数
     * @var string
     */

    private $numberRead;

    /*
     * @desc 该条动态发布时间
     * @var int
     */

    private $addtime;

    /*
     * @desc 点状态，1 不可用  0 正常
     * @var int   0 动态有效  1  无效
     */

    private $flag;

    /**
     * @desc 获取动态列表详情数据
     * @return array
     */

    public function getList(){
        $this->db->i_prepare(' SELECT `dynamicId`,`userId`,`contents`,`pic`,`picInfo`,`numberRead`,`addtime` FROM `dynamic` WHERE `flag`=:flag ORDER BY `addtime` DESC LIMIT :pageStart,:pageSize');
        $this->db->i_execute([':flag'=>0,':pageStart'=>$this->request_lib->reqData['pageStart'],':pageSize'=>$this->request_lib->reqData['pageSize']]);
        $dynamicDetail = $this->setIndex($this->db->i_fetchAll(),'dynamicId');
        if( is_array($dynamicDetail)){
            foreach($dynamicDetail as & $value){
                $value['pic'] = $this->_eachPic($value['pic'],$value['picInfo']);
                $value['shareUrl'] = $this->_getShareUrl($value['dynamicId']);
                unset($value['picInfo']);
            }
        }
        return $dynamicDetail;
    }

    /**
     * @param $pic
     * @param $picInfo
     * @return array|null
     */

    private function _eachPic($pic, $picInfo){
        if( $pic == null ){
            return null;
        }else{
            $picArr = (array)explode(',',$pic);
            $picInfo = (array)explode(';',$picInfo);
            $newPic = [];
            foreach($picArr as $key=>$value){
                $picRow = [];
                if( isset($picInfo[$key]) ){
                    $picInfoRow = explode(',',$picInfo[$key]);
                    if (is_array($picInfoRow) && isset($picInfoRow[0]) && isset($picInfoRow[1]) ){
                        $picRow['width'] = (integer)$picInfoRow[0];
                        $picRow['height'] = (integer)$picInfoRow[1];
                    }
                }
                $picPrimary = $this->_assemblyPic($value);
                $picRow['picPrimary'] = $picPrimary;
                $picRow['picMini'] = $picPrimary.'?'.CI_Config::$Conf['Dynamic']['PicMinQue'];
                $newPic[] = $picRow;
            }
            return $newPic;
        }
    }

    /**
     * @param $pic
     * @return string
     */

    private function _assemblyPic($pic){
        return CI_Config::$Conf['Dynamic']['PicDo'].DIRECTORY_SEPARATOR.$pic;
    }

    /**
     * @param $dynamicId
     * @return string
     */
    private function _getShareUrl($dynamicId){
        return CI_Config::$Conf['WebSite']['Protocol'].CI_Config::$Conf['Dynamic']['ShareUrl'].'?dynamicId='.$dynamicId;
    }
}



















