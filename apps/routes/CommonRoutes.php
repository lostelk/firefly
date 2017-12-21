<?php
/**
 * CommonRoutes
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/1 下午12:12
 * @logs   :
 *
 */

namespace FireFly\Routes;

use Phalcon\Mvc\Router\Group as RouterGroup;

class CommonRoutes extends RouterGroup
{
    const MODULE_NAME = 'common';

    public function initialize()
    {
        $this->setPaths([
            "namespace" => "FireFly\\Controllers\\" . ucfirst(self::MODULE_NAME)
        ]);

        $this->setPrefix("/" . self::MODULE_NAME);

        // app msg
        $this->add("/app/msg", [
            "controller" => "App",
            "action"     => "msg",
        ]);

    }
}
