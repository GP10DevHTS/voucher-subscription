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
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            $model->uuid = Str::uuid();
            $model->save();
        });
    }
}
