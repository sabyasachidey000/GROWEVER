<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductApiController extends Controller
{
    public function index()
    {
        return response()->json(Product::active()->orderBy('product_code')->paginate(25));
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }
}
