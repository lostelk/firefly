<?php
/**
 * ControllerBase
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 16/12/30 下午8:22
 * @logs   :
 *
 */

namespace FireFly\Module\Controller;

use Core\FireReturn;
use Core\Rpc;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    protected $uid = null;

    // 分平台
    protected $cid = null;

    protected $appId = null;

    public function onConstruct()
    {
        if (!$this->checkSign() && !$this->isDebug()) {
            return $this->error(110, '鉴权失败' . rand(1, 10));
        }
    }

    protected function isDebug()
    {
        if (isOnline()) {
            $return = false;
        } else {
            $return = $this->config->debug && $this->request->hasQuery('debug');
        }

        if ($this->request->hasQuery('h5Debug')) {
            $return = true;
        }

        return $return;
    }

    protected function setDebug()
    {
        $this->config->debug = true;
        $_GET['debug'] = 1;
    }

    /**
     * success
     *
     * @param array $data
     * @param int $code
     * @param string $message
     */
    protected function success($data = [], $code = 0, $message = '')
    {
        if (empty($data)) {
            $data = (object)$data;
        }
        $strRtn = FireReturn::makeJson($code, $message, $data);
        $this->response
            ->setContentType('application/json; charset=utf-8')
            ->sendHeaders();
        echo $strRtn;
        exit;
    }

    /**
     * error
     *
     * @param int $code
     * @param string $message
     * @param array $data
     */
    protected function error($code = -99, $message = '', $data = [])
    {
        if (empty($data)) {
            $data = (object)$data;
        }
        $strRtn = FireReturn::makeJson($code, $message, $data);
        $this->response
            ->setContentType('application/json; charset=utf-8')
            ->sendHeaders();
        echo $strRtn;
        exit;
    }

    /**
     * 确认登录
     */
    public function checkLogin()
    {
        if (!$this->isLogin()) {
            return $this->error(-2050, '请登录后再操作');
        }
    }

    /**
     * 检测登录状态
     */
    public function isLogin()
    {
        $sid = $this->request->get('sid', 'string');
        $dev = trim($this->request->get('dev', 'string'));

        $this->uid = (int)Session::checkSession($sid);

        if (empty($this->uid)) {
            return false;
        }

        // 判断设备是否被封禁
        if ($this->redisStorage->SISMEMBER('SET:DEVID:BLOCK', $dev)) {
            return false;
        }

        // 判断是否被封号
        if ($this->uid) {
            $status = $this->getUser($this->uid, 'status');
            if ($status !== '' && ($status + 0) & 32) {
                unset($this->uid);

                return false;
            }

            return true;
        }

        return false;
    }

    public function getApp($appId, $field)
    {
        if ($field) {
            $rebuild = false;
            if (is_array($field)) {
                $app = $this->redisStorage->hMGet(sprintf('HASH:APP:INFO:%d', $appId), $field);
                if (empty($app)) {
                    $app = false;
                } else {
                    if (array_search(false, $app) !== false) {
                        $app = false;
                        $rebuild = true;
                    }
                }
            } else {
                $app = $this->redisStorage->hGet(sprintf('HASH:APP:INFO:%d', $appId), $field);
            }

            if ($app === false) {
                try {
                    $app = [];
                    $rpc = Rpc::instance('OpenSvr.Info.Get')->setData(['appId'   => $appId,
                                                                       'rebuild' => $rebuild
                    ])->getData();
                    if (is_array($field)) {
                        foreach ($field as $item) {
                            $app[$item] = isset($rpc['data'][$item]) ? $rpc['data'][$item] : '';
                        }
                    } else {
                        $app = isset($rpc['data'][$field]) ? $rpc['data'][$field] : '';
                    }
                } catch (\Exception $e) {
                    $app = [];
                }
            }

            return $app;
        } else {
            $app = $this->redisStorage->hGetAll('HASH:APP:INFO:' . $appId);
        }

        if (empty($app)) {
            try {
                $rpc = Rpc::instance('OpenSvr.Info.Get')->setData(['appId' => $appId])->getData();
                $app = $rpc['data'];
            } catch (\Exception $e) {
                $app = [];
            }
        }

        if (!empty($app) && isset($app['appId']) && $app['appId'] != 0) {
            $app['appId'] = (string)$app['appId'];
            $app['cid'] = (int)$app['cid'];
            $app['bid'] = (int)$app['bid'];
            $app['appSecret'] = (string)$app['appSecret'];
        } else {
            $app = [];
        }

        return $app;
    }

    public function checkSign()
    {
        // todo
        return true;

        if ($this->isDebug()) {
            $this->cid = (int)$this->getApp($this->appId, 'cid');
        }

        $this->log->info('鉴权失败:' . json_encode($this->request->get()));

        return false;
    }
}
