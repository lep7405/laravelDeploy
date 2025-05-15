<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    public function __construct(protected DiscountService $discountService)
    {
    }
    public function index(Request $request){
        $result = $this->discountService->index($request->query());
        return response()->json([
            'message' => 'Discounts retrieved successfully',
            'discounts' => $result,
        ],200);
    }
    // Nếu có cột discount_month thì mới thêm vào create
    public function store(Request $request)
    {
        $data = array_filter([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'usage_limit' => $request->input('usage_limit'),
            'trial_days' => $request->input('trial_days'),
            'started_at' => $request->input('started_at'),
            'expired_at' => $request->input('expired_at'),
            'discount_month' => $request->input('discount_month'),
        ], fn($value) => !is_null($value));
        $discount = Discount::create($data);
        return response()->json([
            'message' => 'Discount created successfully',
            'discount' => $discount,
        ],201);
    }
    // Nếu có cột discount_month thì mới thêm vào update
    public function update(Request $request, $id)
    {
        Log::debug('Discount update request', [
            'id' => $id,
            'data' => $request->all(),
        ]);
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }

        $data = array_filter([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'usage_limit' => $request->input('usage_limit'),
            'trial_days' => $request->input('trial_days'),
            'started_at' => $request->input('started_at'),
            'expired_at' => $request->input('expired_at'),
            'discount_month' => $request->input('discount_month'),
        ], fn($value) => !is_null($value));
        $discount->update($data);

        return response()->json([
            'message' => 'Discount updated successfully',
            'discount' => $discount,
        ], 200);
    }
    public function destroy($id)
    {
        Coupon::query()->where('discount_id', $id)->delete();
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }
        $discount->delete();
        return response()->json([
            'message' => 'Discount deleted successfully',
        ], 200);
    }
    public function findDiscountById(Request $request,$id){
        $withCoupon= $request->input('withCoupon', false);
        $discount = Discount::query()
            ->when($withCoupon, function ($query) {
                $query->with(['coupon' => function ($query) {
                    $query->select('id', 'times_used', 'discount_id');
                }]);
            })
            ->find($id);
        if (!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Discount retrieved successfully',
            'discount' => $discount,
        ], 200);
    }

    public function totalDiscounts(){
        $total = Discount::count();
        return response()->json([
            'message' => 'Total discounts retrieved successfully',
            'total' => $total,
        ], 200);
    }

    public function getIdAndName(){
        $discounts = Discount::all(['id', 'name']);
        return response()->json([
            'message' => 'All discounts retrieved successfully',
            'discounts' => $discounts,
        ], 200);
    }

    public function getDiscountsWithCoupons(){
        $discounts=Discount::query()->select('id')
            ->with(['coupon' => function ($query) {
                $query->select('id', 'times_used', 'discount_id');
            }])
            ->get();
        return response()->json([
            'message' => 'All discounts retrieved successfully',
            'discounts' => $discounts,
        ], 200);
    }

}
