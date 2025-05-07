<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = ['code', 'shop', 'discount_id', 'times_used', 'status', 'automatic'];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}

