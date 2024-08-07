<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('todo', TodoController::class);
Route::post('/save-task', [TodoController::class, 'create']);
Route::get('/get-tasks', [TodoController::class, 'geTasks']);
Route::post('/delete-task', [TodoController::class, 'destroy']);
Route::post('/complete-task', [TodoController::class, 'completeTask']);
Route::post('/show-alltask', [TodoController::class, 'showAlltask']);

