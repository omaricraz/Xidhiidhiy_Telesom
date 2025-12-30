<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
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
        $query = Task::with(['creator', 'assignee']);

        // Apply scoped queries based on role
        if ($user->isManager()) {
            // Manager sees all tasks
            $tasks = $query->latest()->paginate(15);
        } elseif ($user->isTeamLead() && $user->team_id) {
            // Team Lead sees tasks from their team
            $teamMemberIds = User::where('team_id', $user->team_id)->pluck('id');
            $tasks = $query->where(function($q) use ($teamMemberIds, $user) {
                $q->whereIn('assignee_id', $teamMemberIds)
                  ->orWhereIn('creator_id', $teamMemberIds)
                  ->orWhere('creator_id', $user->id);
            })->latest()->paginate(15);
        } elseif ($user->isIntern()) {
            // Intern sees only their own tasks
            $tasks = $query->where(function($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhere('creator_id', $user->id);
            })->latest()->paginate(15);
        } else {
            // Employee sees their own tasks
            $tasks = $query->where(function($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhere('creator_id', $user->id);
            })->latest()->paginate(15);
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Normal',
            'status' => 'required|in:Pending,In_Progress,Completed',
            'is_private' => 'boolean',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $validated['creator_id'] = Auth::id();
        $validated['is_private'] = $request->has('is_private');

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);
        $task->load(['creator', 'assignee']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        $users = User::where('id', '!=', Auth::id())->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Normal',
            'status' => 'required|in:Pending,In_Progress,Completed',
            'is_private' => 'boolean',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        // Interns can only update status
        if (Auth::user()->isIntern()) {
            $validated = ['status' => $validated['status']];
        } else {
            $validated['is_private'] = $request->has('is_private');
        }

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}

