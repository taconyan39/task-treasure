<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Laravelのセキュリティ対策に必要な呪文（トークン）よ -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>タスクトレジャー - タストレ -</title>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet">
    <style>
        :root {
            --rpg-black: #000000;
            --rpg-window: rgba(10, 10, 15, 0.95);
            --rpg-border: #ffffff;
            --rpg-text: #ffffff;
            --rpg-gold: #ffcc00;
            --rpg-red: #ff3333;
            --rpg-gray: #aaaaaa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'DotGothic16', monospace;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #2c2c2c;
            background-image: 
                radial-gradient(#1a1a1a 25%, transparent 25%),
                radial-gradient(#1a1a1a 25%, transparent 25%);
            background-size: 16px 16px;
            background-position: 0 0, 8px 8px;
            color: var(--rpg-text);
            padding: 12px;
            padding-bottom: 40px;
            display: flex;
            justify-content: center;
            letter-spacing: 1px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: var(--rpg-window);
            border: 4px double var(--rpg-border);
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.9);
            backdrop-filter: blur(4px);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 4px dashed var(--rpg-border);
        }

        h1 {
            font-size: 28px;
            color: var(--rpg-text);
        }

        .user-selector {
            display: none;
        }

        .status-board {
            border: 2px solid var(--rpg-border);
            border-radius: 4px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: rgba(0,0,0,0.6);
        }

        .stone-count {
            font-size: 24px;
            color: var(--rpg-gold);
        }

        .gacha-section {
            border: 2px solid var(--rpg-border);
            border-radius: 4px;
            padding: 16px;
            text-align: center;
            margin-bottom: 20px;
            background-color: rgba(0,0,0,0.6);
        }

        .chest-icon {
            font-size: 72px;
            margin: 8px 0;
            display: inline-block;
            cursor: pointer;
            transition: transform 0.1s;
            filter: drop-shadow(0 0 10px rgba(255, 204, 0, 0.2));
        }
        .chest-icon:active {
            transform: scale(0.95) translateY(2px);
        }

        .btn {
            background-color: var(--rpg-black);
            color: var(--rpg-text);
            border: 2px solid var(--rpg-border);
            border-radius: 4px;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            display: block;
            text-align: center;
        }
        .btn:active {
            background-color: var(--rpg-text);
            color: var(--rpg-black);
        }

        .btn-success {
            border-color: var(--rpg-border);
            color: var(--rpg-text);
            padding: 8px 16px;
            font-size: 14px;
            width: auto;
            display: inline-block;
        }
        .btn-success:active {
            background-color: var(--rpg-text);
            color: var(--rpg-black);
        }

        h2 {
            font-size: 18px;
            margin-bottom: 12px;
            color: var(--rpg-gold);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        h2::before {
            content: "▶";
            font-size: 14px;
            color: var(--rpg-gold);
        }

        .list-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
            /* ここから下がキングが追加したスクロールの魔法よ！ */
            max-height: 300px; /* 好きな高さに調整していいの。300pxくらいが見やすいかしら */
            overflow-y: auto; /* 縦にはみ出したらスクロールバーを出す設定ね */
            padding-right: 8px; /* スクロールバーが文字に被らないように少し隙間を空けるの */
        }

        .list-item {
            border: 1px solid var(--rpg-border);
            border-radius: 4px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0,0,0,0.6);
        }

        .task-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-width: 65%;
        }

        .task-name {
            font-size: 16px;
            color: var(--rpg-text);
            word-break: break-all;
        }

        .task-meta {
            font-size: 12px;
            color: var(--rpg-gray);
        }

        .status-badge {
            color: var(--rpg-gray);
            font-size: 14px;
            padding: 8px 16px;
            border: 1px dashed var(--rpg-gray);
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 14px;
        }
        label {
            display: block;
            font-size: 13px;
            color: var(--rpg-gray);
            margin-bottom: 4px;
        }
        input, select {
            width: 100%;
            background-color: var(--rpg-black);
            border: 2px solid var(--rpg-border);
            color: var(--rpg-text);
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus {
            border-color: var(--rpg-gold);
        }

        .admin-section {
            margin-top: 32px;
            border-top: 4px dashed var(--rpg-border);
            padding-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- 💡 ログインユーザー情報とログアウトボタン -->
        <div class="header-bar">
            <span style="font-weight: bold; font-size: 18px;">🏆 タスクトレジャー</span>
            <form method="POST" action="/logout" style="margin: 0; display: flex; align-items: center; gap: 10px;">
                @csrf
                <span>ようこそ、<strong>{{ auth()->user()->name ?? '冒険者' }}</strong>！</span>
                <button type="submit" class="btn" style="padding: 4px 10px; font-size: 12px;">ログアウト</button>
            </form>
        </div>
    <div class="header">
        <h1>📦 タスクトレジャー</h1>
    </div>

    <div class="user-selector">
        <select id="userSelect" onchange="loadData()">
            <option value="user1">あなた</option>
            <option value="user2">奥様</option>
        </select>
    </div>

    <div class="status-board">
        <div>💎 ほうせき：</div>
        <div class="stone-count"><span id="stoneCount">0</span> こ</div>
    </div>

    <div class="gacha-section">
        <h2>たからばこを あける</h2>
        <div class="chest-icon" id="chestBox" onclick="drawGacha()">📦</div>
        <button class="btn" onclick="drawGacha()">💎 5こ つかって たからばこを開く</button>
        <div id="gachaResult" style="margin-top:12px; font-size:16px; min-height:20px; color:var(--rpg-gold);"></div>
    </div>

    <h2>潜入中の ダンジョンクエスト</h2>
    <div class="list-container" id="taskList">読み込み中...</div>

    <h2>手に入れた トレジャー（秘宝）</h2>
    <div class="list-container" id="rewardList">読み込み中...</div>

    <div class="admin-section">
        <h2 style="color:var(--rpg-gray);">デバッグメニュー</h2>
        <button class="btn" style="border-color:var(--rpg-gray); color:var(--rpg-gray); margin-bottom:20px;" onclick="resetDailyTasks()">☀️ まいにちの クエストを リセット</button>
        
        <h4 style="margin-bottom:8px; font-size:14px; color:var(--rpg-gray);">➕ クエストを 新しく つくる</h4>
        <div class="form-group">
            <label>クエストめい</label>
            <input type="text" id="newTaskName" placeholder="れい: 読書をする">
        </div>
        <div style="display:flex; gap:12px; margin-bottom:16px;">
            <div class="form-group" style="flex:1;">
                <label>もらえる ほうせき数</label>
                <input type="number" id="newTaskReward" value="3">
            </div>
            <div class="form-group" style="flex:1;">
                <label>タイプ</label>
                <select id="newTaskType">
                    <option value="毎日">毎日</option>
                    <option value="単発">単発</option>
                    <option value="連続">連続</option>
                </select>
            </div>
        </div>
        <button class="btn" style="border-color:var(--rpg-gold); color:var(--rpg-gold);" onclick="addTask()">🎯 クエストを 登録する</button>

        <h4 style="margin-top:24px; margin-bottom:8px; font-size:14px; color:var(--rpg-gray);">➕ ご褒美（ガチャ景品）を 新しく つくる</h4>
        <div class="form-group">
            <label>ご褒美めい</label>
            <input type="text" id="newGachaName" placeholder="れい: 贅沢ケーキ">
        </div>
        <div class="form-group">
            <label>ないよう（詳細説明）</label>
            <input type="text" id="newGachaDescription" placeholder="れい: 近くの美味しいケーキ屋さんのやつ">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label>レアリティ</label>
            <select id="newGachaRarity">
                <option value="N">N（ノーマル）</option>
                <option value="R">R（レア）</option>
                <option value="SR">SR（Sレア）</option>
                <option value="SSR">SSR（SSレア）</option>
                <option value="UR">UR（ウルトラレア）</option>
            </select>
        </div>
        <button class="btn" style="border-color:var(--rpg-gold); color:var(--rpg-gold);" onclick="addGachaItem()">💎 ご褒美を 登録する</button>
        <!-- 💡新設：ご褒美一覧を表示・非表示するトグルエリアよ！ -->
        <div style="margin-top: 16px;">
            <button class="btn" style="background-color: #333; color: #fff; font-size: 12px; padding: 4px 8px;" onclick="toggleGachaList()">👁 ガチャの中身を 見る / 隠す</button>
            <div id="gachaListContainer" style="display: none; margin-top: 12px; padding: 12px; border: 2px dashed var(--rpg-gray); background: #111;">
                <p style="font-size: 12px; color: var(--rpg-gold); margin-bottom: 8px;">【現在の宝箱の中身】</p>
                <div id="gachaItemList" style="font-size: 12px; line-height: 1.6;"></div>
            </div>
        </div>
    </div>
</div>

<script>
// 💡 window. をつけることで、どこからでも見える最強の変数になるのさ！
window.currentUserId = {{ Auth::id() ?? 'null' }};

const API_BASE_URL = '/api';
const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
};
// 1. データの読み込み処理
async function loadData() {
    try {
        const res = await fetch(`${API_BASE_URL}/load?user=${currentUserId}`);
        const data = await res.json(); 

        if (data.error) {
            console.error("【サーバーエラーの正体】:", data.message);
            alert("サーバーでエラーが起きたみたい！F12キーでコンソールを確認してね！\n原因: " + data.message);
            return;
        }

        // ほうせき（所持数）の描画
        if (document.getElementById("stoneCount")) {
            document.getElementById("stoneCount").innerText = data.stones;
        }

        // クエスト一覧の描画
        const taskListDiv = document.getElementById("taskList");
        taskListDiv.innerHTML = "";
        data.tasks.forEach(task => {
            const isCompleted = task.status === '完了';
            const btnHtml = isCompleted 
                ? `<span class="status-badge">完了済み</span>`
                : `<button class="btn btn-success" onclick="completeTask(${task.task_id}, ${task.stone_reward})">🎯 クリア</button>`;
            
            taskListDiv.innerHTML += `
                <div class="list-item">
                    <div class="task-info">
                        <span class="task-name">${task.task_name}</span>
                        <span class="task-meta">${task.type}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="color: var(--rpg-gold); font-weight: bold; font-size: 16px;">💎+${task.stone_reward}</span>
                        ${btnHtml}
                    </div>
                </div>
            `;
        });

        // トレジャー（ご褒美ストック）の描画
        const rewardListDiv = document.getElementById("rewardList");
        rewardListDiv.innerHTML = "";
        data.rewards.forEach(rw => {
            if(rw.stock_count > 0) {
                rewardListDiv.innerHTML += `
                    <div class="list-item">
                        <div class="task-info">
                            <span class="task-name">🎁 ${rw.reward_name} (残り ${rw.stock_count}個)</span>
                        </div>
                        <button class="btn btn-success" onclick="consumeReward('${rw.reward_name}')">つかう</button>
                    </div>
                `;
            }
        });

        // ガチャの景品一覧の描画
        const gachaListDiv = document.getElementById("gachaItemList");
        gachaListDiv.innerHTML = "";
        if (data.gachaItems && data.gachaItems.length > 0) {
            data.gachaItems.forEach(item => {
                const desc = item.description ? ` (${item.description})` : '';
                gachaListDiv.innerHTML += `
                    <div class="list-item" style="margin-bottom: 8px; background-color: rgba(0,0,0,0.8);">
                        <div class="task-info">
                            <span class="task-name" style="color: var(--rpg-gold);">[${item.rarity}] ${item.item_name}</span>
                            <span class="task-meta">${desc}</span>
                        </div>
                        <button class="btn btn-success" style="border-color: var(--rpg-red); color: var(--rpg-red);" onclick="deleteGachaItem(${item.id})">🗑️ 消す</button>
                    </div>
                `;
            });
        } else {
            gachaListDiv.innerHTML = "ご褒美が ひとつも登録されていないね";
        }

    // 💡 エラーの原因：この catch ブロックが誤って消えていた可能性が高いです！
    } catch (e) {
        console.error("データよみこみエラー:", e);
    }
}

// 2. クエストクリア処理
async function completeTask(taskId, stoneReward) {
    try {
        const res = await fetch(`${API_BASE_URL}/complete`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                user_id: window.currentUserId, 
                task_id: taskId 
            })
        });
        const data = await res.json();

        if(data.success) {
            loadData();
        } else {
            console.error("サーバーからの返事に success がないの:", data);
            alert("クリア処理に失敗したの。F12キーでコンソールを確認してちょうだい！");
        }
    // 💡 ここから下の catch が消えていたのが原因よ！
    } catch(e) {
        console.error("通信エラー:", e);
        alert("通信エラーなの。コンソールを確認してちょうだい！");
    }
}
// 3. ガチャを引く処理
async function drawGacha() {
    try {
        const res = await fetch(`${API_BASE_URL}/gacha`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ user_id: currentUserId }) // 💡 名前を user_id に統一
        });
        const data = await res.json();
        if(data.success) {
            alert(`🎉 宝箱から [ ${data.reward} ] が飛び出してきたよ！`);
            loadData();
        } else {
            alert(data.message);
        }
    } catch(e) { console.error(e); }
}

// 4. トレジャー消費処理
async function consumeReward(rewardName) {
    try {
        const res = await fetch(`${API_BASE_URL}/consume`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ user_id: window.currentUserId, rewardName: rewardName })
        });
        const data = await res.json();
        
        if(data.success) {
            // 💡 ここでメッセージを出すのよ！変数と文字を組み合わせているの
            alert(`${rewardName} を 使いました！`);
            
            // メッセージを閉じたあとに、リストの個数を最新の状態にするの
            loadData();
        } else {
            console.error("サーバーからのエラー:", data);
            alert("使うのに失敗したの。コンソールを確認してちょうだい。");
        }
    } catch(e) { 
        console.error("通信エラー:", e); 
        alert("通信エラーなの。コンソールを確認してちょうだい。");
    }
}
// 5. 新しいクエストを登録する処理
async function addTask() {
    const name = document.getElementById("newTaskName").value;
    const reward = document.getElementById("newTaskReward").value;
    const type = document.getElementById("newTaskType").value;
    
    // 入力チェック
    if(!name) {
        alert("クエスト名をいれてちょうだい！");
        return; 
    }
    
    try {
        // デバッグ用：送信する直前のデータをコンソールに表示
        console.log("フロントエンドから送信するデータ:", { user_id: window.currentUserId, task_name: name, stone_reward: reward, type: type });

        const res = await fetch(`${API_BASE_URL}/add-task`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ 
                user_id: window.currentUserId,
                task_name: name,        
                stone_reward: reward,   
                type: type              
            })
        });
        
        // JSONとしてデータを受け取る
        const data = await res.json();
        
        // デバッグ用：サーバーから返ってきた生データをコンソールに表示
        console.log("サーバーからの返事:", data);
        
        if(data.success) {
            document.getElementById("newTaskName").value = "";
            loadData();
            alert("クエストの登録に成功しました！"); // 成功したことが画面でわかるように追加
        } else {
            // success が無かった場合の処理を追加
            console.error("サーバーからの返事に success がありません:", data);
            alert("登録に失敗しました。F12キーでコンソールを確認してください。");
        }
    } catch(e) { 
        // ネットワークエラーなど、通信そのものが失敗した場合
        console.error("通信エラーが発生しました:", e); 
        alert("通信エラーが発生しました。コンソールを確認してください。");
    }
}


    // 6. 毎日のクエストをリセットする処理
    async function resetDailyTasks() {
        try {
            const res = await fetch(`${API_BASE_URL}/reset-daily`, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ user_id: window.currentUserId })
            });
            const data = await res.json();
            
            if(data.success) {
                // 💡 ここでメッセージを出すのよ！
                alert("クエストをリセットしました。");
                
                // メッセージを閉じた後に画面のデータを最新にするの
                loadData();
            } else {
                console.error("サーバーからのエラー:", data);
                alert("リセットに失敗したの。コンソールを確認してちょうだい。");
            }
        } catch(e) { 
            console.error("通信エラー:", e); 
            alert("通信エラーなの。コンソールを確認してちょうだい。");
        }
    }

// 7. 新しいご褒美を登録する処理
async function addGachaItem() {
    const name = document.getElementById("newGachaName").value;
    const description = document.getElementById("newGachaDescription").value;
    const rarity = document.getElementById("newGachaRarity").value;
    
    if(!name) return alert("ご褒美の なまえを いれてちょうだい！");
    
    try {
        const res = await fetch(`${API_BASE_URL}/add-gacha-item`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ 
                user_id: currentUserId, // 💡 ここにもIDを追加したよ！
                item_name: name, 
                description: description, 
                rarity: rarity 
            })
        });
        const data = await res.json();
        if(data.success) {
            alert("宝箱に 新しいご褒美が 補充されたよ！");
            document.getElementById("newGachaName").value = "";
            document.getElementById("newGachaDescription").value = "";
            loadData();
        }
    } catch(e) { console.error(e); }
}

// 8. ご褒美を削除する処理
async function deleteGachaItem(id) {
    if (!confirm("本当にこのご褒美を消していいのかな？")) return; 
    
    try {
        const res = await fetch(`${API_BASE_URL}/delete-gacha-item`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
            // 削除に成功したら、画面のリストを最新の状態にするの！
            loadData(); 
        } else {
            console.error("削除エラー:", data);
            alert("削除に失敗したの。");
        }
    } catch (e) { 
        console.error("通信エラー:", e); 
    }
}

// 9. ガチャリストの表示切り替え（隠す機能）
function toggleGachaList() {
    const container = document.getElementById("gachaListContainer");
    if (container.style.display === "none") {
        // 隠れていたら表示するの
        container.style.display = "block";
    } else {
        // 表示されていたら隠すの
        container.style.display = "none";
    }
}

// 10. 画面の読み込みが終わった瞬間に、自動でデータを引っ張ってくる処理ね
window.addEventListener('DOMContentLoaded', function() {
    loadData();
});
</script>
</body>
</html>