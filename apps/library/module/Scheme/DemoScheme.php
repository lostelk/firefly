<?php
/**
 * DemoScheme
 * 文件按返回对象命名， 比如[房间]命名为Room
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 16/11/17 上午10:15
 * @logs   :
 *
 */

namespace FireFly\Module\Scheme;

class DemoScheme
{
    public static $rules = [
        'id'       => 'int',
        'name'     => 'required|string',
        'livekey'  => 'required|string',
        'stream'   => 'string', // 特殊的字段，data并不存在，需要组装，但是需要返回给app
        'dateline' => 'string', //
    ];

    // 自定义方法 命名规则 get＋
    public static function getStream($data)
    {
        return (string)$data['livekey'];
    }
}