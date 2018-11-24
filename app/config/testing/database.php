<?php
return array(
  'default' => 'sqlite',
  'connections' => array(
    'sqlite' => array(
      'driver'   => 'sqlite',
      'database' => ':memory:',
      'prefix'   => ''
    ),
    'mysql' => array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'laravelTest',
        'username'  => 'root',
        'password'  => 'ctfo6789',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ),
  )
);