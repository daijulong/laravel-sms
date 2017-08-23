<?php

namespace Daijulong\LaravelSms;

use Daijulong\Sms\Interfaces\Sms as SmsInterface;

/**
 * 短信基类
 *
 * 所有短信都应继承自此基类
 *
 * @package Daijulong\LaravelSms
 */
abstract class Sms implements SmsInterface
{
    /**
     * 取得短信内容（或模板）
     *
     * 每个短信对象都应包括所有使用到的代理器，每个
     * 代理器对应一个方法，命名规则为：agent + 代理器名
     * 该方法返回代理器所需要的内容，有的是短信文本，
     * 有的是短信服务平台所建立的模板编号
     *
     * @param string $agent
     * @return string
     */
    public function content($agent): string
    {
        $method_name = 'agent' . ucfirst($agent);
        return method_exists($this, $method_name) ? $this->$method_name() : '';
    }
}