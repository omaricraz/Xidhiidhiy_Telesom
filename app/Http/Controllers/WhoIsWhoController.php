<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhoIsWhoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the Who's Who page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all users with their teams
        $query = User::with('team');

        // Apply scoped queries based on role
        if ($user->isManager()) {
            // Manager sees all users
            $users = $query->latest()->get();
        } elseif ($user->isTeamLead() && $user->team_id) {
            // Team Lead sees users from their team
            $users = $query->where('team_id', $user->team_id)->latest()->get();
        } else {
            // Employee and Intern see users from their team
            $users = $query->where('team_id', $user->team_id)->latest()->get();
        }

        return view('whoswho.index', compact('users'));
    }
}

