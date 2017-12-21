<?php
/**
 * test.php
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/11/1 下午3:49
 * @logs   :
 *
 */

function xrange($start, $end, $step = 1)
{
    for ($i = $start; $i <= $end; $i += $step) {
        yield $i . "\n";
    }
}
//
//foreach (xrange(1, 100) as $num) {
//    echo $num;
//}

function gen() {
    $ret = (yield 'yield1');
    var_dump($ret);
    $ret = (yield 'yield2');
    var_dump($ret);
    $ret = (yield 'yield3');
}

$gen = gen();

var_dump($gen->current());    // string(6) "yield1"
var_dump($gen->send('ret1')); // string(4) "ret1"   (the first var_dump in gen)
// string(6) "yield2" (the var_dump of the ->send() return value)
var_dump($gen->send('ret2')); // string(4) "ret2"   (again from within gen)
var_dump($gen->send('ret3')); // string(4) "ret2"   (again from within gen)
// NULL


function gen2() {
    yield 'foo';
    yield 'bar';
}

$gen = gen2();
$gen->current();


function echoTimes($msg, $max) {
    for ($i = 1; $i <= $max; ++$i) {
        echo "$msg iteration $i\n";
        yield "$msg iteration $i\n";
    }
}

echoTimes('foo', 10); // print foo ten times
echo "---\n";
echoTimes('bar', 5); // print bar five times
