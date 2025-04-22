<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tasks', \App\Http\Controllers\Api\TaskController::class)
    ->where(['task' => '[0-9]+']);

Route::get('tasks/priority', [\App\Http\Controllers\Api\TaskController::class, 'priority'])
    ->name('tasks.priority');
