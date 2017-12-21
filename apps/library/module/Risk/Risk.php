<?php
/**
 * Risk.php
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/10/8 上午11:41
 * @logs   :
 *
 */

namespace FireFly\Module\Risk;

use Exception;
use Phalcon\Di\Injectable;

class Risk extends Injectable
{
    public function AddRisk($uid, $type, $num = 1)
    {
        $key = sprintf('STR:RISK:API:%d:%s', $uid, $type);

        $config = $this->getRules($type);

        $di = $this->getDI();
        $redis = $di->get('redisCache');

        // 不存在数据
        $dataCount = $redis->get($key);
        if (empty($dataCount)) {
            $redis->INCR($key, $num);
            $redis->expire($key, $config['ttl']);
        } else {
            if ($dataCount < $config['dayCount']) {
                $redis->INCR($key, $num);
            } else {
                throw new Exception('超出限制', -1);
            }
        }

        return true;
    }

    private function getRules($type)
    {
        $config = RiskConfig::CONFIG;

        if (isset($config[$type])) {
            return $config[$type];
        } else {
            throw new Exception("风控配置类不存在", 4);
        }
    }
}