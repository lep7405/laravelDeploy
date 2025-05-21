<?php

namespace App\Services;

use App\Models\Discount;
use Illuminate\Support\Arr;

class DiscountService
{
    public function index($filters)
    {
        $perPage = Arr::get($filters, 'perPageDiscount',5);
        $search = Arr::get($filters, 'searchDiscount');
        $sortStartedAt = Arr::get($filters, 'sortStartedAt');
        $pageDiscount = Arr::get($filters, 'pageDiscount',1);
        $withCoupon = Arr::get($filters, 'withCoupon', false);

        $query = Discount::query();

        if ($withCoupon) {
            $query = $query->with(['coupon' => function ($query) {
                $query->select('id', 'times_used', 'discount_id');
            }]);
        }

        if ($search) {
            $query = $query->where(function ($sub) use ($search) {
                $escapedSearch = addcslashes($search, '%_');
                $sub->where('name', 'like', "%{$escapedSearch}%")
                    ->orWhere('started_at', 'like', "%{$escapedSearch}%")
                    ->orWhere('expired_at', 'like', "%{$escapedSearch}%");

                if (is_numeric($search)) {
                    $sub->orWhere('id', $search);
                }

                $sub->orWhere(function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        if (is_numeric(str_replace('%', '', $search))) {
                            $inner->where('type', 'percentage')
                                ->where('value', str_replace('%', '', $search));
                        };
                    });
                    if (is_numeric($search)) {
                        $q->orWhere(function ($inner) use ($search) {
                            $inner->where('type', 'amount')
                                ->where('value', $search);
                        });
                    };
                });
            });
        }

        if ($sortStartedAt && in_array($sortStartedAt, ['asc', 'desc'])) {
            $query = $query->orderBy('started_at', $sortStartedAt);
        }

        $query = $query->orderBy('id', 'desc');
        if ($perPage == -1) {
            $result = $query->get(['id','name','started_at','expired_at','type','value','usage_limit','trial_days']);
            return [
                'discounts' => $result,
                'totalPages' => 1,
                'totalItems' => $result->count(),
                'currentPages' => 1,
            ];
        }

        $result = $query->paginate(
            $perPage,
            ['id','name','started_at','expired_at','type','value','usage_limit','trial_days'],
            'pageDiscount',
            $pageDiscount
        );

        return [
            'discounts' => $result->items(),
            'totalPages' => $result->lastPage(),
            'totalItems' => $result->total(),
            'currentPage' => $result->currentPage(),
        ];
    }
}
