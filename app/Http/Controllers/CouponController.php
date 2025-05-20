<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Discount;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index(Request $request,CouponService $couponService){
        $data=$request->all();
        $result=$couponService->index($data);
        return response()->json([
            'message' => 'Coupons retrieved successfully',
            'data' => $result,
        ], 200);
    }
    public function store(Request $request){
        $data = $request->all();
        $discountId=Arr::get($data, 'discount_id');

        $attributes = $request->only([
            'code', 'shop', 'discount_id' ,'automatic', 'times_used'
        ]);

        $discount= Discount::find($discountId);
        if (!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }
        $coupon = Coupon::where('code', $attributes['code'])
            ->first();
        if ($coupon) {
            return response()->json([
                'message' => 'Coupon created failed',
                'errors' => [
                    'code' => 'Coupon code already exists',
                ],
            ], 422);
        }
        $coupon = Coupon::create($attributes);
        return response()->json([
            'message' => 'Coupon created successfully',
            'coupon' => $coupon,
        ], 201);
    }

    public function findCouponById(Request $request,$id){
        $withDiscount= $request->input('withDiscount', false);
        $coupon = Coupon::query()
            ->when($withDiscount, function ($query) {
                $query->with(['discount' => function ($query) {
                    $query->select('id', 'name');
                }]);
            })
            ->find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Coupon retrieved successfully',
            'coupon' => $coupon,
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
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        $couponCheck = Coupon::where('code', $request->input('code'))
            ->first();
        if ($couponCheck && $couponCheck->id != $id) {
            return response()->json([
                'message' => 'Coupon code already exists',
                'errors' => [
                    'code' => 'Coupon code already exists',
                ],
            ], 422);
        }

        $data = $request->only([
            'code', 'shop', 'discount_id'
        ]);
        $coupon->update($data);

        return response()->json([
            'message' => 'Coupon updated successfully',
            'coupon' => $coupon,
        ], 200);
    }

    public function updateStatus($id){
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        $coupon->status = !$coupon->status;
        $coupon->save();
        return response()->json([
            'message' => 'Coupon status updated successfully',
            'coupon' => $coupon,
        ], 200);
    }

    public function decrementTimesUsed(Request $request, $id)
    {
        $data = $request->all();
        $numDecrement = Arr::get($data, 'numDecrement');
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        if ($coupon->times_used < $numDecrement) {
            return response()->json([
                'message' => 'Cannot decrement times used more than available',
            ], 400);
        }
        $coupon->times_used -= $numDecrement;
        $coupon->save();
        return response()->json([
            'message' => 'Coupon times used decremented successfully',
            'coupon' => $coupon,
        ], 200);
    }

    public function destroy($id){
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        if ($coupon->times_used && $coupon->times_used > 0) {
            return response()->json([
                'message' => 'Cannot delete coupon that has been used',
            ], 400);
        }
        $coupon->delete();
        return response()->json([
            'message' => 'Coupon deleted successfully',
        ], 200);
    }

    public function findCouponByDiscountIdAndCode($id){
        $coupon= Coupon::where('discount_id', $id)
            ->where('code', 'like','GENAUTO'.'%')
            ->get();
        return response()->json([
            'message' => 'Coupons retrieved successfully',
            'coupon' => $coupon,
        ], 200);
    }
    public function findCouponByDiscountIdAndShop($id,$shop){
        $coupon= Coupon::where('discount_id', $id)
            ->where('shop', $shop)
            ->orderBy('created_at', 'desc')
            ->first();
        return response()->json([
            'message' => 'Coupons retrieved successfully',
            'coupon' => $coupon,
        ], 200);
    }

}
