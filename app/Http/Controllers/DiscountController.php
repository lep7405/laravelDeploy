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
        $startedAt = Arr::get($request->all(), 'startedAt');
        $pageDiscount = Arr::get($request->all(), 'pageDiscount');
        $query = Discount::query();
        $query = $query->when($search, function ($query) use ($search) {
            $query
                ->where(function ($sub) use ($search) {
                    if ($search) {
                        $escapedSearch = addcslashes($search, '%_');
                        $sub->where('name', 'like', "%{$escapedSearch}%");
                        $sub->orWhere('started_at', 'like', "%{$escapedSearch}%");
                        $sub->orWhere('expired_at', 'like', "%{$escapedSearch}%");
                    }
                })
                ->orWhere(function ($sub) use ($search) {
                    if (is_numeric($search)) {
                        $sub->where('id', $search);
                    }
                })
                ->orWhere(function ($sub) use ($search) {
                    if ($search) {
                        $cleanSearch = str_replace('%', '', $search);
                        $sub->where('value', $cleanSearch);
                    }
                })
            ;
        });
        $query = $query->when($startedAt, function ($query) use ($startedAt) {
            $query->orderBy('started_at', $startedAt);
        });

        // Thêm orderBy
        $query = $query->orderBy('id', 'desc');

        // Kết thúc với paginate
        $query = $query->paginate($perPage, ['*'], 'pageDiscount', $pageDiscount);
        Log::info('query', ['query' => $query]);
        return response()->json([
            'message' => 'Discounts retrieved successfully',
            'discounts' => $query,
        ]);
    }
}
