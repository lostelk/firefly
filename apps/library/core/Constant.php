<?php

/**
 * constant.php
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 2017/9/26 下午5:33
 * @logs   :
 *
 *  1手机，2微信，3ＱＱ，4微博，5支付宝，6微信公众号，7微信PC, 8帐密，9邮箱，10百度账号
 *
 */

namespace Core;

class Constant
{
    const ERROR_NO_AUTH = 250; // SIGN ERROR
    const ERROR_USER_MODIFY = 251;
    const ERROR_USER_BAD_NICKNAME = 252;
    const ERROR_USER_BAD_PORTRAIT = 253;
    const ERROR_RISK_NOT_FOUND = 254;
    const ERROR_PARAM_ERROR = 1000;

    const ERROR_AUTH_CHECK_PARAM = 80000; // check param
    const ERROR_AUTH_BAN_LOGIN = 80001; // 禁止登录
    const ERROR_USER_NOT_EXITS = 80002; // 用户不存在
    const ERROR_USER_PASS_ERROR = 80003; // 用户密码错误

    const ACCOUNT_TYPE_MOBILE = 1; // 手机登录
    const ACCOUNT_TYPE_WECHAT = 2; // 微信登录
    const ACCOUNT_TYPE_QQ = 3; // QQ 登录
    const ACCOUNT_TYPE_WEIBO = 4; // 微博登录
    const ACCOUNT_TYPE_PASSWORD = 8; // 账号密码登录
    const ACCOUNT_TYPE_WECHAT_APP = 11; // 微信小程序

    const ERROR_AUTH_MOBILE_WRONG = 70001; // 手机号错误
    const ERROR_AUTH_SMS_WRONG = 70002; // 短信发送错误
    const ERROR_AUTH_UN_REGISTERED = 70003; // 未注册用户
}