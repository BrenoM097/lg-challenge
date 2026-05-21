<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductivityController;


Route::get('/dashboard', [ProductivityController::class, 'index']);
Route::post('/dashboard/simulate', 'AiSimulationController@simulate');
