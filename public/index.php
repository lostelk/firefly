<?php
error_reporting(E_ALL);

define('APP_NAME', 'FIREFLY'); // 项目名称
define('APP_ENV', getenv('SY_APPLICATION_ENV'));

define('APP_PATH', __DIR__ . '/../apps');
define('BASE_PATH', __DIR__ . '/..');

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

class Request extends Phalcon\Http\Request
{
    public function getClientAddress($trustForwardedHeader = null)
    {
        $ip = parent::getClientAddress();
        if (isset($_SERVER['HTTP_USERIP'])) {
            $ip = $_SERVER['HTTP_USERIP'];
        } elseif (isset($_SERVER['HTTP_ALI_CDN_REAL_IP'])) {
            $ip = $_SERVER['HTTP_ALI_CDN_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && $_SERVER['HTTP_X_FORWARDED_FOR']
        ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $ip = explode(',', $ip);
        $ip = trim($ip[0]);

        return $ip;
    }
}

try {
    $di = new Phalcon\DI\FactoryDefault();


    $config = APP_PATH . '/config/config.' . APP_ENV . '.php';
    if (file_exists($config)) {
        $config = include APP_PATH . '/config/config.' . APP_ENV . '.php';
        $di->set('config', $config, true);
    }

    define('APP_LOG', $di->getConfig()->logPath);

    $di->set('router', function () {
        $router = new Core\Router(false);

        // 注册 notFound
        $router->notFound([
            'namespace'  => 'FireFly\Controllers',
            'controller' => 'Index',
            'action'     => 'notFound'
        ]);

        $router->add('/', [
            'namespace'  => 'FireFly\Controllers',
            'controller' => 'Index',
            'action'     => 'index'
        ]);

        // 公共信息路由组
        $router->mount(new FireFly\Routes\CommonRoutes());

        return $router;
    });

    // Registering a dispatcher
    $di->set('dispatcher', function () {
        $dispatcher = new Phalcon\Mvc\Dispatcher;
        $dispatcher->setDefaultNamespace('FireFly\Controllers\\');

        return $dispatcher;
    });

    // Registering a Http\Response
    $di->set('response', function () {
        return new Phalcon\Http\Response();
    });

    // Registering a Http\Request
    $di->set('request', function () {
        return new Request();
    });

    // Registering the view component
    $di->set('view', function () {
        $view = new Phalcon\Mvc\View();
        $view->setViewsDir('../apps/views/');

        return $view;
    });

    $di->set('log', function () use ($di) {
        $log = new Core\Log($di->getConfig()->logPath);
        return $log;
    });

    // redis return $app->redisCache
    foreach ((array)$di->get('config')->redis as $k => $v) {
        $di->set('redis' . $k, function () use ($v) {
            $objRedis = new \Core\BeehiveRedis($v->toArray());

            return $objRedis;
        });
    }

    // Loader
    $loader = new Phalcon\Loader();

    $loader->registerFiles([
        __DIR__ . '/../vendor/autoload.php'
    ]);

    $loader->registerNamespaces(
        [
            'FireFly\Controllers' => '../apps/controllers/',
            'FireFly\\Routes'     => '../apps/routes/',
            'FireFly\\Models'     => '../apps/models/',
            'Core'               => '../apps/library/core/',
            'FireFly\\Module'     => '../apps/library/module/',
            'FireFly\\Traits'     => '../apps/library/traits/',
        ]
    );

    $loader->register();
    $application = new Phalcon\Mvc\Application();
    $application->setDI($di);
    echo $application->handle()->getContent();
} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (Core\Rpc\RpcException $e) {
    $controller = new FireFly\Module\Controller\ErrorController;
    echo $controller->showError($e->getCode(), $e->getMessage());
} catch (PDOException $e) {
    echo $e->getMessage();
}
