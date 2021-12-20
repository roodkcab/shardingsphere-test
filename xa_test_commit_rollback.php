<?php
require_once('db.php');

$val1 = $argv[1];
$val2 = $argv[2];

$db1 = DB::instance('127.0.0.1', 3307, 'test1', 'test', 'test');
$db1->execute('use test1');

$db2 = DB::instance('127.0.0.1', 3307, 'test2', 'test', 'test');
$db2->execute('use test2');

//commit
$uuid1 = uniqid();
echo "commit uuid1: $uuid1\n";
$db1->execute("XA START '${uuid1}'");
$db1->execute('update test set val = ? where id = ?', [$val1, 1]);
$db1->execute("XA END '${uuid1}'");

$uuid2 = uniqid();
echo "commit uuid2: $uuid2\n";
$db2->execute("XA START '${uuid2}'");
$db2->execute('update test set val = ? where id = ?', [$val2, 1]);
$db2->execute("XA END '${uuid2}'");

$db1->execute("XA PREPARE '${uuid1}'");
$db2->execute("XA PREPARE '${uuid2}'");

$db1->execute("XA COMMIT '${uuid1}'");
$db2->execute("XA COMMIT '${uuid2}'");

$data1 = $db1->getData("select * from test where id = 1");
$data2 = $db2->getData("select * from test where id = 1");
assert($data1[0]['val'] == $val1);
assert($data2[0]['val'] == $val2);

//rollback
$uuid1 = uniqid();
echo "rollback uuid1: $uuid1\n";
$db1->execute("XA START '${uuid1}'");
$db1->execute('update test set val = ? where id = ?', [$val2, 1]);
$db1->execute("XA END '${uuid1}'");

$uuid2 = uniqid();
echo "rollback uuid2: $uuid2\n";
$db2->execute("XA START '${uuid2}'");
$db2->execute('update test set val = ? where id = ?', [$val1, 1]);
$db2->execute("XA END '${uuid2}'");

$db1->execute("XA PREPARE '${uuid1}'");
$db2->execute("XA PREPARE '${uuid2}'");

$db1->execute("XA ROLLBACK '${uuid1}'");
$db2->execute("XA ROLLBACK '${uuid2}'");

$data1 = $db1->getData("select * from test where id = 1");
$data2 = $db2->getData("select * from test where id = 1");
assert($data1[0]['val'] == $val1);
assert($data2[0]['val'] == $val2);
