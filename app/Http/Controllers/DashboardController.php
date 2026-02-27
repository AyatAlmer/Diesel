<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function summary()
    {
        $data = $this->dashboardService->getSummary();

        return response()->json([
            'message' => 'Financial summary retrieved successfully',
            'data'    => $data
        ]);
    }
}
