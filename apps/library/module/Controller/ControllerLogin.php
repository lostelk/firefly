<?php
/**
 * ControllerLogin
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/9/30 下午12:12
 * @logs   :
 *
 */

namespace FireFly\Module\Controller;

class ControllerLogin extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
        $this->checkLogin();
    }
}
