<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 16:37
 * Author Mail : ryanpulu@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Map_line_model extends CI_Model{
    /*
     * @desc 当前类操作的数据库表名称
     * @var string
     */
    protected $table = 'map_line';

    /*
     * @desc 路线ID
     * @var int
     */

    private $lineId;

    /*
     * @desc 地图ID
     * @var int
     */

    private $mapId;

    /*
     * @desc 路线
     * @var string
     */

    private $line;

    /*
     * @desc 路线类型，1 个人赛路线 2 团队赛路线
     * @var int
     */

    private $type;

    /*
     * @desc 路线是否有效 0 有效 ，1 无效
     * @var int
     */

    private $flag=0;

    public function getAllLine($mapId){
        $this->db->i_prepare('SELECT `line`,`type` FROM `map_line` WHERE `mapId`=:mapId AND `flag`=:flag ');
        $this->db->i_execute([":mapId"=>$mapId,":flag"=>$this->flag]);
        return $this->db->i_fetchAll();
    }
}
