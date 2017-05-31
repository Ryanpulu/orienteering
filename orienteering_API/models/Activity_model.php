<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ryanp
 * Date: 2017/5/29
 * Time: 18:05
 */
class Activity_model extends CI_Model {

    protected $table = "activity";

    /*
     * @desc activity idit
     * @var int
     */
    private $activityId=0;
    /*
     * @desc activity name
     * @var string
     */
    private $name=NULL;

    private $ownerId;



}

