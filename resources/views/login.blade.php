<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <style>
        body { background-color: #222; color: #fff; font-family: sans-serif; text-align: center; padding-top: 50px; }
        .box { background-color: #333; padding: 20px; border-radius: 8px; width: 300px; margin: 0 auto; border: 2px solid #cc9933; }
        input { width: 90%; padding: 10px; margin: 10px 0; border-radius: 4px; border: none; }
        .btn { background-color: #cc9933; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; width: 95%; }
        .btn:hover { background-color: #ffcc00; }
        a { color: #3399ff; font-size: 12px; }
        .error { color: #ff6666; font-size: 12px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>タスクトレジャー</h1>
    <div class="box">
        <h2>ログイン</h2>
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="/login">
            @csrf
            <input type="email" name="email" placeholder="メールアドレス" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit" class="btn">冒険を再開する</button>
        </form>
        <p><a href="/register">新しく登録する場合はこちら</a></p>
    </div>
</body>
</html>