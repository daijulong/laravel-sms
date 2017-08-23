<?php

namespace Daijulong\LaravelSms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'batch', 'mobile', 'agent', 'status', 'message', 'content', 'params', 'receipt_id', 'receipt_data'
    ];
}
