<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12 0012
 * Time: 下午 6:32
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class UserInfo_model extends CI_Model{

    /*
     * @var string
     * @desc 该模型类操作数据库表名称
     */

    protected $table = 'userInfo';

    /*
     * @desc 用户ID
     * @var int
     */

    private $userId;

    /*
     * @desc 用户电话
     * @var string
     */

    private $mobile;

    /*
     * @desc 用户昵称
     * @var string
     */

    private $nickname;

    /*
     * @desc 用户性别 0 女 | 1 男 | 2 保密
     * @var int
     */

    private $gender;

    /*
     * @desc 用户生日日期
     * @var string
     */

    private $birthday;

    /*
     * @desc 用户身高
     * @var string
     */

    private $height;

    /*
     * @desc 用户体重
     * @var string
     */

    private $weight;

    /*
     * @desc 用户QQ
     * @var string
     */

    private $qq;

    /*
     * @desc 用户积分
     * @var int
     */

    private $score;

    /*
     * @desc 用户金币
     * @var int
     */

    private $gold;

    /*
     * @desc 用户银币
     * @var int
     */

    private $silver;

    /*
     * @desc 用户头像地址 七牛
     * @var string
     */

    private $headIcon;

    /*
     * @desc 用户头像md5值
     * @var string
     */

    private $headIconMd5;

    /*
     * @desc 用户签名
     * @var string
     */

    private $signature;

    /*
     * @desc 时间 坐标  格式 -》time;lng,lat
     * @var string
     */

    private $timeLocation;

    /*
     * @desc 兴趣爱好 以逗号分隔
     * @var string
     */

    private $interset;

    /*
     * @desc 环信ID
     * @var string
     */

    private $hxId;

    /*
     * @desc 地图数据ID
     * @var int
     */

    private $poiId;

    /*
     * @desc 运动总距离  单位为m
     * @var int
     */

    private $totalDistance;

    /*
     * @desc 活动ID 正在参与的活动
     * @var int
     */

    private $activityId;

    /*
     * @desc 活动结束时间
     * @var int
     */

    private $activityEnd;

    /*
     * @desc 用户活动推广次数
     * @var int
     */

    private $spreadNumber;

    /**
     * @param array $userIdArr
     * @return array
     */

    public function getDyListUserInfo(array $userIdArr){
        foreach( $userIdArr as $value ){
            $fieldNameArr[':userId'.$value] = $value;
        }
        if( ! isset($fieldNameArr) ){
            return [];
        }
        $this->db->i_prepare(' SELECT `userId`,`nickname`,`headIcon` FROM `userInfo` WHERE `userId` in('.implode(',',array_keys($fieldNameArr) ).') ');
        $this->db->i_execute($fieldNameArr);
        $data =  $this->setIndex($this->db->i_fetchAll(),'userId');
        return $this->_eachHdIcon($data);
    }

    public function getDyDetPraiseInfo(array $userIdArr){
        foreach( $userIdArr as $value ){
            $fieldNameArr[':userId'.$value] = $value;
        }
        if( ! isset($fieldNameArr) ){
            return [];
        }
        $this->db->i_prepare('SELECT `userId`,`nickname` FROM `userinfo` WHERE `userId` in('.implode(',',array_keys($fieldNameArr)).') ');
        $this->db->i_execute($fieldNameArr);
        return $this->db->i_fetchAll();
    }

    /**
     * @desc 获取单个动态详情中所有者信息
     * @param $userId
     * @return mixed
     */
    public function getDyDetUserInfo($userId){
        $this->db->i_prepare(' SELECT `userId`,`nickname`,`headIcon` FROM `userInfo` WHERE `userId` = :userId LIMIT 1 ');
        $this->db->i_execute([':userId'=>$userId]);
        $det = $this->db->i_fetch();
        if( isset($det['headIcon']) ){
            $det['headIcon'] = $this->_hdIconAssembly($det['headIcon']);
        }
        return $det;
    }

    public function getDyDetWriterInfo(array $userIdArr){
        foreach( $userIdArr as $value ){
            $fieldNameArr[':userId'.$value] = $value;
        }
        if( ! isset($fieldNameArr) ){
            return [];
        }
        $this->db->i_prepare('SELECT `userId`,`nickname`,`headIcon` FROM `userinfo` WHERE `userId` in('.implode(',',array_keys($fieldNameArr)).') ');
        $this->db->i_execute($fieldNameArr);
        $res =  $this->db->i_fetchAll();
        return empty($res) ? [] : $this->setIndex($this->_eachHdIcon($res),'userId');
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */

    private function _eachHdIcon(array $data){
        foreach ($data as & $value){
            if ( ! isset($value['headIcon']) ){
                throw new Exception(' the index headIcon is undefined in the class: '.__CLASS__.' func:'.__FUNCTION__);
            }else{
                $value['headIcon'] = $this->_hdIconAssembly($value['headIcon']);
            }
        }
        return $data;
    }

    /**
     * @param $headIcon
     * @return string
     */

    private function _hdIconAssembly($headIcon){
        return ((stristr($headIcon,CI_Config::$Conf['WebSite']['Protocol']) === false) && $headIcon!=null) ? CI_Config::$Conf['User']['HeadIconDo'].DIRECTORY_SEPARATOR.$headIcon : $headIcon;
    }
}

