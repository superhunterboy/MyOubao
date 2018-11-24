<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-8-30
 * Time: 下午5:35
 */
class KeyValues
{
    private $kvs = array();

    function items()
    {
        return $this->kvs;
    }
    function add($k, $v)
    {
        if (!is_null($v))
            $this->kvs[$k] = $v;
    }
    function sign()
    {
        return md5($this->link());
    }
    function link()
    {
        $strb = "";
        ksort($this->kvs);
        foreach ($this->kvs as $key => $val)
        {
            URLUtils::appendParam($strb, $key, $val);
        }
        $oSysconfig = new SysConfig();
        URLUtils::appendParam($strb, AppConstants::$KEY, $oSysconfig::readValue('MER_KEY'));
        $strb = substr($strb, 1, strlen($strb) - 1);
        return $strb;
    }
}