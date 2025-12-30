<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NoticeboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the notices.
     */
    public function index()
    {
        $notices = Notice::with('creator')->latest()->paginate(15);
        
        return view('noticeboard.index', compact('notices'));
    }

    /**
     * Show the form for creating a new notice.
     */
    public function create()
    {
        Gate::authorize('create', Notice::class);
        return view('noticeboard.create');
    }

    /**
     * Store a newly created notice in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Notice::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['created_by'] = Auth::id();

        Notice::create($validated);

        return redirect()->route('noticeboard.index')->with('success', 'Notice created successfully.');
    }

    /**
     * Show the form for editing the specified notice.
     */
    public function edit(Notice $notice)
    {
        Gate::authorize('update', $notice);
        return view('noticeboard.edit', compact('notice'));
    }

    /**
     * Update the specified notice in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        Gate::authorize('update', $notice);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $notice->update($validated);

        return redirect()->route('noticeboard.index')->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified notice from storage.
     */
    public function destroy(Notice $notice)
    {
        Gate::authorize('delete', $notice);
        $notice->delete();

        return redirect()->route('noticeboard.index')->with('success', 'Notice deleted successfully.');
    }
}

