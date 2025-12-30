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
        $allUsers = User::with('team')->latest()->get();

        // Separate users into current team and other teams
        $teamMembers = collect();
        $otherMembers = collect();

        if ($user->team_id) {
            // Get current user's team members
            $teamMembers = $allUsers->where('team_id', $user->team_id);
            // Get all other team members (different team or no team)
            $otherMembers = $allUsers->filter(function ($u) use ($user) {
                return $u->team_id != $user->team_id;
            });
        } else {
            // If user has no team, show all users as "other members"
            $otherMembers = $allUsers;
        }

        return view('whoswho.index', compact('teamMembers', 'otherMembers', 'user'));
    }
}

