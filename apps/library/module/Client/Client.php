<?php

/**
 * Client
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/9/26 下午6:22
 * @logs   :
 *
 */

namespace FireFly\Module\Client;

use Core\Rpc;

class Client
{
    //os区分设备；1：Andriod设备 2：iOS设备 3：Windows Phone设备 4：PC设备 5：浏览器设备 6：ipad 10：壳应用
    //13:百度sdk, 15:壳应用Plus,20:壳应用Mini
    const ANDROID = 1;
    const IOS = 2;
    const WINDOWSPHONE = 3;
    const PC = 4;
    const BROWSER = 5;
    const IPAD = 6;
    const Android_Pad = 7;
    const H5 = 9;
    const IPHONE2 = 10;
    const IOS_BAIDU_SDK = 13;
    const IOS_PLUS = 15;
    const IOS_MINI = 20;
    const IOS_AIR = 25;
    const IOS_ShouBoTV = 30;
    const ZS_ANDROID = 35; // 手游助手安卓
    const ZS_IOS = 40; //手游助手ios

    //获取设备CODE
    static public function getClientCode($client)
    {
        //终端位表示:1 Android, 2:Iphone, 3 H5,4:weixin, 5 Android Pad, 6: Ipad
        $clientMap = [
            'android'    => self::ANDROID,
            'ios'     => self::IOS,
            'h5'         => self::H5,
            'weixin'     => self::H5,
            'androidpad' => self::Android_Pad,
            'ipad'       => self::IPAD,
            'pc'         => self::PC
        ];
        $client = strtolower($client);
        $clientCode = isset($clientMap[$client]) ? $clientMap[$client] : 0;

        return $clientCode;
    }

    //IP获取城市CODE
    static function getCityCode($ip)
    {
        $citycode = 0;
        if ($ip) {
            try {
                $resp = Rpc::instance('UtilSvr.Ip.Get')->setData(array(
                    'ip' => $ip
                ))->getData();

                if ($resp && isset($resp['data']['citycode'])) {
                    $citycode = $resp['data']['citycode'];
                }
            } catch (\Exception $e) {
            }
        }

        return (int)$citycode;
    }

    //IP获取城市 all info
    static function getCityInfo($ip)
    {
        $data = [
            'country'  => '',
            'province' => '',
            'position' => '',
            'citycode' => 0,
        ];
        try {
            $resp = Rpc::instance('UtilSvr.Ip.Get')->setData(array(
                'ip' => $ip
            ))->getData();

            if (!empty($resp) && count($resp) > 5) {
                $data['country'] = (string)$resp[0];
                $data['province'] = (string)$resp[1];
                $data['position'] = (string)$resp[2];
                $data['citycode'] = isset($resp['data']['citycode']) ? (int)$resp['data']['citycode'] : 0;
            }
        } catch (\Exception $e) {
        }

        return (int)$data;
    }
}
