<?php
/**
 * Created by PhpStorm.
 * User: Ryanp
 * Date: 2017/5/29
 * Time: 18:05
 */
class Activity_model extends CI_Model {

    protected $table = "activity";

    /*
     * 活动Id
     */
    private $activityId;

    private $name;

    private $ownerId;

    public function __construct(CI_Cache &$cache)
    {
        parent::__construct($cache);
    }

}

