<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class fakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fake-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($i = 0; $i < 1000; $i++) {
            $discountId = DB::table('discounts')->insertGetId([
                'name' => 'Discount ' . ($i + 1),
                'started_at' => now()->subDays(rand(1, 365)),
                'expired_at' => now()->addDays(rand(1, 365)),
                'type' => rand(0, 1) ? 'percentage' : 'amount',
                'value' => rand(500, 10000) / 100, // Giá trị từ 5.00 đến 100.00
                'usage_limit' => rand(1, 100),
                'trial_days' => rand(0, 30),
                'discount_month' => rand(1, 12),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Chèn 1-5 Coupons cho mỗi Discount
            $couponCount = rand(1, 5);
            for ($j = 0; $j < $couponCount; $j++) {
                DB::table('coupons')->insert([
                    'code' => 'COUPON' . ($i + 1) . ($j + 1) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'shop' => 'shop' . rand(1, 5),
                    'discount_id' => $discountId,
                    'times_used' => rand(0, 50),
                    'status' => rand(0, 1),
                    'automatic' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
