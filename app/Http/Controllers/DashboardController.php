<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    // Sends the user to the right dashboard after login
    public function index()
    {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'gym_owner' => redirect()->route('coach.dashboard'),
            default => redirect()->route('member.dashboard'),
        };
    }

   public function admin()  { return view('admin_dash'); }
public function coach()  { return view('gym_ownerdash'); }
public function member() { return view('members_dash'); }
}