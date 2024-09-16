<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
    ];

    protected static function booted(){

        static::created(function ($model) {
            $model->slug = uniqid();
            $model->save();
        });
        
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'package_id')->where('is_sold', false);
    }
    
}
