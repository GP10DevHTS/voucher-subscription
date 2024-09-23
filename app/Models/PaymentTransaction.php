<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'package_id',
        'status',
        'uuid',
        'ipn_id',
        'voucher_id',
        'order_tracking_id',
        'payment_method',
        'amount',
        'currency',
        'confirmation_code',
        'paid_at',
        'payment_account',
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            $model->uuid = Str::uuid();
            $model->save();
        });
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
