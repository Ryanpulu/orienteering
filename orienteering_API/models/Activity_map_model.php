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

    /**
     * @desc 查询所有地图信息
     * @return mixed
     */

    public function getAllMapList(){
        $mapData = $this->cache->redis->get( getRedisKey(__CLASS__,__FUNCTION__) );
        if($mapData == FALSE){
            $this->db->i_prepare('SELECT `center`,`locationName`,`mapId`,`name`,`pic` FROM `activity_map`');
            $this->db->i_execute([]);
            $mapData = $this->db->i_fetchAll();
            $this->cache->redis->save( getRedisKey(__CLASS__,__FUNCTION__),$mapData );
        }
        return is_array($mapData) ? $this->_setMapIdIndex($mapData) : null;
    }

    /**
     * @desc 设置地图ID为数据index键名索引mysql数据
     * @param array $mapData
     * @return array
     * @throws Exception
     */
    private function _setMapIdIndex(array $mapData){
        $newData = [];
        foreach($mapData as $value){
            if( ! isset($value['mapId']) ){
                throw new Exception('mapId is undefined in the class:'.__CLASS__.' func: '.__FUNCTION__);
            }else{
                $newData[$value['mapId']] = $value;
            }
        }
        unset($mapData);
        return $newData;
    }
}

