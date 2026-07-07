<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Artisan;

// トップページ（http://127.0.0.1:8000）にアクセスした時に、あのRPG風画面を表示させる
Route::get('/', function () {
    return view('task_treasure');
});

// 新規登録の画面と処理
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログインの画面と処理
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ログアウトの処理（ぐちゃぐちゃになっていた部分を修正！）
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// マイグレーションを実行するための特別なURL
Route::get('/migrate-db', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'オーッホッホッホ♪ マイグレーション大成功なの！';
}); // ← このカッコでしっかり閉じるの！