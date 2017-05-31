<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/5/31
 * Time: 17:05
 * Author Mail : ryanpulu@outlook.com
 */
class User_token_model extends CI_Model {

    /*
     * @desc MySQL table name
     * @var string
     */
    protected $table = 'user_token';

    /*
     * @desc 用户ID
     * @var int
     */

    private $userID = null;

    /*
     * @desc 用户token
     * @var string
     */

    private $userToken = NULL;

    /*
     * @desc token过期时间
     * @var int
     */

    private $expiration_time = NULL;

    public function getUserToken($token){
        $this->db->i_prepare(" SELECT `userID`,`userToken`,`expiration_time` FROM user_token WHERE `userToken`=:token LIMIT :limit ");
        $this->db->i_execute([":token"=>$token,":limit"=>1]);
        return $this->db->i_fetchObject();
    }

    /*public function __set($name,$value){
        $this->$name = $value;
    }*/

}
