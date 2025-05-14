<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index(Request $request){
        $data=$request->all();
        $perPage = Arr::get($data, 'perPageCoupon');
        $pageCoupon = Arr::get($data, 'pageCoupon', 1);
        $search = Arr::get($data, 'searchCoupon');
        $status = Arr::get($data, 'status');
        $sortTimesUsed = Arr::get($data, 'timesUsed');
        $discountId = Arr::get($data, 'discountId');

        $query=Coupon::query();
        $query = $query->with(['discount:id,name']);

        if ($discountId) {
            $query = $query->where('discount_id', $discountId);
        }

        if ($search) {
            $query = $query->where(function ($sub) use ($search) {
                $escapedSearch = addcslashes($search, '%_');
                $sub->where('code', 'like', "%{$escapedSearch}%")
                    ->orWhere('shop', 'like', "%{$escapedSearch}%")
                    ->orWhereHas('discount', function ($q) use ($escapedSearch) {
                        $q->where('name', 'like', "%{$escapedSearch}%");
                    });

                if (is_numeric($search)) {
                    $sub->orWhere('id', $search)
                        ->orWhere('times_used', $search);
                }
            });
        }
        if ($status !== null) {
            $query = $query->where('status', $status);
        }
        if ($sortTimesUsed && in_array($sortTimesUsed, ['asc', 'desc'])) {
            $query = $query->orderBy('times_used', $sortTimesUsed);
        }
        $query = $query->orderBy('id', 'desc');
        $result = $query->paginate(
            $perPage,
            ['id', 'code', 'shop', 'times_used', 'status', 'discount_id'],
            'pageCoupon',
            $pageCoupon
        );
        return response()->json([
            'message' => 'Coupons retrieved successfully',
            'coupons' => $result,
        ], 200);
    }
    public function store(Request $request){
        $data = $request->all();
        $attributes=[
            'code' => Arr::get($data, 'code'),
            'shop' => Arr::get($data, 'shop'),
            'discount_id' => Arr::get($data, 'discountId'),
            'automatic' => Arr::get($data, 'automatic',0),
        ];
        $coupon = Coupon::create($attributes);
        return response()->json([
            'message' => 'Coupon created successfully',
            'coupon' => $coupon,
        ], 201);
    }

    public function findCouponById($id){
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Coupon retrieved successfully',
            'discount' => $coupon,
        ], 200);
    }
    public function findCouponByCode($code){
        $coupon = Coupon::where('code', $code)->first();
        return response()->json([
            'message' => 'Coupon retrieved successfully',
            'coupon' => $coupon,
        ], 200);
    }

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
        $discount->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'usage_limit' => $request->input('usage_limit'),
            'trial_days' => $request->input('trial_days'),
            'started_at' => $request->input('started_at'),
            'expired_at' => $request->input('expired_at'),
            'discount_month' => $request->input('discount_month'),
        ]);

        return response()->json([
            'message' => 'Discount updated successfully',
            'discount' => $discount,
        ], 200);
    }

}
