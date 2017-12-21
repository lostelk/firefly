<?php
/**
 * AppController
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/12 下午2:29
 * @logs   :
 *
 */

namespace FireFly\Controllers\Common;

use FireFly\Module\Controller\ControllerBase;

class AppController extends ControllerBase
{
    public function msgAction()
    {
        return $this->success([], 0, 'Hello, Msg');
    }
}
