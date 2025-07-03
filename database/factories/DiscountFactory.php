<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true), // Tạo tên ngẫu nhiên gồm 3 từ
            'started_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Ngày bắt đầu trong vòng 1 năm trước
            'expired_at' => $this->faker->dateTimeBetween('now', '+1 year'), // Ngày hết hạn trong vòng 1 năm tới
            'type' => $this->faker->randomElement(['percentage', 'amount']), // Loại giảm giá: percentage hoặc fixed
            'value' => $this->faker->randomFloat(2, 5, 100), // Giá trị giảm giá từ 5.00 đến 100.00
            'usage_limit' => $this->faker->numberBetween(1, 100), // Giới hạn sử dụng từ 1 đến 100
            'trial_days' => $this->faker->numberBetween(0, 30), // Số ngày dùng thử từ 0 đến 30
            'discount_month' => $this->faker->numberBetween(1, 12), // Tháng giảm giá từ 1 đến 12
        ];
    }
}
