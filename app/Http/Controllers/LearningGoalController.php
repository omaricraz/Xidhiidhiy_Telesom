<?php

namespace App\Http\Controllers;

use App\Models\LearningGoal;
use App\Models\Team;
use App\Models\User;
use App\Models\UserLearningProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LearningGoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = LearningGoal::with(['team', 'userProgress']);

        // Apply scoped queries based on role
        if ($user->isManager()) {
            // Manager sees all learning goals
            $goals = $query->latest()->get();
        } elseif ($user->team_id) {
            // Team Lead, Employee, Intern see goals from their team
            $goals = $query->where('team_id', $user->team_id)->latest()->get();
        } else {
            $goals = collect();
        }

        // Get user progress for each goal
        $userProgress = UserLearningProgress::where('user_id', $user->id)
            ->pluck('is_completed', 'goal_id')
            ->toArray();

        return view('onboarding.index', compact('goals', 'userProgress'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', LearningGoal::class);
        $teams = Team::all();
        return view('onboarding.create', compact('teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', LearningGoal::class);

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_url' => 'nullable|url',
        ]);

        $goal = LearningGoal::create($validated);

        // Automatically create user_learning_progress entries for all team members
        $teamMembers = User::where('team_id', $goal->team_id)->get();
        foreach ($teamMembers as $member) {
            UserLearningProgress::create([
                'user_id' => $member->id,
                'goal_id' => $goal->id,
                'is_completed' => false,
            ]);
        }

        return redirect()->route('onboarding.index')->with('success', 'Learning goal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LearningGoal $learningGoal)
    {
        Gate::authorize('view', $learningGoal);
        $learningGoal->load(['team', 'userProgress.user']);
        return view('onboarding.show', compact('learningGoal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LearningGoal $learningGoal)
    {
        Gate::authorize('update', $learningGoal);
        $teams = Team::all();
        return view('onboarding.edit', compact('learningGoal', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LearningGoal $learningGoal)
    {
        Gate::authorize('update', $learningGoal);

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_url' => 'nullable|url',
        ]);

        $learningGoal->update($validated);

        return redirect()->route('onboarding.index')->with('success', 'Learning goal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LearningGoal $learningGoal)
    {
        Gate::authorize('delete', $learningGoal);
        $learningGoal->delete();

        return redirect()->route('onboarding.index')->with('success', 'Learning goal deleted successfully.');
    }

    /**
     * Mark a learning goal as completed for the current user.
     */
    public function markCompleted(Request $request, LearningGoal $learningGoal)
    {
        $user = Auth::user();
        
        $progress = UserLearningProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'goal_id' => $learningGoal->id,
            ],
            [
                'is_completed' => false,
            ]
        );

        $progress->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return redirect()->route('onboarding.index')->with('success', 'Learning goal marked as completed.');
    }
}

