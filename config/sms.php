<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 短信签名
    |--------------------------------------------------------------------------
    |
    | 某些以纯文本为内容直接发送短信的代理器可能需要使用短信签名
    | 在此直接设置
    */
    'sign' => '短信签名',

    /*
    |--------------------------------------------------------------------------
    | 默认代理器
    |--------------------------------------------------------------------------
    |
    | 短信发送默认代理器，应为配置项“agents”中代理器之一
    */
    'default_agent' => 'Content',

    /*
    |--------------------------------------------------------------------------
    | 备用代理器
    |--------------------------------------------------------------------------
    |
    | 短信发送备用代理器，每个代理器应为配置项“agents”中代理器之一
    | 在默认代理器发送失败时，将按顺序使用这些备用代理器进行尝试发送，
    | 直到其中之一发送成功或全部发送失败
    */
    'spare_agents' => [
        'Aliyun',
    ],

    /*
    |--------------------------------------------------------------------------
    | 代理器
    |--------------------------------------------------------------------------
    |
    | 可供直接使用的代理器
    | 每个代理器应配置好连接服务端和其他所需要的参数
    | 也可通过扩展机制来增加新的代理器
    */
    'agents' => [
        //纯文本内容代理器，一般用作调试时使用，永远“发送成功”
        'Content' => [

        ],
        //阿里云代理器
        'Aliyun' => [
            'access_key_id' => env('SMS_AGENT_ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' => env('SMS_AGENT_ALIYUN_ACCESS_KEY_SECRET'),
        ],
        // ...
    ],

    /*
    |--------------------------------------------------------------------------
    | 超时时间（秒）
    |--------------------------------------------------------------------------
    |
    | 接收数据最大时间
    */
    'timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | 连接超时时间（秒）
    |--------------------------------------------------------------------------
    |
    | 在设置时间内如短信服务平台未响应则断开连接
    */
    'connect_timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | 扩展代理器命名空间
    |--------------------------------------------------------------------------
    |
    | 用于声明自定义的短信发送代理器
    | 此命名空间下的代理器将根据代理器设置（agents）进行注册
    | 如有同名情况，则此命名空间下的代理器将取代默认提供的代理器
    */
    'agent_ext_namespace' => 'App\\Sms\\Agents\\',

    /*
    |--------------------------------------------------------------------------
    | 是否开启发送日志
    |--------------------------------------------------------------------------
    |
    | 如开启，将在发送结束后记录所有发送情况
    | 如一条短信经过尝试多个代理器才发送成功，则也将会记录发送失败的情况
    */
    'log_enable' => true,

    /*
    |--------------------------------------------------------------------------
    | 发送日志驱动
    |--------------------------------------------------------------------------
    |
    | file : 将以 Y/md.log 作为文件名，每天一个日志文件
    |        存放目录：Storage::disk('local')下的 sms 目录
    | db   : 存入数据库，使用此驱动前务必创建对应日志表，可使用提供的migration
    */
    'log_driver' => 'file',
];