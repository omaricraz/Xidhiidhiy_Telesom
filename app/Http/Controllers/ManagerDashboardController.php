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
        
        // Calculate productivity level based on completion rate
        $productivityLevel = $this->calculateProductivityLevel($taskCompletionPercentage);
        
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
        // Order: Completed tasks at bottom, then by priority (High > Medium > Normal), then by most recent
        $recentTasks = Task::with(['creator', 'assignee'])
            ->orderByRaw("CASE 
                WHEN status = 'Completed' THEN 2 
                ELSE 1 
            END")
            ->orderByRaw("CASE 
                WHEN priority = 'High' THEN 1 
                WHEN priority = 'Medium' THEN 2 
                WHEN priority = 'Normal' THEN 3 
                ELSE 4 
            END")
            ->orderBy('created_at', 'desc')
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
            'productivityLevel' => $productivityLevel,
            'totalTeams' => $totalTeams,
            'teamLeadsCount' => $teamLeadsCount,
            'activeEmployees' => $activeEmployees,
            'totalQuestions' => $totalQuestions,
            'recentQuestions' => $recentQuestions,
            'recentTasks' => $recentTasks,
        ]);
    }
    
    /**
     * Calculate productivity level based on completion percentage.
     *
     * @param int $completionPercentage
     * @return array
     */
    private function calculateProductivityLevel($completionPercentage)
    {
        if ($completionPercentage >= 90) {
            return [
                'label' => 'Excellent',
                'color' => 'success',
                'icon' => 'ti-trophy',
                'percentage' => $completionPercentage
            ];
        } elseif ($completionPercentage >= 75) {
            return [
                'label' => 'Very Good',
                'color' => 'info',
                'icon' => 'ti-star',
                'percentage' => $completionPercentage
            ];
        } elseif ($completionPercentage >= 60) {
            return [
                'label' => 'Good',
                'color' => 'primary',
                'icon' => 'ti-thumb-up',
                'percentage' => $completionPercentage
            ];
        } elseif ($completionPercentage >= 40) {
            return [
                'label' => 'Average',
                'color' => 'warning',
                'icon' => 'ti-alert-circle',
                'percentage' => $completionPercentage
            ];
        } else {
            return [
                'label' => 'Needs Improvement',
                'color' => 'danger',
                'icon' => 'ti-alert-triangle',
                'percentage' => $completionPercentage
            ];
        }
    }
}


