<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // 1. 新規登録画面を表示
    public function showRegister() {
        return view('register');
    }

    // 2. 新規登録の処理（💡 ここが「関数」の箱なの！）
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // パスワードを暗号化してデータベースに保存
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 💡 第2引数の true が「ずっとログイン状態を保持する」魔法のスイッチ！
        Auth::login($user, true);

        return redirect('/'); // タスクトレジャーのメイン画面へ移動
    }

    // 3. ログイン画面を表示
    public function showLogin() {
        return view('login');
    }

    // 4. ログインの処理
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 💡 ここでも true を渡すことで、次回からパスワード入力が不要になる！
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect('/'); // タスクトレジャーのメイン画面へ移動
        }

        return back()->withErrors(['email' => 'メールアドレスかパスワードが違うみたい。']);
    }

    // 5. ログアウトの処理
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}