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
            ['id', 'code', 'shop', 'times_used', 'status', 'discount_id', 'created_at', 'updated_at'],
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
        $discountId=Arr::get($data, 'discount_id');
        $attributes=[
            'code' => Arr::get($data, 'code'),
            'shop' => Arr::get($data, 'shop'),
            'discount_id' => Arr::get($data, 'discount_id'),
            'automatic' => Arr::get($data, 'automatic',0),
        ];

        $discount= Discount::find($discountId);
        if (!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
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
        Log::debug('Discount update request', [
            'id' => $id,
            'data' => $request->all(),
        ]);
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
            ], 404);
        }
        $data = array_filter([
            'code' => $request->input('code'),
            'shop' => $request->input('shop'),
            'discount_id' => $request->input('discount_id'),
        ], fn($value) => !is_null($value));

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


}
