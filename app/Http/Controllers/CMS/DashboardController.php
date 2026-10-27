<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $project = function_exists('current_project') ? current_project() : null;
        $data = (new AdminDashboardController)->getInbetweenV2DashboardData($project);

        return view('cms.dashboard', $data);
    }
}
