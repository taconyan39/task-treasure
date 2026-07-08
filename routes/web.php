<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Artisan;

group(function () {
    Route::get('/', function () {
        return view('task_treasure'); // あなたの素晴らしいRPG画面ね！
    });
});

// --- ここから下は誰でもアクセスできる場所よ ---

// 新規登録の画面と処理
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログインの画面と処理
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ログアウトの処理
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// マイグレーション用
Route::get('/migrate-db', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'オーッホッホッホ♪ マイグレーション大成功なの！';
});