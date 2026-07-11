<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// ログインしている人だけがトップページ（RPG画面）を見られるようにする「見張り役（auth）」を設定するの
Route::middleware('auth')->group(function () {
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

// routes/web.php にこれを追加してちょうだい！
Route::get('/task-edit', function () {
    return view('task_edit');
});

// ✏️ タスクを編集する処理（URLに /api/ をつける！）
Route::post('/api/edit-task', function (Request $request) {
    try {
        DB::table('tasks')
            ->where('task_id', $request->input('task_id')) // IDの列名が 'id' なら書き換えて！
            ->update([
                'task_name' => $request->input('task_name'),
                'stone_reward' => $request->input('stone_reward'),
                'type' => $request->input('type')
            ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

// 🗑️ タスクを削除する処理（URLに /api/ をつける！）
Route::post('/api/delete-task', function (Request $request) {
    try {
        DB::table('tasks')
            ->where('task_id', $request->input('task_id')) // IDの列名が 'id' なら書き換えて！
            ->delete();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}); // ← 👑 ここ！この「});」が足りていなかったの！