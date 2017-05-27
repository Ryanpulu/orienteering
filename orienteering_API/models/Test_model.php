<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/26
 * Time: 16:27
 * Author Mail : ryanpulu@outlook.com
 */
class Test_model extends CI_Model {
    public function __construct(CI_Cache &$cache)
    {
        parent::__construct($cache);
    }
    public function check_redis(){
        $this->db->i_prepare("select * from user WHERE `id`=:id");
        var_dump($this->db->stat);
        //$this->db->stat->execute([":id"=>50]);
        //$this->db->stat->execute();
        $this->db->i_execute([":id"=>30]);
        $data = $this->db->i_fetchObject();
        //$stat->execute([":id"=>30]);
        //$data = $stat->fetchAll(PDO::FETCH_ASSOC);
        //echo self::getRedisKey(__CLASS__,__FUNCTION__);
        var_dump(self::redis_SaveData(self::getRedisKey(__CLASS__,__FUNCTION__),$data));
        $sdk = $this->cache->redis->get(self::getRedisKey(__CLASS__,__FUNCTION__));
        var_dump($sdk);
        //var_dump(CI_Config::$Conf);
    }
}

