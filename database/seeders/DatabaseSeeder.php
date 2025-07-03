<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Discount;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Discount::factory()->count(1000)->create()->each(function ($discount) {
            // For each Discount, create 1 to 5 Coupons
            $couponCount = rand(1, 5);
            Coupon::factory()->count($couponCount)->create([
                'discount_id' => $discount->id,
            ]);
        });
    }
}
