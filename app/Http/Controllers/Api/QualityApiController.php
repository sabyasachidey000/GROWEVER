<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QualityInspection;
use Illuminate\Http\Request;

class QualityApiController extends Controller
{
    public function index(Request $request)
    {
        $query = QualityInspection::with(['product', 'inspector']);

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        return response()->json($query->latest('inspection_date')->paginate(25));
    }
}
