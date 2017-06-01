<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/26
 * Time: 16:27
 * Author Mail : ryanpulu@outlook.com
 */
class Example_model extends CI_Model {
    public function check_redis(){
        //i_log_message("access",__CLASS__,__FUNCTION__);
        //var_dump($this->cache);
        //$this->db->i_prepare("SELECT * FROM user WHERE `id`=:id");
        /*var_dump($this->db->stat);

        var_dump($this->db->i_execute([":id"=>30]));
        $data = $this->db->i_fetchObject();*/
        /*$this->db->i_trans_start();
        $this->db->i_prepare("INSERT INTO `dynamic_praise` (`dynamicId`) VALUES (:dynamicId)");
        $this->db->i_execute([":dynamicId"=>600]);
        $this->db->i_prepare("INSERT INTO `dynamic` (`dynamicId`) VALUES (:dynamicId)");
        $this->db->i_execute([":dynamicId"=>300]);
        $this->db->i_trans_complete();   */   //该该方法返回一个事务处理结果，TRUE 为事务成功提交，false 为事务处理失败，回滚了事务
        //$this->cache->redis->save(getRedisKey(__CLASS__,__FUNCTION__),$data);
        //$sdk = $this->cache->redis->get(getRedisKey(__CLASS__,__FUNCTION__));
        //var_dump($sdk);
    }
}

