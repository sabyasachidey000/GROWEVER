<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rejection;
use Illuminate\Http\Request;

class RejectionApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Rejection::with(['product', 'machine']);

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        return response()->json($query->latest('rejection_date')->paginate(25));
    }
}
