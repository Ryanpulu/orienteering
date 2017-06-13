<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 15:28
 * Author Mail : ryanpulu@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity_point_model extends CI_Model{

    /*
     * @desc 当前模型类操作表名称
     * @var string
     */

    protected $table = 'activity_point';

    /*
     * @desc 点ID
     * @var int
     */

    private $pointId;

    /*
     * @desc 所属地图ID
     * @var int
     */

    private $mapId;

    /*
     * @desc 点名称
     * @var string
     */

    private $name;

    /*
     * @desc 点坐标
     * @var string
     */

    private $location;

    /*
     * @desc 点坐标
     * @var string
     */

    private $dxLocation;

    /*
     * @desc 点图片
     * @var string
     */

    private $pic;

    /*
     * @desc 点状态，1 不可用  0 正常
     * @var int
     */

    private $status=0;


    public function getMapPoint($mapId){
        $this->db->i_prepare(' SELECT `pointId` FROM `activity_point` WHERE `mapId`=:mapId AND `status`=:status');
        $this->db->i_execute([":mapId"=>$mapId,":status"=>$this->status]);
        $pointIdResArr = $this->db->i_fetchAll();
        if($pointIdResArr != FALSE){
            $pointIdArr = array();
            foreach($pointIdResArr as $value){
                $pointIdArr[] = $value['pointId'];
            }
        }else{
            $pointIdArr = $pointIdResArr;
        }
        return $pointIdArr;
    }

    /**
     * @desc 查询一个pointId数组中的所有点信息
     * @param array $pointArr
     * @return mixed
     */
    public function getPointArrDetail(array $pointArr){
        foreach($pointArr as $key => $pointId){
            $pointIdArr[':pointId'.$key] = $pointId;
        }
        $pointIdStr = implode(',',array_keys($pointIdArr));
        $pdoExeArr = $pointIdArr;
        $pdoExeArr[":status"]=$this->status;
        $this->db->i_prepare(' SELECT `pointId`,`location`,`pic` FROM `activity_point` WHERE `pointId` IN ('.$pointIdStr.') AND `status` = :status' );
        $this->db->i_execute($pdoExeArr);
        $pointIdArr =  $this->db->i_fetchAll();
        if(is_array($pointIdArr)){
            $pointIdArrKey = [];
            foreach($pointIdArr as $value){
                $pointIdArrKey[$value['pointId']] = $value;
                $pointIdArrKey[$value['pointId']]['pic'] = activity_picAssembly($value['pic']);
            }
        }else{
            $pointIdArrKey = $pointIdArr;
        }
        return $pointIdArrKey;
    }

}
