<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
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

Route::redirect('/', '/admin');

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/log', [LogController::class, 'index'])->name('log.index');


use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


use App\Models\User;

Route::get('/newuser', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);


    return $user;
});
