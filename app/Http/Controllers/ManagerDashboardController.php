<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the manager dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $manager = Auth::user();
        
        // Get all employees excluding the manager
        $totalEmployees = User::where('id', '!=', $manager->id)->count();
        
        // Get interns count (excluding manager)
        $internsCount = User::where('role', 'Intern')
            ->where('id', '!=', $manager->id)
            ->count();
        
        // Get permanent employees count (Employee and Team_Lead roles, excluding manager)
        $permanentCount = User::whereIn('role', ['Employee', 'Team_Lead'])
            ->where('id', '!=', $manager->id)
            ->count();
        
        // Calculate percentages
        $totalForPercentage = $internsCount + $permanentCount;
        $internPercentage = $totalForPercentage > 0 ? round(($internsCount / $totalForPercentage) * 100) : 0;
        $permanentPercentage = $totalForPercentage > 0 ? round(($permanentCount / $totalForPercentage) * 100) : 0;
        
        // Tasks statistics
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $inProgressTasks = Task::where('status', 'In_Progress')->count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $highPriorityTasks = Task::where('priority', 'High')->where('status', '!=', 'Completed')->count();
        $taskCompletionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        // Teams statistics
        $totalTeams = Team::count();
        $teamLeadsCount = User::where('role', 'Team_Lead')
            ->where('id', '!=', $manager->id)
            ->count();
        
        // Active employees (assuming status field exists)
        $activeEmployees = User::where('id', '!=', $manager->id)
            ->where(function($query) {
                $query->where('status', 'active')
                    ->orWhereNull('status');
            })
            ->count();
        
        // Questions statistics
        $totalQuestions = Question::count();
        $recentQuestions = Question::with(['creator', 'team'])->latest()->take(5)->get();
        
        // Recent tasks
        $recentTasks = Task::with(['creator', 'assignee'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('manager.dashboard', [
            'totalEmployees' => $totalEmployees,
            'internsCount' => $internsCount,
            'permanentCount' => $permanentCount,
            'internPercentage' => $internPercentage,
            'permanentPercentage' => $permanentPercentage,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'inProgressTasks' => $inProgressTasks,
            'pendingTasks' => $pendingTasks,
            'highPriorityTasks' => $highPriorityTasks,
            'taskCompletionPercentage' => $taskCompletionPercentage,
            'totalTeams' => $totalTeams,
            'teamLeadsCount' => $teamLeadsCount,
            'activeEmployees' => $activeEmployees,
            'totalQuestions' => $totalQuestions,
            'recentQuestions' => $recentQuestions,
            'recentTasks' => $recentTasks,
        ]);
    }
}


