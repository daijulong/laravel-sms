<?php

namespace Daijulong\LaravelSms;

use Daijulong\Sms\Interfaces\Sms;
use Daijulong\Sms\Supports\SmsAgent;
use Daijulong\Sms\Supports\SmsConfig;

class SmsSender
{

    /**
     * 发送者
     */
    private $sender;

    public function __construct()
    {
        SmsConfig::load(config('sms'));
        SmsAgent::init();
        $this->sender = new \Daijulong\Sms\SmsSender();
    }

    /**
     * 指定代理器
     *
     * @param string $agent
     * @param array $spare_agents
     * @return $this
     */
    public function agent(string $agent, array $spare_agents = [])
    {
        $this->sender->agent($agent, $spare_agents);
        return $this;
    }

    /**
     * 指定唯一代理器
     *
     * 如果指定的代理器发送失败，将不再尝试使用备用代理器
     *
     * @param string $agent
     * @return $this
     */
    public function onlyAgent(string $agent)
    {
        $this->sender->onlyAgent($agent);
        return $this;
    }

    /**
     * 发送短信
     *
     * @param string $to
     * @param Sms $sms
     * @param array $params
     * @return bool
     */
    public function send(string $to, Sms $sms, array $params = [])
    {
        $batch = date('YmdHis-') . uniqid();
        $send_result = $this->sender->to($to)->sms($sms)->params($params)->send();
        if (config('sms.log_enable')) {
            SmsLog::save($to, $this->sender->getResults(), $batch);
        }
        return $send_result;
    }

    /**
     * 取得所有发送结果
     *
     * @return array
     */
    public function getResults()
    {
        return $this->sender->getResults();
    }

    /**
     * 取得最终发送结果
     *
     * @return Daijulong\Sms\Supports\SmsResult
     */
    public function getResult()
    {
        return $this->sender->getResult();
    }
}