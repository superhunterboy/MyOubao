<?php

$dbhost = '127.0.0.1';
$dbname = 'firecat';
// echo $dbhost;
$m = new Mongo($dbhost);
$db = $m->$dbname;

$collection = $db->testData;

 // $item = array(
 //    'name' => 'milk',
 //    'quantity' => 10,
 //    'price' => 2.50,
 //    'note' => 'skimmed and extra tasty'
 //  );
 //  $collection->insert($item);
 //  echo 'Inserted document with ID: ' . $item['_id'];

$cursor = $collection->find();
// var_dump($cursor);
foreach($cursor as $doc)
{
    var_dump($doc);
}

?>