<?php

namespace Daijulong\LaravelSms;

use Daijulong\Sms\Exceptions\SmsException;
use Daijulong\Sms\Interfaces\Agent as AgentInterface;
use Daijulong\Sms\Interfaces\Sms as SmsInterface;
use Daijulong\Sms\Traits\AgentSendResult;


/**
 * 短信代理器基类
 *
 * 所有代理器都应继承自此基类
 *
 * @package Daijulong\LaravelSms
 */
abstract class Agent implements AgentInterface
{

    use AgentSendResult;

    /**
     * 代理器名称
     */
    protected $agent_name = '';

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

    /**
     * 发送短信
     *
     * @param string $to
     * @param Daijulong\Sms\Interfaces\Sms $sms
     * @param array $params
     * @return bool
     * @throws SmsException
     */
    abstract public function send(string $to, SmsInterface $sms, array $params = []): bool;

    /**
     * 获取代理器所需要的短信内容
     *
     * @param Sms $sms
     * @return string
     * @throws SmsException
     */
    protected function getSmsContent(Sms $sms)
    {
        $content = $sms->content($this->agent_name);
        if (!$content) {
            throw new SmsException('The agent not supported by SMS');
        }
        return $content;
    }
}