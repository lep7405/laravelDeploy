<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    public function store(Request $request)
    {
        $discount = Discount::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);
        return response()->json([
            'message' => 'Discount created successfully',
            'discount' => $discount,
        ]);
    }
    public function index(Request $request){
        Log::debug('✅ Laravel Cloud log test');
        $perPage = Arr::get($request->all(), 'perPageDiscount');
        $search = Arr::get($request->all(), 'searchDiscount');
        $sortStartedAt = Arr::get($request->all(), 'sortStartedAt');
        $pageDiscount = Arr::get($request->all(), 'pageDiscount');
        $withCoupon = Arr::get($request->all(), 'withCoupon', false);

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
                        $inner->where('type', 'percentage')
                            ->where('value', str_replace('%', '', $search));
                    })
                        ->orWhere(function ($inner) use ($search) {
                            $inner->where('type', 'amount')
                                ->where('value', $search);
                        });
                });
            });
        }

        if ($sortStartedAt && in_array($sortStartedAt, ['asc', 'desc'])) {
            $query = $query->orderBy('started_at', $sortStartedAt);
        }

        $query = $query->orderBy('id', 'desc');

        $result = $query->paginate(
            $perPage,
            ['id','name','started_at','expired_at','type','value','usage_limit','trial_days'],
            'pageDiscount',
            $pageDiscount
        );

        return response()->json([
            'message' => 'Discounts retrieved successfully',
            'discounts' => $result,
        ],200);
    }
}
