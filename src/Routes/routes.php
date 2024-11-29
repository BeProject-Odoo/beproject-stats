<?php

use Beproject\Monitor\Controllers\BeprojectStatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('beproject')->group(function () {
    Route::get('ping', [BeprojectStatsController::class, 'ping']);
    Route::get('system-health', [BeprojectStatsController::class, 'checkSystemHealth']);
});