<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the noticeboard page.
     */
    public function index()
    {
        // This is a placeholder for the noticeboard
        // In a real application, you would have a Notice model
        $notices = collect(); // Empty collection for now
        
        return view('noticeboard.index', compact('notices'));
    }
}

