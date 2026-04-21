<?php

namespace App\Http\Controllers;

class MemberController extends Controller
{
    // Show member dashboard
    public function dashboard()
    {
        return view('member.dashboard');
    }
}
