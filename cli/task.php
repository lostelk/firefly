<?php
/**
 * public_local
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 16/11/17 下午1:14
 * @logs   :
 *
 */

error_reporting(E_ALL);
include __DIR__ . '/../vendor/autoload.php';

define('APP_PATH', __DIR__ . '/../apps');
define('APP_ENV', getenv('SY_APPLICATION_ENV'));
define('BASE_PATH', __DIR__ . '/..');

use Phalcon\Cli\Console;

function isOnline()
{
    return APP_ENV === 'master';
}

function isPrerelease()
{
    return APP_ENV === 'prerelease';
}

function isLocal()
{
    return APP_ENV === 'local';
}

try {
    $di = new Phalcon\Di\FactoryDefault\Cli;

    $config = APP_PATH . '/config/config.' . APP_ENV . '.php';
    if (file_exists($config)) {
        $config = include APP_PATH . '/config/config.' . APP_ENV . '.php';
        $di->set('config', $config, true);
    }

    $di->set('log', function () use ($di) {
        $log = new Core\Log($di->getConfig()->logPath);

        return $log;
    });

    $di->set('dispatcher', function () {
        $dispatcher = new Phalcon\Cli\Dispatcher;
        $dispatcher->setDefaultNamespace('FireFly\Tasks\\');

        return $dispatcher;
    });

    // 设置redis return $app->redisCache
    foreach ((array)$di->get('config')->redis as $k => $v) {
        $di->set('redis' . $k, function () use ($v, $k) {
            $objRedis = new \Claw\Redis\BeehiveRedis($v->toArray());

            return $objRedis;
        });
    }

    $di->set('redisLock', function () use ($di) {
        return new \Claw\Redis\Lock\RedisLock($this->get('redisCache'));
    });

    $loader = new Phalcon\Loader();
    $loader->registerNamespaces(
        [
            'FireFly\Tasks'   => '../apps/tasks',
            'FireFly\\Models' => '../apps/models/',
            'Core'           => '../apps/library/core/',
            'FireFly\\Module' => '../apps/library/module/'
        ]
    );

    $loader->register();
    $console = new Console;
    $console->setDi($di);

    $arguments = [];

    foreach ($argv as $k => $arg) {
        if ($k === 1) {
            $arguments['task'] = $arg;
        } elseif ($k === 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $arguments['params'][] = $arg;
        }
    }

    $console->handle($arguments);
} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}
