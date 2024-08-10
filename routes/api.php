<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/tasks', [TodoController::class, 'createTasks']);
Route::put('/tasks/{id}', [TodoController::class, 'updateTasks']);
Route::delete('/tasks/{id}', [TodoController::class, 'deleteTask']);
