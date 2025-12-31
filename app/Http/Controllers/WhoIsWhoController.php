<?php

namespace App\Http\Controllers;

use App\Models\Team;
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
        
        // For managers, show teams divided into Dev and DevOps
        if ($user->isManager()) {
            // Get all teams with their members (excluding the manager)
            $teams = Team::with(['members' => function($query) use ($user) {
                $query->where('id', '!=', $user->id);
            }])->get();
            
            // Group users by team
            $teamsWithMembers = [];
            foreach ($teams as $team) {
                $teamsWithMembers[$team->name] = $team->members;
            }
            
            // Also get users without teams (excluding the manager)
            $usersWithoutTeam = User::whereNull('team_id')
                ->where('id', '!=', $user->id)
                ->get();
            
            return view('whoswho.index', [
                'teamsWithMembers' => $teamsWithMembers,
                'usersWithoutTeam' => $usersWithoutTeam,
                'user' => $user,
                'isManager' => true
            ]);
        }
        
        // For non-managers, use the original logic
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

        return view('whoswho.index', [
            'teamMembers' => $teamMembers,
            'otherMembers' => $otherMembers,
            'user' => $user,
            'isManager' => false
        ]);
    }
}

