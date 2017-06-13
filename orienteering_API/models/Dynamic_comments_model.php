<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12
 * Time: 22:26
 */
class Dynamic_comments_model extends CI_Model{

    /*
     * @desc 该模型类操作表名
     * @var int
     */

    protected $table = 'dynamic_comments';

    /*
     * @desc 评论Id
     * @var int
     */

    private $commnetsId;

    /*
     * @desc 该评论指向的动态ID
     * @var int
     */

    private $dynamicId;

    /*
     * @desc 该动态所属用户Id
     * @var int
     */

    private $writerId;

    /*
     * @desc 该字段作废，请勿使用
     */

    private $responseId;

    /*
     * @desc 评论内容
     * @var string
     */

    private $contents;

    /*
     * @desc 评论发表时间
     * @var string
     */

    private $commnet_time;

    /*
     * @desc 标识，0 该评论有效，1 该评论无效或已删除
     * @var int
     */

    private $flag;

    /**
     * @desc 计算指定动态Id评论总数
     * @param array $dynamicIdArr
     * @return int/array
     */
    public function mathDynamicNum(array $dynamicIdArr){
        if( ! isset($dynamicIdArr[0])){
            return 0;
        }
        $dynamicIdFieldArr = [];
        foreach($dynamicIdArr as $dynamicId){
            $dynamicIdFieldArr[':dynamicId'.$dynamicId] = $dynamicId;
            $sumFieldArr[] = ' sum(case `dynamicId` when :dynamicId'.$dynamicId.' then 1 else 0 end ) as dynamicId'.$dynamicId;
        }
        $this->db->i_prepare('SELECT '.implode(',',$sumFieldArr).' FROM `dynamic_comments` WHERE `flag`=:flag ');
        $dynamicIdFieldArr[':flag'] = 0;
        $this->db->i_execute($dynamicIdFieldArr);
        return $this->db->i_fetchAll();
    }


}