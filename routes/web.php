<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningGoalController;
use App\Http\Controllers\NoticeboardController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhoIsWhoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Redirect guests to login page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Define a group of routes with 'auth' middleware applied
Route::middleware(['auth'])->group(function () {
    // Define a GET route for the home/dashboard
    Route::get('/home', function () {
        // Return a view named 'index' when accessing the home URL
        return view('index');
    })->name('home');

    // Admin routes (Manager only)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Lead routes
    Route::prefix('lead')->name('lead.')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    });

    // Tasks routes
    Route::resource('tasks', TaskController::class);

    // Onboarding routes
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/', [LearningGoalController::class, 'index'])->name('index');
        Route::get('create', [LearningGoalController::class, 'create'])->name('create');
        Route::post('/', [LearningGoalController::class, 'store'])->name('store');
        Route::get('{learningGoal}/edit', [LearningGoalController::class, 'edit'])->name('edit');
        Route::put('{learningGoal}', [LearningGoalController::class, 'update'])->name('update');
        Route::delete('{learningGoal}', [LearningGoalController::class, 'destroy'])->name('destroy');
        Route::post('{learningGoal}/mark-completed', [LearningGoalController::class, 'markCompleted'])->name('mark-completed');
    });

    // Who's Who route
    Route::get('whoswho', [WhoIsWhoController::class, 'index'])->name('whoswho.index');

    // Noticeboard route
    Route::get('noticeboard', [NoticeboardController::class, 'index'])->name('noticeboard.index');

    // Q&A route
    Route::get('qa', [QAController::class, 'index'])->name('qa.index');

    // Template pages route disabled - uncomment below to enable template demo pages
    // Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});