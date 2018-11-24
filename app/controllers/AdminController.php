<?php

class AdminController extends AdminBaseController {

    public function getIndex() {
        $sysInfo = array('php_version' => PHP_VERSION, 'os' => PHP_OS, 'web_server' => $_SERVER['SERVER_SOFTWARE'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'app_version' => SysConfig::readValue('sys_version'));
        return View::make('index')->with(compact('sysInfo'));
    }

    public function getFrameset() {
        $title = $this->generateTitle();
        return View::make('l.frameset')->with(compact('title'));
    }

    protected function generateTitle() {
        $appTitle = SysConfig::readValue('app_title');
        $appName = SysConfig::readValue('app_name');
        $appTitle || ($appTitle = $appName);
        $appTitle = __($appTitle);
        $appVersion = SysConfig::readValue('sys_version');
        return $appTitle . ' ' . $appVersion;
    }

}

?>