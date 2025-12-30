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
    public function index()
    {
        $user = Auth::user();
        $query = Question::with(['creator', 'team']);

        // Apply scoped queries based on role
        if ($user->isManager()) {
            // Manager sees all questions
            $questions = $query->latest()->get();
        } elseif ($user->isTeamLead() && $user->team_id) {
            // Team Lead sees questions from their team or questions without a team (global)
            $questions = $query->where(function ($q) use ($user) {
                $q->where('team_id', $user->team_id)
                  ->orWhereNull('team_id');
            })->latest()->get();
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
        }
        
        return view('qa.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        Gate::authorize('create', Question::class);
        $teams = Team::all();
        return view('qa.create', compact('teams'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Question::class);

        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $validated['created_by'] = Auth::id();

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
        $teams = Team::all();
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

