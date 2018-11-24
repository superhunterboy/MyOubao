<?php
return [
    'root' => storage_path() . DIRECTORY_SEPARATOR . 'logs',
    'default'=>'logger',
    'logs'=>[
        'logger'=>date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR.date('d').DIRECTORY_SEPARATOR.'logger.log',
        'activity'=>date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR.date('d').DIRECTORY_SEPARATOR.'activity.log',
    ],
];