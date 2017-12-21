<?php
/**
 * RiskConfig.php
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/8 上午11:42
 * @logs   :
 *
 */

namespace FireFly\Module\Risk;

class RiskConfig
{
    const CONFIG = [
        'qiniuToken' => [
            'ttl'      => 86400,
            'dayCount' => 10,
            'errCount' => 0,
        ],
        'register'   => [
            'ttl'      => 600,
            'dayCount' => 5,
            'errCount' => 3,
        ],
    ];
}