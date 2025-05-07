<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

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
}
