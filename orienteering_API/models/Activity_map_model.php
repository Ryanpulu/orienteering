<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 14:49
 * Author Mail : ryanpulu@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity_map_model extends CI_Model{

    /*
     * @var string
     * @desc 该模型类操作数据库表名称
     */

    protected $table = 'activity_map';
    //'.$this->table.'

    /*
     * @desc 地图ID
     * @var int;
     */

    private $mapId;

    /*
     * @desc 地图名称
     * @var string;
     */

    private $name;

    /*
     * @desc 地图地点名称
     * @var string
     */

    private $locationName;

    /*
     * @desc 地图边界点坐标
     * @var string
     */

    private $bound;

    /*
     * @desc 地图中心店坐标
     * @var string
     */

    private $center;

    /*
     * @desc 地图图片
     * @var string
     */

    private $pic;

    /*
     * @desc
     * @var string
     */

    private $recommands;

    /*
     * @desc 地图趣味图图片地址
     * @var string
     */

    private $interestPic;

    /*
     * @desc 地图打印图图片地址
     * @var string
     */

    private $printPic;

    /**
     * @param $mapId
     * @return mixed
     */

    public function getMapDetail($mapId){
        $this->db->i_prepare('SELECT `mapId`,`name`,`locationName`,`bound`,`center`,`pic` FROM `activity_map` WHERE `mapId`=:mapId LIMIT :limit');
        $this->db->i_execute([":mapId"=>$mapId,":limit"=>1]);
        $res = $this->db->i_fetchObject();
        if( isset($res->pic) ){
            $res->pic = map_picAssembly($res->pic);
        }
        return $res;
    }


}

