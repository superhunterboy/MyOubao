<?php

/*
|--------------------------------------------------------------------------
| 拓展配置文件
|--------------------------------------------------------------------------
|
*/

return array(

    /**
     * 网站静态资源文件别名配置
     */
    'webAssets' => array(

        'cssAliases' => array(  //  样式文件别名配置

            'bootstrap-3.0.3'    => 'assets/bootstrap-3.0.3/css/bootstrap.min.css',
            'bootstrap-3-switch' => 'assets/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css',
            'bootstrap-ie'       => 'assets/bootstrap-3.0.3/css/bootstrap-ie6.css',
            'ie'                 => 'assets/bootstrap-3.0.3/css/ie.css',
            'ui'                 => 'assets/css/ui.css',
            'main'               => 'assets/css/main.css',
            'ueditor'            => 'assets/ueditor/themes/default/css/ueditor.css',

        ),

        'jsAliases'  => array(  //  脚本文件别名配置

            'jquery-1.10.2'         => 'assets/js/jquery-1.10.2.min.js',
            'google::jquery-1.10.2' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',

            'jquery.cookie'         => 'assets/js/jquery.cookie.js',


            'bootstrap-3.0.3'       => 'assets/bootstrap-3.0.3/js/bootstrap.min.js',
            'bootstrap-ie'          => 'assets/bootstrap-3.0.3/js/bootstrap-ie.js',
            'bootstrap-3-switch'    => 'assets/bootstrap-switch/js/bootstrap-switch.min.js',
            'jquery-ui'             => 'assets/js/jquery-ui-1.10.3.custom.min.js',
            'highcharts'            => 'assets/js/highcharts.js',
            'exporting'             => 'assets/js/exporting.js',

            'ui-checkbox'           => 'assets/js/ui-checkbox.js',
            'bootstrap-select'      => 'assets/js/bootstrap-select.js',
            'bootstrap-switch'      => 'assets/js/bootstrap-switch.js',
            'base'                  => 'assets/js/base.js',
            'datetimepicker'        => 'assets/js/bootstrap-datetimepicker.min.js',
            'datetimepicker-zh-CN'  => 'assets/js/bootstrap-datetimepicker.zh-CN.js',

            'ueditor.config'        => 'assets/ueditor/ueditor.config.js',
            'ueditor.min'           => 'assets/ueditor/ueditor.all.min.js',
            'zh-cn'                 => 'assets/ueditor/lang/zh-cn/zh-cn.js',
            'create-user-link'      => 'assets/js/create-user-link.js',
            'md5'                   => 'assets/js/md5.js',

            'numtochinese'   =>'assets/js/numtochinese.js',

            'ZeroClipboard'            => 'assets/js/ZeroClipboard.js',

        ),

    ),

);