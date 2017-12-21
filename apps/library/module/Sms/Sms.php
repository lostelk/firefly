<?php
/**
 * Sms
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 17/5/11 下午12:13
 * @logs   :
 *
 */

namespace FireFly\Module\Sms;

use Core\Rpc;
use Core\Rpc\RpcException;
use Exception;

class Sms extends \Phalcon\Di\Injectable
{
    /**
     * 限制
     *
     * @var array
     */
    protected $limit = [
        'register' => [
            'ttl' => 600,
            'dayCount' => 20,
            'errCount' => 3, // shell
        ],
        'findpasswd' => [
            'ttl' => 600,
            'dayCount' => 5,
            'errCount' => 3, // shell
        ],
        'bindpasswd' => [
            'ttl' => 600,
            'dayCount' => 5,
            'errCount' => 3, // shell
        ],
        'login' => [
            'ttl' => 600,
            'dayCount' => 15,
            'errCount' => 3, // shell
        ],
        'takeout' => [
            'ttl' => 600,
            'dayCount' => 15,
            'errCount' => 3,
        ],
        'task' => [
            'ttl' => 600,
            'dayCount' => 15,
            'errCount' => 3,
        ],
    ];

    /**
     * 发送短信
     *
     * @param $mobile
     * @param $code
     * @param $tplid
     * @param $data
     * @param $type
     * @param int $retry
     *
     * @throws \Exception
     */
    public function sendSms($cid, $mobile, $code, $tplId, $tplData, $type, $retry = 0)
    {
        $checkData = $this->checkRule($mobile, $type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        $redisData = [
            'sendTime' => time(),
            'code' => $code,
        ];
        $redisData = array_merge($checkData, $redisData);

        $codeKey = sprintf('STR:SMS:%s:%s', strtoupper($type), $mobile);

        $redis->setex($codeKey, $redisData['ttl'], json_encode($redisData));

        try {
            $rpc = Rpc::instance('NotifySvr.Sms.Send')->setData([
                'cid' => $cid,
                'mobile' => $mobile,
                'tplid' => $tplId,
                'retry' => $retry,
                'data' => $tplData,
            ])->getData();

            return $rpc['code'] == 0;
        } catch (RpcException $e) {
            throw new Exception("验证码发送失败", 10001);
        }
    }

    /**
     * 发送语音
     *
     * @param $cid
     * @param $mobile
     * @param $code
     * @param $type
     *
     * @return bool
     */
    public function sendVoice($cid, $mobile, $code, $type)
    {
        $checkData = $this->checkRule($mobile, $type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        $redisData = [
            'sendTime' => time(),
            'code' => $code,
        ];
        $redisData = array_merge($checkData, $redisData);

        $codeKey = sprintf('STR:SMS:%s:%s', strtoupper($type), $mobile);

        $redis->setex($codeKey, $redisData['ttl'], json_encode($redisData));

        try {
            $rpc = Rpc::instance('NotifySvr.Voice.Send')->setData([
                'cid' => $cid,
                'mobile' => $mobile,
                'code' => $code,
            ])->getData();

            return $rpc['code'] == 0;
        } catch (RpcException $e) {
            return false;
        }
    }

    private function getRules($type)
    {
        if (isset($this->limit[$type])) {
            $ttl = $this->limit[$type]['ttl'];
            $dayCount = $this->limit[$type]['dayCount'];
            $errCount = $this->limit[$type]['errCount'];
        } else {
            throw new Exception("短信限制配置类不存在", 4);
        }

        return [
            'ttl' => $ttl,
            'dayCount' => $dayCount,
            'errCount' => $errCount,
        ];
    }

    private function checkRule($mobile, $type)
    {

        if (!isLocal()) {
            // 风控
            $this->risk($mobile, $type);
        }

        // 配置类
        $rule = $this->getRules($type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        if (strpos($mobile, '-') === false && !preg_match('/^1[\d]{10}$/', $mobile)) {
            throw new Exception("没有可发送的手机号码", 1);
        }

        $codeKey = sprintf('STR:SMS:%s:%s', strtoupper($type), $mobile);
        $codeData = $redis->get($codeKey);

        if ($codeData) {
            $codeData = json_decode($codeData, true);
            if (time() - $codeData['sendTime'] < 15) {
                throw new Exception("请求频率太高", 3);
            }
            if (time() - $codeData['sendTime'] > $rule['ttl']) {
                $redis->del($codeKey);
                //throw new \Exception("验证码已失效", 5);
            }
        }

        return $rule;
    }

    public function checkCode($mobile, $code, $type)
    {
        $rule = $this->getRules($type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        $codeKey = sprintf('STR:SMS:%s:%s', strtoupper($type), $mobile);
        $codeData = $redis->get($codeKey);

        if ($codeData) {
            $codeData = json_decode($codeData, true);

            if (time() - $codeData['sendTime'] > $rule['ttl']) {
                $redis->del($codeKey);
                throw new Exception("验证码已失效", 10005);
            }

            if ($codeData['errCount'] < 1) {
                $redis->del($codeKey);
                throw new Exception("验证码错误已达上限，请重新获取验证码", 10002);
            }

            if ($code != $codeData['code']) {
                $codeData['errCount'] -= 1;
                $redis->set($codeKey, json_encode($codeData));
                throw new Exception("校验验证码失败", 10009);
            }

            return true;
        }

        throw new Exception("校验验证码失败", 10009);
    }

    /**
     * 风控
     *
     * @param $mobile
     * @param $type
     *
     * @return bool
     * @throws Exception
     */
    private function risk($mobile, $type)
    {
        $key = sprintf('STR:RISK:SMS:MOBILE:%s:%s', $type, $mobile);
        $rules = $this->getRules($type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        // 不存在数据
        $dataCount = $redis->get($key);
        if (empty($dataCount)) {
            $redis->INCR($key, 1);
            $redis->expire($key, 86400);
        } else {
            if ($dataCount < $rules['dayCount']) {
                $redis->INCR($key, 1);
            } else {
                throw new Exception('手机短信发送次数超出限制', -1);
            }
        }

        return true;
    }
}
