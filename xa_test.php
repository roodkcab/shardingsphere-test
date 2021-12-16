<?php
require_once('db.php');

$db = DB::instance('127.0.0.1', 3307, 'test', 'test', 'test');
$db->execute('use test');

$val = $argv[1];
$uuid = uniqid();
var_dump($uuid);
$db->execute("XA START '${uuid}'");
$data = $db->execute('update test set val = "' . $val . '" where id = 1');
var_dump($data);
sleep(100);
$db->execute("XA END '${uuid}'");
$db->execute("XA PREPARE '${uuid}'");
$db->execute("XA COMMIT '${uuid}'");
