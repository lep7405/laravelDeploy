<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{8}'), // Mã coupon 8 ký tự ngẫu nhiên (chữ hoa và số)
            'shop' => $this->faker->randomElement(['shop1', 'shop2', 'shop3', 'shop4', 'shop5']), // Cửa hàng ngẫu nhiên
            'discount_id' => null, // Sẽ được gán trong seeder
            'times_used' => $this->faker->numberBetween(0, 50), // Số lần sử dụng từ 0 đến 50
            'status' => $this->faker->randomElement(['0', '1']), // Trạng thái: active hoặc inactive
            'automatic' => $this->faker->boolean, // True hoặc False ngẫu nhiên
        ];
    }
}
