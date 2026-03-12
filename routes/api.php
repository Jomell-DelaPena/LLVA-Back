<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimeSessionController;
use App\Http\Controllers\IdleEventController;
use App\Http\Controllers\PresenceLogController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\NavItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminTimeSessionController;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('incident-types', IncidentTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('statuses', StatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Navigation structure
    Route::apiResource('nav-items', NavItemController::class)->only(['index', 'store', 'update', 'destroy']);

    // Access Control
    Route::apiResource('modules', ModuleController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('accesses', AccessController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/users/{user}/accesses',  [UserAccessController::class, 'index']);
    Route::put('/users/{user}/accesses',  [UserAccessController::class, 'sync']);

    // Time Tracker — order matters: /active before /{timeSession}
    Route::get('/time-sessions/active',           [TimeSessionController::class, 'active']);
    Route::post('/time-sessions',                 [TimeSessionController::class, 'store']);
    Route::patch('/time-sessions/{timeSession}',  [TimeSessionController::class, 'update']);

    Route::post('/idle-events',                [IdleEventController::class,  'store']);
    Route::patch('/idle-events/{idleEvent}',   [IdleEventController::class,  'update']);
    Route::get('/presence-logs',  [PresenceLogController::class, 'index']);
    Route::post('/presence-logs', [PresenceLogController::class, 'store']);

    // Admin — Time Tracker overview (order matters: /stats, /export before /{timeSession})
    Route::get('/admin/time-sessions/stats',          [AdminTimeSessionController::class, 'stats']);
    Route::get('/admin/time-sessions/export',         [AdminTimeSessionController::class, 'export']);
    Route::get('/admin/time-sessions',                [AdminTimeSessionController::class, 'index']);
    Route::get('/admin/time-sessions/{timeSession}',  [AdminTimeSessionController::class, 'show']);

    // Notifications — order matters: /read-all before /{id}/read
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all',   [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{id}/read',  [NotificationController::class, 'markRead']);
});
