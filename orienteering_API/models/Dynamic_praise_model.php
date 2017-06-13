<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/13 0013
 * Time: 上午 9:51
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Dynamic_praise_model extends CI_Model{

    /*
     * @var string
     * @desc 该模型类操作数据库表名称
     */

    protected $table = 'dynamic_praise';

    /*
     * @desc 点赞ID
     * @var int
     */

    private $praiseId;

    /*
     * @desc 该点赞指向的动态ID
     * @var int
     */

    private $dynamicId;

    /*
     * @desc 用户ID
     * @var int
     */

    private $userId;

    /*
     * @desc 标识，标识该点赞是否有效，或是否取消  1 点赞状态  |  0  点赞被取消
     * @var int
     */

    private $flag;


    /**
     * @desc 获取一个动态ID数组中的所有点赞详情
     * @param array $dynamicIdArr
     * @return array
     */

    public function getPraiseAll(array $dynamicIdArr){
        foreach($dynamicIdArr as $dynamicId){
            $fieldNameArr[':dynamicId'.$dynamicId] = $dynamicId;
        }
        if( ! isset($fieldNameArr) ){
            return [];
        }
        $this->db->i_prepare(' SELECT `dynamicId`,`userId` FROM `dynamic_praise` WHERE `dynamicId` in ('.implode(',',array_keys($fieldNameArr)).') AND `flag` = :flag');
        $fieldNameArr[':flag'] = 1;
        $this->db->i_execute($fieldNameArr);
        return $this->_eachPraise($this->db->i_fetchAll());
    }

    /**
     * @desc 遍历点赞数据数组，将dynamicId值作为index，点赞用户ID作为数组
     * @param array $data
     * @return array
     */
    private function _eachPraise(array $data){
        $newData = [];
        foreach ($data as $dynamicDetail) {
            if (!is_array($dynamicDetail) && !isset($dynamicDetail['dynamicId']) OR !isset($dynamicDetail['userId'])) {
                continue;
            } else {
                $newData[$dynamicDetail['dynamicId']][] = $dynamicDetail['userId'];
            }
        }
        return $newData;
    }
}