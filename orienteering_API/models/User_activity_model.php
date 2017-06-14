<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/13 0013
 * Time: 下午 5:23
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * 该模型操作表格 user_activity  表格储存用户对应活动的状态，是否报名参与，是否开始了寻宝，等等
 */
class User_activity_model extends CI_Model{

    /*
     * @desc 当前模型类操作表名称
     * @var string
     */

    protected $table = 'user_activity';

    /*
     * @desc 自增ID
     * @var int
     */

    private $id;

    /*
     * @desc 用户ID
     * @var int
     */

    private $userId;

    /*
     * @desc 活动ID
     * @var int
     */

    private $activityId;

    /*
     * @desc 参与状态  1 参与 0 未参与
     * @var int
     */

    private $attend;

    /*
     * @desc 是否已经开始活动，0 未开始，1 进行中，2 结束 3 失败
     * @var int
     */

    private $status;

    /*
     * @desc 开始时间
     * @var int
     */

    private $startTime;

    /*
     * @desc 完成活动的用户的轨迹路线
     * @var string
     */

    private $track;

    /*
     * @desc 用户到达的点 点以逗号分隔
     * @var int
     */

    private $reach;

    /*
     * @desc 用户完成活动到达终点的时间
     * @var int
     */

    private $reachTime;

    /*
     * @desc 备注
     * @var string
     */

    private $desc;

    /*
     * @desc 最后操作该条信息的时间
     * @var date
     */

    private $lastModifyTime;

    /*
     * @desc 用户对应该活动活动的积分
     * @var int
     */

    private $score;

    /*
     * @desc 用户参与该活动的用时
     * @var int
     */

    private $elapsed;

    /*
     * @desc 用户获得的礼物信息
     * @var string
     */

    private $gift;

    /*
     * @desc 用户在该活动中的排名
     * @var int
     */

    private $myRank;

    /*
     * @desc 用户该活动走过的里程数
     * @var string
     */

    private $mileage;

    /*
     * @desc 用户报名该活动对应的订单号 免费活动该值为null
     * @var string
     */

    private $out_trade_no;

    /*
     * @desc status可取的值
     * @var array
     */

    private static $statusLimit = [0,1,2,3];

    private static $typeLimit = [1,2,3];

    /**
     * @desc 查询用户特定活动信息
     * @param $userId
     * @param $activityId
     * @return mixed
     */

    public function getUpPointDesc($userId, $activityId){
        $this->db->i_prepare(' SELECT `attend`,`status`,`startTime`,`reach`,`score` FROM `user_activity` WHERE `userId`=:userId AND `activityId`=:activityId LIMIT 1');
        $this->db->i_execute([':userId'=>$userId,':activityId'=>$activityId]);
        $resObject = $this->db->i_fetchObject();
        return $resObject === FALSE ? new stdClass() : $resObject;
    }

    /**
     * @desc 对已经开始寻宝的活动上传点
     * @param $userId
     * @param $activityId
     * @param $startTime
     * @param $reach
     * @param $reachTime
     * @param $upStatus
     * @param $curStatus
     * @param $score
     * @param $starTimeFlag
     * @return bool
     * @throws Exception
     */
    public function upReached($userId, $activityId, $startTime, $reach, $reachTime, $upStatus, $curStatus, $score, $starTimeFlag=FALSE){
        if( ! in_array($upStatus,self::$statusLimit) OR ! in_array($curStatus,self::$statusLimit) ){
            throw new Exception('the param upStatus is invalid in the className:'.__CLASS__.'-funcName:'.__FUNCTION__);
        }
        $elapsed = $reachTime - $startTime;
        $exeData = [
            'score'         =>  $score,
            ':reach'        =>  $reach,
            ':reachTime'    =>  $reachTime,
            ':elapsed'      =>  $elapsed,
            ':upStatus'     =>  $upStatus,
            ':userId'       =>  $userId,
            ':activityId'   =>  $activityId,
            ':attend'       =>  1,
            ':curStatus'    =>  $curStatus
        ];
        if( $starTimeFlag ){
            $this->db->i_prepare(' UPDATE `user_activity` SET `score`=:score,`startTime`=:startTime,`reach`=:reach,`reachTime`=:reachTime,`elapsed`=:elapsed,`status`=:upStatus WHERE `userId`=:userId AND `activityId`=:activityId AND `attend`=:attend AND `status`=:curStatus LIMIT 1');
            $exeData[':startTime'] = $startTime;
        }else{
            $this->db->i_prepare(' UPDATE `user_activity` SET `score`=:score,`reach`=:reach,`reachTime`=:reachTime,`elapsed`=:elapsed,`status`=:upStatus WHERE `userId`=:userId AND `activityId`=:activityId AND `attend`=:attend AND `status`=:curStatus LIMIT 1');
        }
        $res = $this->db->i_execute($exeData);
        return $res && $this->db->i_rowCount()>0 ? TRUE : FALSE;
    }

    /**
     * @desc 成功获取排名返回整数，失败则为null
     * @param $activityId
     * @param $userId
     * @return int|null|string
     */
    public function getPersonalCurRank($activityId, $userId){
        $this->db->i_prepare(' SELECT `userId` FROM `user_activity` WHERE `activityId`=:activityId AND `attend`=:attend ORDER BY `score` DESC,`elapsed` ASC');
        $this->db->i_execute([':activityId'=>$activityId,':attend'=>1]);
        $resData =  $this->db->i_fetchAll();
        $rank = null;
        if( ! empty($resData) ){
            foreach ($resData as $key => $value){
                if($value['userId'] == $userId){
                    $rank =  $key + 1;
                    break;
                }
            }
        }
        return $rank;
    }

    public function getIntegralCurRank($activityId, $userId){
        $this->db->i_prepare(' SELECT `userId` FROM `user_activity` WHERE `activityId`=:activityId AND `attend`=:attend ORDER BY `score` DESC,`elapsed` ASC');
        $this->db->i_execute([':activityId'=>$activityId,':attend'=>1]);
        $resData =  $this->db->i_fetchAll();
        $rank = null;
        if( ! empty($resData) ){
            foreach ($resData as $key => $value){
                if($value['userId'] == $userId){
                    $rank =  $key + 1;
                    break;
                }
            }
        }
        return $rank;
    }

}