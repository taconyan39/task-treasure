<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // 1. ユーザー（宝石数管理）テーブル
    Schema::create('app_users', function (Blueprint $table) {
        $table->string('id')->primary(); // user1, user2 などのID
        $table->integer('stones')->default(0); // 所持宝石数
        $table->timestamps();
    });

    // 2. クエスト（タスク）テーブル
    Schema::create('tasks', function (Blueprint $table) {
        $table->id('task_id');
            $table->unsignedBigInteger('user_id'); 
            $table->string('task_name');
            $table->string('type');
            $table->integer('stone_reward');
            $table->string('status')->default('未完了');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. トレジャー（ご褒美ストック）テーブル
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reward_name');
            $table->integer('stock_count')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. 💡 抜け落ちていた「ガチャ景品」テーブルを追加！
        Schema::create('gacha_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->string('item_name');
            $table->string('description')->nullable(); // 💡 空っぽでもOKな設定（nullable）
            $table->string('rarity');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gacha_items');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('tasks');
    }
};