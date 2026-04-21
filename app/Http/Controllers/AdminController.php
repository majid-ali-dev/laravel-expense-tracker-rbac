<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    // Super admin dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
