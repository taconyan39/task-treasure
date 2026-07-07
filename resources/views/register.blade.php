<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>冒険の準備（新規登録）</title>
    <style>
        body { background-color: #222; color: #fff; font-family: sans-serif; text-align: center; padding-top: 50px; }
        .box { background-color: #333; padding: 20px; border-radius: 8px; width: 300px; margin: 0 auto; border: 2px dashed #cc9933; }
        input { width: 90%; padding: 10px; margin: 10px 0; border-radius: 4px; border: none; }
        .btn { background-color: #cc9933; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; width: 95%; }
        .btn:hover { background-color: #ffcc00; }
        a { color: #3399ff; font-size: 12px; }
    </style>
</head>
<body>
    <h1>タスクトレジャー</h1>
    <div class="box">
        <h2>冒険者ギルドへ登録</h2>
        <form method="POST" action="/register">
            @csrf
            <input type="text" name="name" placeholder="ユーザーネーム" required>
            <input type="email" name="email" placeholder="メールアドレス" required>
            <input type="password" name="password" placeholder="パスワード（8文字以上）" required>
            <button type="submit" class="btn">ギルドに登録する</button>
        </form>
        <p><a href="/login">すでに登録している場合はこちら</a></p>
    </div>
</body>
</html>