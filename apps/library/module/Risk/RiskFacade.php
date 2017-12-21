<?php
/**
 * RiskFacade
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/8 上午11:54
 * @logs   :
 *
 */

namespace FireFly\Module\Risk;

use Core\Facade;

class RiskFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return new Risk();
    }
}
