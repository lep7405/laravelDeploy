<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = [
        'name', 'started_at', 'expired_at', 'type', 'value', 'usage_limit', 'trial_days', 'discount_month',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->hasMany(Coupon::class);
    }

}

