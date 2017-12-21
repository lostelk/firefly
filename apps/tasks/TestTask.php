<?php
/**
 * TestTask
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/12/21 上午11:20
 * @logs   :
 *
 */

namespace FireFly\Tasks;

use Phalcon\Cli\Task;

class TestTask extends Task
{
    public function mainAction(array $params)
    {
        echo PHP_EOL . sprintf('[%s][%s]%s', APP_ENV, __CLASS__, '我飞起来了') . PHP_EOL;
        var_dump($params);
    }
}
