# Laravel Sms

供*Laravel5*使用的一个可支持多平台，易扩展的短信发送工具

仅作为短信发送工具，不涉及业务逻辑

## 环境要求

- php: >= 7.0
- ext-curl: *

## 目前支持的短信平台

- 阿里云

## 安装

Via Composer

```php
$  composer require daijulong/laravel-sms
```

composer.json

```php
"daijulong/laravel-sms": "~1.0"
```

如果Laravel5.5以下版本：

1. 在config/app.php文件中providers数组里加入：

    ```php
    Daijulong\LaravelSms\Providers\SmsServiceProvider::class,
    ```

1. 在config/app.php文件中的aliases数组里加入：

    ```php
    'Sms' => Daijulong\LaravelSms\SmsSender::class,
    ```

## 配置

生成配置文件：

```php
php artisan vendor:publish --provider="Daijulong\LaravelSms\Providers\SmsServiceProvider"
```

将在config目录下生成配置文件：sms.php，各配置项在此配置文件中有详细说明。

## 使用

> 以新建并使用一个验证码短信为例

1. 创建短信

    ```php
    php artisan sms:create Captcha
    ```
    
    > 短信名应首字母大写
    
    将在app/Sms目录下生成类文件：Captcha.php，内容如下：
    
    ```php
    <?php
    
    namespace App\Sms;
    
    use Daijulong\LaravelSms\Sms;
    
    class Captche extends Sms
    {
    
        /**
         * Content for agent : Content
         *
         * return string
         */
        protected function agentContent ()
        {
            return '';
        }
        
        /**
         * Content for agent : Aliyun
         *
         * return string
         */
        protected function agentAliyun ()
        {
            return '';
        }
        
    }
    ```
    
    > 自动生成的类中已包含若干个以“agent”开头的方法，方法的多少及内容以配置文件中的agents定义自动处理
    每个方法返回一个字符串，即为代理器发送时需要的短信内容（不包括变量，变量将在代理器中处理）
    不同短信平台要求的短信内容不同，有的要求直接发送内容，有的要建立短信模板传入短信模板编号，在此体现
    
    补全内容后如下：
    
    ```php
    <?php
    
    namespace App\Sms;
    
    use Daijulong\LaravelSms\Sms;
    
    class Captche extends Sms
    {
    
        /**
         * Content for agent : Content
         *
         * return string
         */
        protected function agentContent ()
        {
            return '【' . config('sms.sign') . '】验证码：${code}，打死也不能告诉别人';
        }
        
        /**
         * Content for agent : Aliyun
         *
         * return string
         */
        protected function agentAliyun ()
        {
            return 'SMS_12345678';
        }
        
    }
    ```
    
1. 发送短信
    
    代码片断
    
    ```php
    $sms = new App\Sms\Captche();
    $result = Sms::send('18012345678', $sms, ['code' => rand(100000,999999)]);
    ```
    
    发送后将返回 true（发送成功） 或 false （发送失败）。
    
    如需查看各代理器发送结果情况，可以：
    
    - ```Sms::getResults()```：所有代理器发送结果，按发送顺序
    - ```Sms::getResult()```：最后一个代理器发送结果，发送成功的代理器或最后一个发送失败的代理器
    - 查看日志，具体请参考后续“日志”相关内容
    
# 代理器

代理器是沟通项目和短信平台的桥梁，一般一个短信平台对应一个代理器。

如果本工具包提供的代理器不能满足项目需求，可以很方便地增加新的代理器。

如果自带的某代理器不能满足需求，可新增一个同名代理器来取而代之。

#### 代理器调用顺序

```
默认代理器（default_agent）  >  备用代理器（spare_agents）
``` 

发送短信时，将按以上代理器顺序依次发送短信，直到某代理器发送成功或全部发送失败。

#### 创建代理器

```php
php artisan sms:agent MyAgent
```

默认将在 app\Sms\Agents 目录下创建代理器类文件：MyAgent.php，其内容如下：

```php
<?php

namespace App\Sms\Agents;

use Daijulong\LaravelSms\Agent;
use Daijulong\Sms\Interfaces\Sms;

class MyAgent extends Agent
{

    /**
     * 代理器名称
     */
    protected $agent_name = 'MyAgent';

    /**
     * 发送短信
     *
     * @param string $to
     * @param Sms $sms
     * @param array $params
     * @return bool
     * @throws SmsException
     */
    public function send(string $to, Sms $sms, array $params = []): bool
    {
        $content = $this->getSmsContent($sms);
        // ... 具体发送操作


        // ... 记录发送情况
        $this->result
            ->setStatus()
            ->setMessage('')
            ->setContent($content)
            ->setParams($params)
            ->setReceiptId('')
            ->setReceiptData('');

        // ... return true or false
        // return true;
    }

}
```

只需要完善send方法即可，send方法执行流程为：

1. 取得本代理器发送短信所需要的内容

1. 发送短信，这里根据实际情况，可能需要处理短信内容和短信内容变量

1. 记录发送情况，模板代码已作演示，补全或选择性使用即可

1. 返回 true 或 false 来标记发送情况

#### 代理器配置

1. 创建的代理器需要在配置文件（config/sms.php）中```agents```项目中注册。

    ```php
    'agents' => [
        // ...
        
        'MyAgent' => [
            'key' => '',
        ],
        
        // ...
    ],
    ```
    
    > 在代理器中使用配置内容：```$this->config['key']```

1. 在配置文件（config/sms.php）的 default_agent 或 spare_agents 中设置其调用优先级。

#### 改变代理器调用顺序

某些特殊情况下需要临时调整发送短信的代理器。

1. 重新调整默认代理器和备用代理器
    
    ```php
    $sms = new App\Sms\Captche();
    $result = Sms::agent('MyAgent', ['Aliyun', 'Content'])->send('18012345678', $sms, ['code' => rand(100000,999999)]);
    ```
    
1. 仅使用指定代理器发送短信，其他代理器不再参与发送

    ```php
    $sms = new App\Sms\Captche();
    $result = Sms::onlyAgent('MyAgent')->send('18012345678', $sms, ['code' => rand(100000,999999)]);    
    ```

> 注：以上调整仅针对当次发送有效

# 日志

日志功能默认开启，配置文件的```log_enable```为日志开关。

日志支持以下驱动：

1. 文件：file

    发送日志文件将存储在```Storage::disk('local')```下的sms目录中，每天生成一个日志文件。
    
2. 数据库：db

    发送记录将存储于数据库中，使用此驱动前，应先创建日志表。
    
    生成migration文件：
    
    ```php
    php artisan sms:table
    ```
    
    创建表：
    
    ```php
    php artisan migrate
    ```

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.