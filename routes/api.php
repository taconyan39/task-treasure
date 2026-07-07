<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskTreasureController;

// タスクトレジャー用のAPIルート一覧よ！
Route::get('/load', [TaskTreasureController::class, 'load']);
Route::post('/complete', [TaskTreasureController::class, 'complete']);
Route::post('/gacha', [TaskTreasureController::class, 'gacha']);
Route::post('/consume', [TaskTreasureController::class, 'consume']);
Route::post('/add-task', [TaskTreasureController::class, 'addTask']);
Route::post('/reset-daily', [TaskTreasureController::class, 'resetDaily']);

// 💡ここが今回の新しいご褒美登録用の道しるべよ！スペルが完全に一致しているか確認してね
Route::post('/add-gacha-item', [TaskTreasureController::class, 'addGachaItem']);
Route::post('/delete-gacha-item', [TaskTreasureController::class, 'deleteGachaItem']);