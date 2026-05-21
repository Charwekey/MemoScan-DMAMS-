<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Session operations
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Memo routes - accessible by all roles (Admin, Staff, Viewer) for viewing/downloading
    Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
    Route::get('/memos/{memo}/show', [MemoController::class, 'show'])->name('memos.show');
    Route::get('/memos/{memo}/download', [MemoController::class, 'download'])->name('memos.download');
    Route::get('/memos/{memo}/preview', [MemoController::class, 'preview'])->name('memos.preview');

    // Memo routes - creation & editing (Admin and Staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/memos/create', [MemoController::class, 'create'])->name('memos.create');
        Route::post('/memos', [MemoController::class, 'store'])->name('memos.store');
        Route::get('/memos/{memo}/edit', [MemoController::class, 'edit'])->name('memos.edit');
        Route::put('/memos/{memo}', [MemoController::class, 'update'])->name('memos.update');
    });

    // Memo routes - deletion & recovery (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::delete('/memos/{memo}', [MemoController::class, 'destroy'])->name('memos.destroy');
        Route::post('/memos/{id}/restore', [MemoController::class, 'restore'])->name('memos.restore');

        // Admin-only User management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Admin-only Audit Logs
        Route::get('/logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
    });
});
