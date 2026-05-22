<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductivityController;
use App\Http\Controllers\AiSimulationController;

Route::redirect('/', '/dashboard');
Route::get('/dashboard', [ProductivityController::class, 'index']);
Route::get('/dashboard/check-api-key', [AiSimulationController::class, 'checkApiKey']);
Route::post('/dashboard/simulate', [AiSimulationController::class, 'simulate']);
