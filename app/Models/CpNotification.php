<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpNotification extends Model
{
    use HasFactory, ModelBase;

    protected $table = 'cp_notifications';

    const CHECK     = 1;
    const PAY_FAIL  = 2;
    const CONFIRM   = 3;
    const REFUND    = 4;
    const RECURRENT = 5;
    const CANCEL    = 6;

    protected $fillable = [
        'transaction_id',
        'request',
        'type',
    ];

    protected $casts = [
        'request' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
