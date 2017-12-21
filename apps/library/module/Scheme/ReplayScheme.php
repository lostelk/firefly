<?php
/**
 * RoomWawaListScheme
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/2 下午8:22
 * @logs   :
 *
 */

namespace FireFly\Module\Scheme;

class ReplayScheme extends \Phalcon\Di\Injectable
{
    protected $rules = [
        'videoId'       => 'filter',
        'livekey'       => 'string',
        'startTime'     => 'int',
        'endTime'       => 'filter',
        'duration'      => 'int',
        'machineStream' => 'string',
    ];
}
