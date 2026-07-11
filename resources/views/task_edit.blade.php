<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>タスク編集 - タスクトレジャー</title>
    <style>
        :root {
            --rpg-black: #000000;
            --rpg-window: rgba(10, 10, 15, 0.95);
            --rpg-border: #ffffff;
            --rpg-text: #ffffff;
            --rpg-gold: #ffcc00;
            --rpg-gray: #aaaaaa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #2c2c2c;
            background-image: radial-gradient(#1a1a1a 25%, transparent 25%), radial-gradient(#1a1a1a 25%, transparent 25%);
            background-size: 16px 16px;
            background-position: 0 0, 8px 8px;
            color: var(--rpg-text);
            padding: 12px;
            padding-bottom: 80px; /* ボトムメニュー用に広げるの */
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: var(--rpg-window);
            border: 4px double var(--rpg-border);
            border-radius: 8px;
            padding: 16px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 4px dashed var(--rpg-border);
        }

        h1 { font-size: 24px; color: var(--rpg-text); }
        h2 { font-size: 18px; margin-bottom: 12px; color: var(--rpg-gold); }

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
            text-decoration: none;
        }
        .btn:active { background-color: var(--rpg-text); color: var(--rpg-black); }

        .btn-edit {
            border-color: var(--rpg-gold);
            color: var(--rpg-gold);
            padding: 6px 12px;
            font-size: 14px;
            width: auto;
        }

        .list-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 400px;
            overflow-y: auto;
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

        /* 編集ボックスのデザイン */
        .edit-box {
            display: none; /* 最初は隠しておくの */
            margin-top: 20px;
            border: 2px dashed var(--rpg-gold);
            padding: 16px;
            background-color: rgba(20, 20, 0, 0.8);
            border-radius: 8px;
        }

        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: 13px; color: var(--rpg-gray); margin-bottom: 4px; }
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
        input:focus, select:focus { border-color: var(--rpg-gold); }

        /* ボトムメニュー */
        .bottom-menu {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background-color: var(--rpg-window);
            border-top: 2px solid var(--rpg-border);
            display: flex; justify-content: space-around;
            padding: 8px 0; z-index: 1000;
        }
        .menu-item {
            display: flex; flex-direction: column; align-items: center;
            text-decoration: none; color: var(--rpg-text); font-size: 11px; flex: 1;
        }
        .menu-icon { font-size: 24px; margin-bottom: 4px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>✏️ タスクを 編集する</h1>
    </div>

    <h2>クエスト一覧</h2>
    <div class="list-container" id="taskList">読み込み中...</div>

    <!-- ここが編集ボックス！ボタンを押すと現れるの -->
    <div id="editBox" class="edit-box">
        <h3 style="color: var(--rpg-gold); margin-bottom: 12px;">クエストを 書き換える</h3>
        <input type="hidden" id="editTaskId">
        <div class="form-group">
            <label>クエスト名</label>
            <input type="text" id="editTaskName" maxlength="30">
        </div>
        <div class="form-group">
            <label>もらえる ほうせき数</label>
            <input type="number" id="editTaskReward">
        </div>
        <div class="form-group">
            <label>タイプ</label>
            <select id="editTaskType">
                <option value="毎日">毎日</option>
                <option value="単発">単発</option>
                <option value="連続">連続</option>
            </select>
        </div>
        <button class="btn" style="border-color:var(--rpg-gold); color:var(--rpg-gold);" onclick="saveEdit()">💾 保存する</button>
        <button class="btn" style="margin-top: 10px;" onclick="closeEditBox()">❌ やめる</button>
    </div>
</div>

<nav class="bottom-menu">
    <a href="/" class="menu-item"><span class="menu-icon">🏠</span><span class="menu-text">ホーム</span></a>
    <a href="/task-edit" class="menu-item" style="color: var(--rpg-gold);"><span class="menu-icon">✏️</span><span class="menu-text">タスク編集</span></a>
</nav>

<script>
window.currentUserId = {{ Auth::id() ?? 1 }}; // ログインID（仮）
const API_BASE_URL = '/api';

// 1. タスク一覧を読み込む
async function loadEditTasks() {
    try {
        const res = await fetch(`${API_BASE_URL}/load?user=${window.currentUserId}`);
        const data = await res.json();
        
        const taskListDiv = document.getElementById("taskList");
        taskListDiv.innerHTML = "";

        data.tasks.forEach(task => {
            // taskオブジェクトを文字にしてボタンに埋め込むの
            const taskJson = JSON.stringify(task).replace(/"/g, '&quot;');
            
            taskListDiv.innerHTML += `
                <div class="list-item">
                    <div style="max-width: 60%;">
                        <div style="font-size: 16px;">${task.task_name}</div>
                        <div style="font-size: 12px; color: var(--rpg-gray);">💎${task.stone_reward} / ${task.type}</div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-edit" onclick="openEditBox('${taskJson}')">✏️ 編集</button>
                        <button class="btn" style="border-color: var(--rpg-red); color: var(--rpg-red); padding: 6px 12px; font-size: 14px;" onclick="deleteTask(${task.task_id})">🗑️ 削除</button>
                    </div>
                </div>
            `;
        });
    } catch (e) {
        console.error(e);
    }
}

// 2. 編集ボックスを開く
function openEditBox(taskString) {
    const task = JSON.parse(taskString);
    
    // 選んだタスクのデータを入れる
    document.getElementById("editTaskId").value = task.task_id;
    document.getElementById("editTaskName").value = task.task_name;
    document.getElementById("editTaskReward").value = task.stone_reward;
    document.getElementById("editTaskType").value = task.type;

    // 編集ボックスを表示する
    document.getElementById("editBox").style.display = "block";
    
    // スクロールして編集ボックスを見せる
    document.getElementById("editBox").scrollIntoView({ behavior: "smooth" });
}

// 3. 編集ボックスを閉じる
function closeEditBox() {
    document.getElementById("editBox").style.display = "none";
}

// 4. 書き換えたデータをサーバーに送る
async function saveEdit() {
    const id = document.getElementById("editTaskId").value;
    const name = document.getElementById("editTaskName").value;
    const reward = document.getElementById("editTaskReward").value;
    const type = document.getElementById("editTaskType").value;

    if (!name) return alert("クエスト名をいれてちょうだい！");

    try {
        const res = await fetch(`${API_BASE_URL}/edit-task`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                task_id: id,
                task_name: name,
                stone_reward: reward,
                type: type
            })
        });
        const data = await res.json();

        if(data.success) {
            alert("クエストの書き換えに成功したわ！");
            closeEditBox();
            loadEditTasks(); // 一覧を最新にするの
        } else {
            alert("保存に失敗したの。");
        }
    } catch (e) {
        console.error(e);
    }
}

// 5. クエストを削除する処理
async function deleteTask(taskId) {
    // 間違えて押した時のために確認する！
    if (!confirm("本当にこのクエストを消していいの？")) {
        return; 
    }

    try {
        const res = await fetch(`${API_BASE_URL}/delete-task`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                task_id: taskId
            })
        });
        const data = await res.json();

        if (data.success) {
            alert("クエストを綺麗に消し去った！");
            loadEditTasks(); // リストを最新の状態に更新する
        } else {
            alert("削除に失敗したみたい。コンソールを確認してちょうだい。");
        }
    } catch (e) {
        console.error("通信エラー:", e);
        alert("通信エラーなの。コンソールを確認してちょうだい。");
    }
}

// 最初から読み込む
window.addEventListener('DOMContentLoaded', loadEditTasks);
</script>
</body>
</html>