<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the questions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Question::with(['creator', 'team']);
        $selectedTeam = $request->get('team');

        // Apply scoped queries based on role
        if ($user->isManager()) {
            // Manager sees all questions, can filter by team
            if ($selectedTeam) {
                if ($selectedTeam === 'global') {
                    $query->whereNull('team_id');
                } else {
                    $query->where('team_id', $selectedTeam);
                }
            }
            $questions = $query->latest()->get();
            $allTeams = Team::all();
        } elseif ($user->isTeamLead() && $user->team_id) {
            // Team Lead sees questions from their team or questions without a team (global)
            $questions = $query->where(function ($q) use ($user) {
                $q->where('team_id', $user->team_id)
                  ->orWhereNull('team_id');
            })->latest()->get();
            $allTeams = collect([$user->team]);
        } else {
            // Other users see questions from their team or global questions
            if ($user->team_id) {
                $questions = $query->where(function ($q) use ($user) {
                    $q->where('team_id', $user->team_id)
                      ->orWhereNull('team_id');
                })->latest()->get();
            } else {
                $questions = $query->whereNull('team_id')->latest()->get();
            }
            $allTeams = $user->team ? collect([$user->team]) : collect();
        }
        
        // Group questions by team for better display
        $questionsByTeam = $questions->groupBy(function ($question) {
            return $question->team_id && $question->team ? $question->team->name : 'Global';
        });
        
        return view('qa.index', compact('questions', 'questionsByTeam', 'allTeams', 'selectedTeam'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        Gate::authorize('create', Question::class);
        $user = Auth::user();
        
        // Managers can select any team, Team Leads can only select their team
        if ($user->isManager()) {
            $teams = Team::all();
        } else {
            // Team Lead can only create questions for their team
            $teams = $user->team_id ? Team::where('id', $user->team_id)->get() : collect();
        }
        
        return view('qa.create', compact('teams'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Question::class);

        $user = Auth::user();
        
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        // Restrict Team Leads to only their team
        if ($user->isTeamLead() && !$user->isManager()) {
            $validated['team_id'] = $user->team_id;
        }

        $validated['created_by'] = $user->id;

        Question::create($validated);

        return redirect()->route('qa.index')->with('success', 'Question created successfully.');
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question)
    {
        Gate::authorize('view', $question);
        $question->load(['creator', 'team']);
        return view('qa.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question)
    {
        Gate::authorize('update', $question);
        $user = Auth::user();
        
        // Managers can select any team, Team Leads can only select their team
        if ($user->isManager()) {
            $teams = Team::all();
        } else {
            // Team Lead can only edit questions for their team
            $teams = $user->team_id ? Team::where('id', $user->team_id)->get() : collect();
        }
        
        return view('qa.edit', compact('question', 'teams'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        Gate::authorize('update', $question);

        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $question->update($validated);

        return redirect()->route('qa.index')->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        Gate::authorize('delete', $question);
        $question->delete();

        return redirect()->route('qa.index')->with('success', 'Question deleted successfully.');
    }
}

