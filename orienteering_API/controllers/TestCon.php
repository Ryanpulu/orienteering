<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class TestCon extends CI_Controller{
    public function testFun(){
        echo "good";
    }
    public function testToml(){

        var_dump($this->cache);
        var_dump(CI_Config::$Conf);

    }
    public function phpinfo(){
        phpinfo();
    }
}