<?php

$driver = 'redis';
$host = '127.0.0.1';
$ttr = 60;
return array(
    'default' => 'main',
    /*
      |--------------------------------------------------------------------------
      | Queue Connections
      |--------------------------------------------------------------------------
      |
      | Here you may configure the connection information for each server that
      | is used by your application. A default configuration has been added
      | for each back-end shipped with Laravel. You are free to add more.
      |
     */
    'connections' => array(
        'sync' => array(
            'driver' => 'sync',
        ),
        'main' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'main',
            'ttr' => $ttr,
        ),
        'send_money' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'send_money',
            'ttr' => $ttr,
        ),
        'prize' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'prize',
            'ttr' => $ttr,
        ),
        'trace' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'trace',
            'ttr' => $ttr,
        ),
        'withdraw' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'withdraw',
            'ttr' => $ttr,
        ),
        'stat' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'stat',
            'ttr' => $ttr,
        ),
        'statWithdrawal' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statWithdrawal',
            'ttr' => $ttr,
        ),
        'statDeposit' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statDeposit',
            'ttr' => $ttr,
        ),
        'statTurnover' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statTurnover',
            'ttr' => $ttr,
        ),
        'statBonus' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statBonus',
            'ttr' => $ttr,
        ),
        'statPrize' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statPrize',
            'ttr' => $ttr,
        ),
        'statCommission' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'statCommission',
            'ttr' => $ttr,
        ),
        'account' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'account',
            'ttr' => $ttr,
        ),
        'activity' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'activity',
            'ttr' => $ttr,
        ),
        'send_commission' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'send_commission',
            'ttr' => $ttr,
        ),
        'ProjectIdQueue' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'ProjectIdQueue',
            'ttr' => $ttr,
        ),
        'get_trace_issue_code' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'get_trace_issue_code',
            'ttr' => $ttr,
        ),
        'SDWithdraw' => array(
            'driver' => $driver,
            'host' => '10.6.21.52',
            'queue' => 'SDWithdraw',
            'ttr' => $ttr,
        ),
        'send_money_electronic_entertainment' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'send_money_electronic_entertainment',
            'ttr' => $ttr,
        ),
        'calculate_electronic_entertainment' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'calculate_electronic_entertainment',
            'ttr' => $ttr,
        ),
        /* ==================JC================= */
        'jc_calculate' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'jc_calculate',
            'ttr' => $ttr,
        ),
        'jc_send_money' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'jc_send_money',
            'ttr' => $ttr,
        ),
        'jc_send_commission' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'jc_send_commission',
            'ttr' => $ttr,
        ),
        'send_money_electronic_entertainment' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'send_money_electronic_entertainment',
            'ttr' => $ttr,
        ),
        'calculate_electronic_entertainment' => array(
            'driver' => $driver,
            'host' => $host,
            'queue' => 'calculate_electronic_entertainment',
            'ttr' => $ttr,
        ),
//        'sqs'                => array(
//            'driver' => 'sqs',
//            'key'    => 'your-public-key',
//            'secret' => 'your-secret-key',
//            'queue'  => 'your-queue-url',
//            'region' => 'us-east-1',
//        ),
//        'iron'               => array(
//            'driver'  => 'iron',
//            'host'    => 'mq-aws-us-east-1.iron.io',
//            'token'   => 'your-token',
//            'project' => 'your-project-id',
//            'queue'   => 'your-queue-name',
//        ),
//        'redis'              => array(
//            'driver' => 'redis',
//            'queue'  => 'default',
//        ),
    ),
    /*
      |--------------------------------------------------------------------------
      | Failed Queue Jobs
      |--------------------------------------------------------------------------
      |
      | These options configure the behavior of failed queue job logging so you
      | can control which database and table are used to store the jobs that
      | have failed. You may change them to any database / table you wish.
      |
     */
    'failed' => array(
        'database' => 'mysql', 'table' => 'failed_jobs',
    ),
);
