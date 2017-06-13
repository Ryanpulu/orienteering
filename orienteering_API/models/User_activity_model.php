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


}