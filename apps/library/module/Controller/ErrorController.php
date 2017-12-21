<?php

namespace FireFly\Module\Controller;

class ErrorController extends ControllerBase
{
    public function showError(
        $code = -99,
        $msg = 'error',
        $data = []
    ) {
    
        return $this->error($code, $msg, $data);
    }
}
