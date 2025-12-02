<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ComplexQueryController;
use App\Http\Controllers\FileManipulationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public ticket routes (accessible without authentication)
Route::prefix('tickets')->group(function () {
    Route::get('/create', [SupportTicketController::class, 'create'])->name('tickets.create');
    Route::post('/store', [SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('/anonymous/{ticketNumber}', [SupportTicketController::class, 'showAnonymous'])
        ->name('tickets.anonymous.show');
   
});

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Support Ticket routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('tickets.index');
        Route::post('/{ticket}/update-status', [SupportTicketController::class, 'updateStatus'])
            ->name('tickets.update-status');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
       
    });
    
    // Complex Query routes
    Route::prefix('complex-query')->group(function () {
        Route::get('/', [ComplexQueryController::class, 'index'])->name('complex-query.index');
        Route::post('/generate-data', [ComplexQueryController::class, 'generateData'])
            ->name('complex-query.generate-data');
        Route::get('/query1', [ComplexQueryController::class, 'query1'])->name('complex-query.query1');
        Route::get('/query2', [ComplexQueryController::class, 'query2'])->name('complex-query.query2');
        Route::get('/query3', [ComplexQueryController::class, 'query3'])->name('complex-query.query3');
        Route::get('/query4', [ComplexQueryController::class, 'query4'])->name('complex-query.query4');
    });
    
    // File Manipulation routes
    Route::prefix('file-manipulation')->group(function () {
        Route::get('/', [FileManipulationController::class, 'index'])->name('file-manipulation.index');
        Route::post('/process', [FileManipulationController::class, 'processFile'])
            ->name('file-manipulation.process');
    });
});

// Fallback route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
