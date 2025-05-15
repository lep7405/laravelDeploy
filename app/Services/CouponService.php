<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Arr;

class CouponService {
    public function index($filters){
        $perPage = Arr::get($filters, 'perPageCoupon');
        $pageCoupon = Arr::get($filters, 'pageCoupon', 1);
        $search = Arr::get($filters, 'searchCoupon');
        $status = Arr::get($filters, 'status');
        $sortTimesUsed = Arr::get($filters, 'timesUsed');
        $discountId = Arr::get($filters, 'discountId');

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
        if ($perPage == -1) {
            $result = $query->get(['id', 'code', 'shop', 'times_used', 'status', 'discount_id', 'created_at', 'updated_at']);
            return [
                'couponData' => $result,
                'totalPagesCoupon' => 1,
                'totalItemsCoupon' => $result->count(),
                'currentPagesCoupon' => 1,
            ];
        }
        $result = $query->paginate(
            $perPage,
            ['id', 'code', 'shop', 'times_used', 'status', 'discount_id', 'created_at', 'updated_at'],
            'pageCoupon',
            $pageCoupon
        );
        return [
            'couponData' => $result->items(),
            'totalPagesCoupon' => $result->lastPage(),
            'totalItemsCoupon' => $result->total(),
            'currentPagesCoupon' => $result->currentPage(),
        ];
    }


}
