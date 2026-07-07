<?php

use Illuminate\Support\Facades\Route;

// トップページ（http://127.0.0.1:8000）にアクセスした時に、あのRPG風画面を表示させるわ！
Route::get('/', function () {
    return view('task_treasure');
});

use App\Http\Controllers\AuthController;

// 新規登録の画面と処理
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログインの画面と処理
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ログアウトの処理
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');