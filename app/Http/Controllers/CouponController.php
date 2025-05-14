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

}
