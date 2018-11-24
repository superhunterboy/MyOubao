<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * 各任务基类
 *
 * @author frank
 */
class ActivityBaseTask extends BaseTask {

    public static function addTask($sCommand, $data, $sQueue) {
        $bActivityStatus = SysConfig::readValue('activity_status');
        if ($bActivityStatus == Activity::STATUS_OPEN) {
            return parent::addTask($sCommand, $data, $sQueue);
        } else {
            return true;
        }
    }

}
