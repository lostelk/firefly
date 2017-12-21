<?php

/**
 * Scheme
 * 数据结构
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 16/11/16 下午8:32
 * @logs   :
 *
 */

namespace Core;

use Phalcon\Http\Request;

class Scheme
{

    /**
     * in
     *
     * @param Request $request
     *
     * @return array|bool
     */
    public static function in($data, $scheme)
    {
        // check
        self::checkRules($data, $scheme::$rules);

        // generate data
        return self::generateIn($data, $scheme);
    }

    /**
     * out
     *
     * @param $data
     * @param $rule
     * @param array $filter
     *
     * @return mixed
     */
    public static function out($data, $scheme, $filter = [])
    {
        try {
            // generate data
            return self::generateOut($data, $scheme, $filter);
        } catch (SchemErrorException $e) {
            throw new SchemErrorException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 格式验证
     *
     * @param $params
     * @param $rules
     *
     * @return bool
     * @throws SchemErrorException
     */
    public static function checkRules($params, $rules)
    {
        if (empty($rules)) {
            return true;
        }
        foreach ($rules as $key => $rule) {
            //解析规则
            $rule_list = explode('|', $rule);

            $rule = [
                'type' => 'string', //类型默认为字符串
            ];

            foreach ($rule_list as $rule_item) {
                if ($rule_item === 'required') {
                    $rule['required'] = true;
                } elseif ($rule_item === 'int') {
                    $rule['type'] = 'int';
                } elseif ($rule_item == 'array') {
                    $rule['type'] = 'array';
                } elseif (substr($rule_item, 0, 4) === 'min:') {
                    $rule['min'] = (int)substr($rule_item, 4);
                } elseif (substr($rule_item, 0, 4) === 'max:') {
                    $rule['max'] = (int)substr($rule_item, 4);
                }
            }

            //必须项
            if (!empty($rule['required']) && !isset($params[$key])) {
                throw new SchemErrorException($key . ' 参数丢失', 1000);
            }

            if (empty($rule['required']) && !isset($params[$key])) {
                continue;
            }

            //数值型
            if ($rule['type'] == 'int') {
                if (!is_integer($params[$key])) {
                    throw new SchemErrorException($key . ' 必须是数值型', 1000);
                }
                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
                if (!empty($rule['max'])) {
                    if ($params[$key] > $rule['max']) {
                        $message = $key . ' 不能大于' . $rule['max'];
                        throw new SchemErrorException($message, 1000);
                        continue;
                    }
                }
                if (!empty($rule['min'])) {
                    if ($params[$key] < $rule['min']) {
                        $message = $key . ' 不能小于' . $rule['min'];
                        throw new SchemErrorException($message, 1000);
                        continue;
                    }
                }
            } else {
                //不是必须项，如果为空不继续判断
                if (empty($params[$key])) {
                    continue;
                }
            }
            //字符串
            if ($rule['type'] == 'string') {
                if (!is_string($params[$key])) {
                    $message = $key . ' 必须是字符串';
                    throw new SchemErrorException($message, 1000);
                }
                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
                if (!empty($rule['max'])) {
                    if (mb_strlen($params[$key]) > $rule['max']) {
                        $message = $key . ' 长度不能大于' . $rule['max'];
                        throw new SchemErrorException($message, 1000);
                    }
                }
                if (!empty($rule['min'])) {
                    if (mb_strlen($params[$key]) < $rule['min']) {
                        $message = $key . ' 长度不能小于' . $rule['min'];
                        throw new SchemErrorException($message, 1000);
                    }
                }
            }
        }

        return true;
    }

    /**
     * 格式转化
     *
     * @param $params
     * @param $rules
     *
     * @return bool
     * @throws SchemErrorException
     */
    private static function convertRules($params, $rules)
    {
        if (empty($rules)) {
            return true;
        }
        //解析规则
        foreach ($rules as $key => $rule) {
            if ($rule == 'filter') {
                if (isset($params[$key])){
                    unset($params[$key]);
                }
                continue;
            }

            $rule_list = explode('|', $rule);

            $rule = [
                'type' => 'string', //类型默认为字符串
            ];

            foreach ($rule_list as $rule_item) {
                if ($rule_item === 'required') {
                    $rule['required'] = true;
                } elseif ($rule_item === 'int') {
                    $rule['type'] = 'int';
                } elseif ($rule_item === 'filter') {
                    $rule['filter'] = true;
                } elseif ($rule_item == 'array') {
                    $rule['type'] = 'array';
                } elseif (substr($rule_item, 0, 4) === 'min:') {
                    $rule['min'] = (int)substr($rule_item, 4);
                } elseif (substr($rule_item, 0, 4) === 'max:') {
                    $rule['max'] = (int)substr($rule_item, 4);
                } elseif ($rule_item === 'bool') {
                    $rule['type'] = 'bool';
                }
            }

            //必须项
            if (!empty($rule['required']) && !isset($params[$key])) {
                throw new SchemErrorException($key . ' 参数丢失', 1000);
            }

            //过滤
            if (!empty($rule['filter']) && isset($params[$key])) {
                unset($params[$key]);
                continue;
            }

            if (empty($rule['required']) && !isset($params[$key])) {
                continue;
            }

            //bool型
            if ($rule['type'] == 'bool') {
                if (empty($params[$key])) {
                    $params[$key] = false;
                } else {
                    $params[$key] = $params[$key] == true;
                }
            }

            //数值型
            if ($rule['type'] == 'int') {
                if (empty($params[$key])) {
                    $params[$key] = 0;
                } else {
                    $params[$key] = (int)$params[$key];
                }

                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
                if (!empty($rule['max'])) {
                    if ($params[$key] > $rule['max']) {
                        $message = $key . ' 不能大于' . $rule['max'];
                        throw new SchemErrorException($message, 1000);
                        continue;
                    }
                }
                if (!empty($rule['min'])) {
                    if ($params[$key] < $rule['min']) {
                        $message = $key . ' 不能小于' . $rule['min'];
                        throw new SchemErrorException($message, 1000);
                        continue;
                    }
                }
            } else {
                //不是必须项，如果为空不继续判断
                if (empty($params[$key])) {
                    continue;
                }
            }
            //字符串
            if ($rule['type'] == 'string') {
                if (!is_string($params[$key])) {
                    $params[$key] = (string)$params[$key];
                }
                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
            }
        }

        return $params;
    }

    /**
     * 构建转化数据 - in
     *
     * @param $class
     * @param $data
     * @param $filter
     *
     * @return mixed
     */
    private static function generateIn($data, $scheme, $filter = [])
    {
        // 过滤
        foreach ($data as $k => $item) {
            if (in_array($k, $filter)) {
                unset($data[$k]);
            }
        }

        $classes = new \ReflectionClass($scheme);
        $funcs = $classes->getMethods();

        foreach ((array)$funcs as $fnc) {
            $funcName = $fnc->name;
            if (strpos($funcName, 'check') !== false) {
                $gkey = substr($funcName, 5);
                if (in_array($gkey, $filter)) {
                    continue;
                }
                // 检查参数
                try {
                    call_user_func_array([$scheme, $funcName], [$data]);
                } catch (\Exception  $e) {
                    throw new SchemErrorException($e->getMessage(), $e->getCode());
                }
            }
        }

        return $data;
    }

    /**
     * 构建转化数据 - out
     *
     * @param $class
     * @param $data
     * @param $filter
     *
     * @return mixed
     */
    private static function generateOut($data, $scheme, $filter)
    {
        // 过滤
        foreach ((array)$data as $k => $item) {
            if (in_array($k, $filter)) {
                unset($data[$k]);
            }
        }

        // 映射关系
        $classes = new \ReflectionClass($scheme);
        $funcs = $classes->getMethods();
        $props = $classes->getDefaultProperties();
        $rules = $props['rules'];

        // 1. checkRules
        try {
            $data = Scheme::convertRules($data, $rules);
        } catch (SchemErrorException $e) {
            return [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }

        $newClass = new $scheme();
        foreach ((array)$funcs as $fnc) {
            // 过滤继承关系
            if ($fnc->class != $scheme) {
                continue;
            }
            // 获取其他数据
            $funcName = $fnc->name;
            if (strpos($funcName, 'get') !== false) {
                $gkey = substr($funcName, 3);
                if (in_array($gkey, $filter)) {
                    continue;
                }
                try {
                    $ret = call_user_func_array([$newClass, $funcName], [$data]);
                } catch (\Exception $e) {
                    return [
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
                $data[lcfirst($gkey)] = $ret;
            }
        }

        return $data;
    }
}

/**
 * 返回标准错误 code， message
 *
 * Class SchemErrorException
 * @package Common\Core
 */
class SchemErrorException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ':[' . $this->code . ']:' . $this->message . '\n';
    }


    // 返回全民的异常
    public function getSendAndExit()
    {
        return [
            'code'  => $this->getCode(),
            'error' => json_decode($this->getMessage(), true),
        ];
    }
}