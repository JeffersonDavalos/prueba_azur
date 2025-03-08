<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/students',function(){
    return 'holi';
});


Route::get('/tasks', [TaskController::class, 'getTasks']);
Route::get('/estado', [TaskController::class, 'estados']);
Route::post('/tareas', [TaskController::class, 'agregarTarea']);




