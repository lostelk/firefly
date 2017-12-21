<?php
/**
 * ParamTrait
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/9/27 下午12:29
 * @logs   :
 *
 */

namespace FireFly\Traits;

trait ParamTrait
{
    /**
     * 验证接口参数
     *
     * $rules
     * @return array|bool
     */
    public function checkRules($params, $rules)
    {
        if (empty($rules)) {
            return true;
        }

        $errors = [];

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
                } elseif (substr($rule_item, 0, 8) === 'default:') {
                    $rule['default'] = substr($rule_item, 8);
                }
            }

            //必须项
            if (!empty($rule['required']) && !isset($params[$key])) {
                $errors[] = $key . ' 参数丢失';
                break;
            }

            if (empty($rule['required']) && !isset($params[$key])) {
                // 目前必须显示使用default，否则老的isset判断会失效
                if (isset($rule['type']) && isset($rule['default'])) {
                    $rule['default'] = isset($rule['default']) ? $rule['default'] : '';
                    if ($rule['type'] == 'int') {
                        $params[$key] = (int)$rule['default'];
                    } elseif ($rule['type'] == 'string') {
                        $params[$key] = (string)$rule['default'];
                    }
                }
                continue;
            }

            //数值型
            if ($rule['type'] == 'int') {
                if (!is_int($params[$key])) {
                    $errors[] = $key . ' 必须是数值型';
                    break;
                }
                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
                if (!empty($rule['max'])) {
                    if ($params[$key] > $rule['max']) {
                        $errors[] = $key . ' 不能大于' . $rule['max'];
                        break;
                    }
                }
                if (!empty($rule['min'])) {
                    if ($params[$key] < $rule['min']) {
                        $errors[] = $key . ' 不能小于' . $rule['min'];
                        break;
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
                    $errors[] = $key . ' 必须是字符串';
                    break;
                }
                if (empty($rule['min']) && empty($rule['max'])) {
                    continue;
                }
                if (!empty($rule['max'])) {
                    if (mb_strlen($params[$key]) > $rule['max']) {
                        $errors[] = $key . ' 长度不能大于' . $rule['max'];
                        break;
                    }
                }
                if (!empty($rule['min'])) {
                    if (mb_strlen($params[$key]) < $rule['min']) {
                        $errors[] = $key . ' 长度不能小于' . $rule['min'];
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}

