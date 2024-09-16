<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'package_id', 'is_sold'];

    public function package()
    {
        return $this->belongsTo(Package::class)->withTrashed();
    }
}
