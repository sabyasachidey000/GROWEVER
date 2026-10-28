<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;

class ShiftApiController extends Controller
{
    public function getCurrentShift()
    {
        $currentShift = Shift::getCurrentShift();

        return response()->json($currentShift ? [
            'found' => true,
            'shift' => $currentShift,
        ] : ['found' => false]);
    }
}
