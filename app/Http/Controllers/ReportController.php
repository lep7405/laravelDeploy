<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Service\DiscountService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReportController extends Controller
{
    public function __construct(protected DiscountService $discountService, protected CouponService $couponService)
    {
    }
    public function index(Request $request){
        $data = $request->all();
        $discountStats = $this->processDiscountData($data);

        $couponStats = $this->processCouponData($data);

        $summaryStats = $this->calculate();

        $result= array_merge($discountStats, $couponStats, $summaryStats);
        return response()->json([
            'message' => 'Report retrieved successfully',
            'report' => $result,
        ], 200);
    }
    private function processDiscountData($data): array
    {

        $result = $this->discountService->index($data);


        foreach ($result->discountData as $discount) {
            $totalCoupon = 0;
            $totalCouponUsed = 0;
            foreach ($discount->coupon as $coupon) {
                $totalCoupon++;
                if($coupon->times_used > 0){
                    $totalCouponUsed ++;
                }
            }
            $discount->setAttribute('totalCoupon', $totalCoupon);
            $discount->setAttribute('totalCouponUsed', $totalCouponUsed);
        }
        return $result;
    }
    private function processCouponData($data): array
    {
        return  $this->couponService->index($data);
    }

    private function calculate(): array
    {
        $discounts = Discount::query()->select('id')
            ->with(['coupon' => function ($query) {
                $query->select('id', 'times_used', 'discount_id');
            }])
            ->get();;
        $countDiscount = $discounts->count();
        $countDiscountUsed = 0;
        $countCoupon = 0;
        $countCouponUsed = 0;
        foreach ($discounts as $discount) {
            $total = 0;
            foreach ($discount->coupon as $coupon) {
                $countCoupon++;
                $total += $coupon->times_used;
            }

            $countCouponUsed += $total;

            if ($total > 0) {
                $countDiscountUsed++;
            }
        }
        return [
            'countDiscount' => $countDiscount,
            'countDiscountUsed' => $countDiscountUsed,
            'countCoupon' => $countCoupon,
            'countCouponUsed' => $countCouponUsed,
        ];
    }
}
