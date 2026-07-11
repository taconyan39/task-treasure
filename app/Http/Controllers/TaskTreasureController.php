<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskTreasureController extends Controller
{
    // 1. データの読み込み処理（load）
public function load(Request $request)
    {
        try {
            // 送られてきたユーザーIDを受け取る
            $userId = request('user'); 

            // フライングで 'null' が来ても、ここで優しく空っぽのデータを返してエラーを防ぐの！
            if ($userId === 'null' || empty($userId)) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
}

// --- これより下はあなたが元々書いていたデータベース検索処理 ---

            // ほうせき（所持数）の取得
            $user = DB::table('users')->where('id', $userId)->first();
            // ... (これより下のコードは変更しなくてOKよ！)
            $stones = $user && isset($user->stones) ? $user->stones : 0;

            // クエスト一覧の取得
            $tasks = DB::table('tasks')->where('user_id', $userId)->get();

            // 手に入れたトレジャーの取得
            $rewards = DB::table('rewards')->where('user_id', $userId)->get();

            // ガチャ景品一覧の取得
            $gachaItems = DB::table('gacha_items')->where('user_id', $userId)->get();

            return response()->json([
                'stones' => $stones,
                'tasks' => $tasks,
                'rewards' => $rewards,
                'gachaItems' => $gachaItems
            ]);
        } catch (\Throwable $e) {
            // 💡 パニックにならずに、エラーの正体をJSONで返すプロのテクニックさ！
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 2. クエストクリア処理
    // クエストをクリアする処理
    public function complete(Request $request)
    {
        try {
            // 画面から送られてきたユーザーIDとタスクIDを受け取ります
            $userId = $request->input('user_id');
            $taskId = $request->input('task_id');

            // 1. クリア対象のクエスト情報をデータベースから探して、$task という箱に入れます
            $task = DB::table('tasks')
                ->where('task_id', $taskId)
                ->where('user_id', $userId)
                ->first();

            // 万が一クエストが見つからなかった場合の安全装置です
            if (!$task) {
                return response()->json([
                    'success' => false, 
                    'message' => 'クエストが見つかりませんでした。'
                ]);
            }

            // 2. 「毎日」と「単発」だけ完了にする
            if ($task->type !== '連続') {
                DB::table('tasks')
                    ->where('task_id', $taskId)
                    ->update([
                        'status' => '完了',
                        'updated_at' => now()
                    ]);
            }
            // 3. ユーザーの「ほうせき（stones）」を、クエストの報酬分だけ増やします
            // 💡 先ほど追加した (int) もバッチリ入っているの！
            DB::table('users')
                ->where('id', $userId)
                ->increment('stones', (int) $task->stone_reward);

            // 全て成功したことをフロントエンドに伝えます
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('クリア処理エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 3. ガチャを引く処理
   // ガチャ（宝箱）を引く処理
    public function gacha(Request $request)
    {
        try {
            $userId = $request->input('user_id');

            // 1. ユーザーの現在のほうせき数を確認するの
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user || $user->stones < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'ほうせきが 足りないみたい！'
                ]);
            }

            // 2. 宝箱の中身（ご褒美一覧）を取得するの
            $gachaItems = DB::table('gacha_items')->where('user_id', $userId)->get();
            if ($gachaItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => '宝箱の中身が 空っぽなの。デバッグメニューから ご褒美を登録してちょうだい！'
                ]);
            }

            // 3. ランダムに1つご褒美を選ぶのよ（これがガチャの魔法ね！）
            $randomItem = $gachaItems->random();

            // 4. ほうせきを5個減らすの（decrementは引き算をしてくれる便利な機能よ）
            DB::table('users')->where('id', $userId)->decrement('stones', 10);

            // 5. 当たったご褒美を「トレジャー（rewards）」に追加、もしくは持っていれば数を増やすの
            $existingReward = DB::table('rewards')
                ->where('user_id', $userId)
                ->where('reward_name', $randomItem->item_name)
                ->first();

            if ($existingReward) {
                // すでに同じご褒美を持っていたら、個数（stock_count）を1つ増やすの
                DB::table('rewards')
                    ->where('user_id', $userId)
                    ->where('reward_name', $randomItem->item_name)
                    ->increment('stock_count', 1);
            } else {
                // 初めてのご褒美なら、新しくリストに登録するのよ
                DB::table('rewards')->insert([
                    'user_id' => $userId,
                    'reward_name' => $randomItem->item_name,
                    'stock_count' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 6. 何が当たったか、画面に教えるの
            return response()->json([
                'success' => true,
                'reward' => $randomItem->item_name
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ガチャ処理エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

// ４手に入れたご褒美（トレジャー）を使う処理
    public function consume(Request $request)
    {
        try {
            // 画面から送られてきたデータを受け取るの
            $userId = $request->input('user_id');
            $rewardName = $request->input('rewardName');

            // 1. データベースから、使おうとしているご褒美を探すの
            $reward = DB::table('rewards')
                ->where('user_id', $userId)
                ->where('reward_name', $rewardName)
                ->first();

            // 安全装置：もしご褒美が見つからなかったらエラーを返すの
            if (!$reward) {
                return response()->json([
                    'success' => false,
                    'message' => 'ご褒美が見つからないの。'
                ]);
            }

            // 2. 残りの個数によって処理を変えるのよ
            if ($reward->stock_count > 1) {
                // 2個以上持っているなら、1つ減らす（decrement）の
                DB::table('rewards')
                    ->where('user_id', $userId)
                    ->where('reward_name', $rewardName)
                    ->decrement('stock_count', 1);
            } else {
                // ラスト1個なら、リストから完全に削除（delete）するのよ
                DB::table('rewards')
                    ->where('user_id', $userId)
                    ->where('reward_name', $rewardName)
                    ->delete();
            }

            // 成功したことを画面に伝えるの
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ご褒美消費エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 5. 新しいクエストを登録する処理
    public function addTask(Request $request)
    {
        try {
            // ① どんなデータがLaravelに届いたかログに記録するの
            // ※ \Illuminate\Support\Facades\Log を使って直接ログを呼び出すのよ
            \Illuminate\Support\Facades\Log::info('フロントから届いたデータ:', $request->all());

            // ② データベースに登録するデータをまとめるのよ
            $insertData = [
                'user_id' => $request->input('user_id'),
                'task_name' => $request->input('task_name'),
                'stone_reward' => $request->input('stone_reward'),
                'type' => $request->input('type'),
                'status' => '未完了',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ③ データベースに挿入するの (DBは元のコード通り使うのよ)
            DB::table('tasks')->insert($insertData);

            // ④ 成功したら、登録したデータも一緒にフロントエンドに返すのよ
            return response()->json([
                'success' => true,
                'inserted_data' => $insertData // 追加：何を保存したか確認するためね
            ]);

        } catch (\Throwable $e) {
            // エラーが起きたらログに記録するの
            \Illuminate\Support\Facades\Log::error('クエスト登録エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // 6. 毎日のクエストをリセットする処理
    public function resetDaily(Request $request)
    {
        try {
            // 画面から送られてきたユーザーIDを受け取るの
            $userId = $request->input('user_id');

            // 「毎日」タイプのクエストだけを探して、「未完了」に戻すのよ！
            DB::table('tasks')
                ->where('user_id', $userId)
                ->where('type', '毎日')
                ->update([
                    'status' => '未完了',
                    'updated_at' => now()
                ]);

            // 成功したことを画面に伝えるの
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            // エラーが起きたらログに記録して、画面にもエラーを伝えるのよ
            \Illuminate\Support\Facades\Log::error('リセット処理エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // 7. 新しいご褒美（ガチャ景品）を登録する処理
    // 新しいご褒美（ガチャ景品）を登録する処理
    public function addGachaItem(Request $request)
    {
        try {
            // 画面から送られてきたデータを受け取るの
            $userId = $request->input('user_id');
            $itemName = $request->input('item_name');
            $description = $request->input('description');
            $rarity = $request->input('rarity');

            // データベースの gacha_items テーブルに新しい景品を保存するのよ
            \DB::table('gacha_items')->insert([
                'user_id' => $userId,
                'item_name' => $itemName,
                'description' => $description,
                'rarity' => $rarity,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 成功したことを画面に伝えるの
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ご褒美登録エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 8. ご褒美（ガチャ景品）を削除する処理
    public function deleteGachaItem(Request $request)
    {
        try {
            // 画面から送られてきた「消したい景品のID」を受け取るの
            $id = $request->input('id');

            // データベースからそのIDの景品を探して、削除（delete）するのよ！
            DB::table('gacha_items')->where('id', $id)->delete();

            // 成功したことを画面に伝えるの
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ご褒美削除エラー:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}