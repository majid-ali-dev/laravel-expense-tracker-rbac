<?php

namespace App\Http\Controllers;

class StaffController extends Controller
{
    // Show staff dashboard
    public function dashboard()
    {
        return view('staff.dashboard');
    }
}
