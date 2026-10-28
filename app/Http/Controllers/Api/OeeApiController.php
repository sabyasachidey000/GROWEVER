<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OeeService;
use Illuminate\Http\Request;

class OeeApiController extends Controller
{
    public function __construct(
        protected OeeService $oeeService
    ) {
    }

    public function summary(Request $request)
    {
        $summary = $this->oeeService->getOeeComparison(
            $request->get('start_date', now()->subDays(30)),
            $request->get('end_date', now())
        );

        return response()->json($summary);
    }

    public function ranking(Request $request)
    {
        $date = $request->get('date', today());
        $ranking = $this->oeeService->getMachineOeeRanking($date);

        return response()->json($ranking);
    }
}
