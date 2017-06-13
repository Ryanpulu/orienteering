<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/12 0012
 * Time: 上午 10:45
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Math_lib {

    const EARTH_RADIUS = 6370.996; // 地球半径系数

    const PI = 3.1415926;  //pi

    /**
     * @param $lat1         //第一个点纬度坐标
     * @param $lng1         //第一个点经度坐标
     * @param $lat2         //第二个点纬度坐标
     * @param $lng2         //第二个点经度坐标
     * @param int $unit     //默认为1 返回距离单位   1 : m  /// 2 : km
     * @return float
     */
    public function mathDistanceCoor($lat1, $lng1, $lat2, $lng2, $unit=1)
    {
        $radLat1 = $lat1 * self::PI / 180.0;
        $radLat2 = $lat2 * self::PI / 180.0;

        $radLng1 = $lng1 * self::PI / 180.0;
        $radLng2 = $lng2 * self::PI /180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $distance = $distance * self::EARTH_RADIUS * 1000;

        if($unit==2){
            $distance = $distance / 1000;
        }
        return round($distance,1);
    }


}