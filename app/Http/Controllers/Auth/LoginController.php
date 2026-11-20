<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // ゲストログイン設定
    private const GUEST_USER_ID = 2;

    /**
     * ゲストログイン（ポートフォリオ閲覧用）
     */
    public function guestLogin()
    {
        if (!config('auth.allow_guest_login', true)) {
            abort(403, 'ゲストログインは無効化されています。');
        }

        $guestUserId = config('auth.guest_user_id', self::GUEST_USER_ID);
        $guestUser = \App\User::find($guestUserId);
        if (!$guestUser) {
            return redirect()->route('login')->withErrors(['email' => 'ゲストユーザーが存在しません。']);
        }

        Auth::login($guestUser);
        return redirect('/');
    }
}
