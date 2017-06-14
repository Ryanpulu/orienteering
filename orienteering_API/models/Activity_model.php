<?php
/**
 * Created by PhpStorm.
 * User: Ryanp
 * Date: 2017/5/29
 * Time: 18:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity_model extends CI_Model {

    protected $table = "activity";

    /*
     * @desc activity idit
     * @var int
     */
    private $activityId=0;

    /*
     * @desc activity name
     * @var string
     */

    private $name=NULL;

    /*
     * @desc 活动所属用户ID
     * @var int
     */

    private $ownerId;

    /*
     * @desc 地图ID
     * @var int
     */

    private $mapId;

    /*
     * @desc 活动路线
     * @var string
     */

    private $line;

    /*
     * @desc 活动类型，1 个人赛 2 积分赛 3 团体赛
     * @var int
     */

    private $type;

    /*
     * @desc 活动开始时间
     * @var int
     */

    private $start;

    /*
     * @desc 活动结束时间
     * @var int
     */

    private $end;

    /*
     * @desc 积分赛限定时长
     * @var int
     */

    private $duration;

    /*
     * @desc 活动是否公开 0 私密活动，1 公开活动
     * @var int
     */

    private $isPublic;

    /*
     * @desc 活动已报名人数
     * @var int
     */

    private $entries;

    /*
     * @desc 活动评论
     * @var string
     */

    private $comments;

    /*
     * @desc 活动礼品
     * @var string
     */

    private $gift;

    /*
     * @desc 活动图片
     * @var string
     */

    private $pic;

    /*
     * @desc 活动常见时间
     * @var int
     */

    private $createTime;

    /*
     * @desc 对应当前活动ID信息修改时间
     * @var string
     */

    private $lastModifyTime;

    /*
     * @desc 活动参与者排名
     * @var string
     */

    private $rank;

    /*
     * @desc 活动数据ID
     * @var int
     */

    private $poild;

    /*
     * @desc 活动路线总长
     * @var int
     */

    private $totalDistance;

    /*
     * @desc 活动是否是推广 0 否 1 是
     * @var int
     */

    private $spread;

    /*
     * @desc 当前字段作废，不使用
     * @var int
     */

    private $isPrivate;//作废字段

    /*
     * @desc 活动报名费用
     * @var int
     */

    private $price;

    /*
     * @desc 活动创建者留下的联系电话
     * @var string
     */

    private $mobile;

    /*
     * @desc 活动等级ID，不同活动等级，拥有不同的特性
     * @var int
     */

    private $activityRankId;

    /*
     * @desc 标识活动是否有效，0 有效活动，1 无效活动
     * @var int
     */

    private $flag;

    /*
     * @desc 活动描述
     * @var string
     */

    private $description;

    /*
     * @desc 活动创建人留下的公司logo图标
     * @var string
     */

    private $companyLogo;

    public function getEnterPayDetail($activityId){
        //$this->db->i_prepare(' SELECT `name`,`price`,`ownerId`,`end`,`` ');
    }

    /**
     * @desc 获取正在进行的活动Id，并计算每个活动ID进行中活动的总数
     * @return mixed
     */

    public function getMapIdOngoing(){
        $curTime = time();
        $this->db->i_prepare(' SELECT `mapId` FROM `activity` WHERE `end` > :end AND `flag`=:flag AND `isPublic`=:isPublic ' );
        $this->db->i_execute([':flag'=>0,':isPublic'=>1,':end'=>$curTime]);
        return $this->db->i_fetchAll();
    }

    /**
     * @desc 获取进行中活动的路线,同时查询该活动类型，不同活动路线格式不同
     * @param $activityId
     * @return stdClass
     */
    public function getLineOngoing($activityId){
        $this->db->i_prepare('SELECT `line`,`type`,`duration` FROM `activity` WHERE `activityId`=:activityId AND `end` > :endTime AND `flag`=:flag LIMIT 1');
        $exeArr = [':activityId'=>$activityId,':endTime'=>time(),':flag'=>0];
        $this->db->i_execute($exeArr);
        $stdObject = $this->db->i_fetchObject();
        return $stdObject===FALSE ? new stdClass() : $stdObject;
    }

}

