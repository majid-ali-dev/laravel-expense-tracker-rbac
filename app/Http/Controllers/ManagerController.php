<?php

namespace App\Http\Controllers;

class ManagerController extends Controller
{
    // Show manager dashboard
    public function dashboard()
    {
        return view('manager.dashboard');
    }
}
