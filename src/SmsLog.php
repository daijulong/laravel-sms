<?php

namespace Daijulong\LaravelSms;

use Daijulong\Sms\Supports\SmsResult;
use Illuminate\Support\Facades\Storage;
use Daijulong\LaravelSms\Models\SmsLog as SmsLogModel;

class SmsLog
{

    /**
     * 保存日志
     *
     * @static
     * @param string $mobile
     * @param array $send_results
     * @param string $send_batch
     */
    public static function save(string $mobile, array $send_results, string $send_batch)
    {
        $driver = config('sms.log_driver', 'file');
        switch ($driver) {
            case 'db':
                self::saveInDb($mobile, $send_results, $send_batch);
                break;
            case 'file':
                self::saveInFile($mobile, $send_results, $send_batch);
                break;
            default:

        }
    }

    /**
     * 保存日志到数据库
     *
     * 使用此功能前，应先创建对应的数据表
     *
     * @static
     * @param string $mobile
     * @param array $send_results
     * @param string $send_batch
     */
    private static function saveInDb(string $mobile, array $send_results, string $send_batch = '')
    {
        if (!empty($send_results)) {
            foreach ($send_results as $send_result) {
                if ($send_result instanceof SmsResult) {
                    SmsLogModel::create(self::transLogData($mobile, $send_result, $send_batch));
                }
            }
        }
    }


    /**
     * 保存日志至文件
     *
     * @static
     * @param string $mobile
     * @param array $send_results
     * @param string $send_batch
     */
    private static function saveInFile(string $mobile, array $send_results, string $send_batch = '')
    {
        $log_content = "-------------------------------------------------------------------\n";
        $log_content .= '+ batch:' . $send_batch . "\n";
        $log_content .= '+ mobile:' . $mobile . "\n";
        $log_content .= "-------------------------------------------------------------------\n";
        if (!empty($send_results)) {
            foreach ($send_results as $send_result) {
                if ($send_result instanceof SmsResult) {
                    collect(self::transLogData($mobile, $send_result, $send_batch))->each(function ($value, $key) use (&$log_content) {
                        if ($key != 'batch' && $key != 'mobile') {
                            $log_content .= ($key == 'agent' ? '+  ' : '   ') . $key . ':' . $value . "\n";
                        }
                    });
                }
            }
        }
        Storage::disk('local')->append('sms/' . date('Y/md') . '.log', $log_content);
    }

    /**
     * 转换发送结果数据
     *
     * @static
     * @param string $mobile
     * @param SmsResult $result
     * @param string $send_batch
     * @return array
     */
    private static function transLogData(string $mobile, SmsResult $result, string $send_batch = '')
    {
        return [
            'batch' => $send_batch,
            'mobile' => $mobile,
            'agent' => $result->getAgent(),
            'status' => $result->getStatusText(),
            'message' => $result->getMessage(),
            'content' => $result->getContent(),
            'params' => json_encode($result->getParams()),
            'receipt_id' => $result->getReceiptId(),
            'receipt_data' => json_encode($result->getReceiptData()),
        ];
    }
}