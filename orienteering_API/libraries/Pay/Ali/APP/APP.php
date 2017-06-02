<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 2017/6/2
 * Time: 11:41
 * Author Mail : ryanpulu@outlook.com
 */
class Ali_APP{
    const method = 'alipay.trade.app.pay';
    private $app_id = null;
    const format = 'JSON';
    private $charset = 'utf-8';
    private $sign_type = 'RSA';
    private $sign = null;
    private $timestamp = null;
    private $version = null;
    private $notify_url = null;
    private $biz_content = null;
}
