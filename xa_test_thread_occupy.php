<?php
/**
 * set maxPoolSize and minPoolSize to 2 for shardingsphere
 * then run php xa_test_thread_occupy.php test1, and it will sleep before 
 * xa end for 100 seconds, then run php xa_test_thread_occupy.php test2 
 * and it will stuck on update. 
 * during the script is running, you can check full processlist 
 * of database, if everything is ok you should see a sleep 
 * connection, with another one in lock wait
 */
require_once('db.php');

$val1 = $argv[1];

$db1 = DB::instance('127.0.0.1', 3307, 'test1', 'test', 'test');
$db1->execute('use test1');
var_dump($db1->getData('select * from test where id = ?', [1]));

$uuid1 = uniqid();
$db1->execute("XA START '${uuid1}'");
$db1->execute('update test set val = ? where id = ?', [$val1, 1]);
echo "uuid1: $uuid1\n";
sleep(100);
$db1->execute("XA END '${uuid1}'");
$db1->execute("XA COMMIT '${uuid1}'");
