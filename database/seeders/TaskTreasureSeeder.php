<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTreasureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 初期ユーザーデータの登録
        DB::table('app_users')->updateOrInsert(['id' => 'user1'], ['stones' => 10000, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('app_users')->updateOrInsert(['id' => 'user2'], ['stones' => 10, 'created_at' => now(), 'updated_at' => now()]);

        // 2. 新しい景品データ（個別の確率ではなく、レア度を指定する形式よ！）
        $items = [
            ['item_name' => 'コーヒー', 'rarity' => 'N'],
            ['item_name' => 'アイス', 'rarity' => 'N'],
            ['item_name' => 'チョコ', 'rarity' => 'N'],

            ['item_name' => 'コンビニフード', 'rarity' => 'R'],
            ['item_name' => 'スイーツ', 'rarity' => 'R'],

            ['item_name' => 'コメダ', 'rarity' => 'SR'],
            ['item_name' => 'ゲーム２時間', 'rarity' => 'SR'],

            ['item_name' => 'ゲーム６時間', 'rarity' => 'SSR'],
            ['item_name' => 'サイゼリヤ', 'rarity' => 'SSR'],

            ['item_name' => 'お小遣い３万円', 'rarity' => 'UR'],
        ];

        DB::table('gacha_items')->truncate();

        foreach ($items as $item) {
            DB::table('gacha_items')->insert([
                'item_name' => $item['item_name'],
                'rarity' => $item['rarity'], // レア度を保存する
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}