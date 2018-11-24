<?php
return [
    'boolean' => ['No', 'Yes'],
    'default_url' => 'http://user.local.firecat.com/auth/signup', // TODO 后台链接开户默认绑定的开户平台, 暂时这么使用，后续要修改成可管理的方式
    'default_signup_dir_name' => '/reg/',

    'mobile_package_download_path'=>'/home/ubuntu-anvo/public/',//移动端安装包的下载路径

    //是否开启开发模式
    'environment' => 0 ? 'develop' : 'production' ,

    
    'js_config' => [
        'develop'    => [
            'base_path' => '/assets/js/',
            'game_path' => '/assets/js/game/',
            'suffix'    => '.js',
        ],
        'production' => [
            'base_path' => '/dist/assets/js/',
            'game_path' => '/dist/assets/js/game/',
            'suffix'    => '.min.js',
        ]
    ],
    'template' => app_path() . '/views/advertisement/adTemplates/',

    //是否开启静态文件cdn域名
    'domain_static' => 0 ? 'http://static.bomao24.com/' : '',
//    'domain_static' => 1 ? 'http://bomao.b0.upaiyun.com/' : '',
//    'domain_static' => 1 ? 'http://7xkwdn.com1.z0.glb.clouddn.com/' : '',

    //前端记录用户行为key名白名单

//    'userBehaviorList' => array(
//        'keys' => array(
//            'gameid' => array('type' => 'int', 'length' => 5)
//        ),
//        'valuekeys' => array(
//            'methodid' => array('type' => 'int', 'length' => 3),
//            'moneyunit' => array('type' => 'string', 'length' => 10),
//            'tracetab' => array('type' => 'string', 'length' => 5)
//        )
//    )

    'userBehaviorList' => [
         'valuekeys' => [
             'methodid' => 'numeric:max:3',
             'moneyunit' => 'max:10',
             'tracetab' => 'alpha_dash|max:5',
         ],
         'keys' => [
             'gameid' => 'numeric:max:5',

         ],
     ],

    
];
