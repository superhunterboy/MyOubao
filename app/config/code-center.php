<?php

return [
    'logs' => [
        'set-winning-number' => Config::get('log.root') . DIRECTORY_SEPARATOR . 'code-center' . DIRECTORY_SEPARATOR . 'set' . DIRECTORY_SEPARATOR . date('Ymd'),
    ],
    'lotteries' => [
        'CQSSC' => 1,
        'SD11Y' => 2,
        'HLJSSC' => 3,
        'SSL' => 4,
        'JXSSC' => 5,
        'XJSSC' => 6,
        'TJSSC' => 7,
        'JX11Y' => 8,
        'GD11Y' => 9,
        'CQ11Y' => 10,
        'BMSSC' => 11,
        'BM11Y' => 12,
    ],
    'user_id' => 65535,
    'username' => 'code_center'
];
