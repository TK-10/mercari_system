<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 新規登録画面が無効化（404 Not Found）されていることを確認
     */
    public function testRegistrationRouteIsDisabled()
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $postResponse = $this->post('/register', [
            'name' => 'attacker',
            'email' => 'attacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $postResponse->assertStatus(404);
    }

    /**
     * パスワードリセット画面が無効化（404 Not Found）されていることを確認
     */
    public function testPasswordResetRouteIsDisabled()
    {
        $response = $this->get('/password/reset');
        $response->assertStatus(404);
    }

    /**
     * ログイン画面にゲストログインリンクが表示されることを確認
     */
    public function testLoginViewDisplaysGuestLoginLinkByDefault()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('ゲストログイン');
        $response->assertSee(route('login.guest'));
    }

    /**
     * ゲストログインが有効時（デフォルト）に正常にログインできることを確認
     */
    public function testGuestLoginWorksSuccessfully()
    {
        $guestUser = new User([
            'name' => 'GuestUser',
            'email' => 'guest@example.com',
            'password' => bcrypt('password123'),
        ]);
        $guestUser->id = 2;
        $guestUser->save();

        $response = $this->get('/guest');
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($guestUser);
    }

    /**
     * ゲストログインが無効設定時にアクセス拒否（403 Forbidden）されることを確認
     */
    public function testGuestLoginIsBlockedWhenDisabled()
    {
        config(['auth.allow_guest_login' => false]);
        $response = $this->get('/guest');
        $response->assertStatus(403);
    }

    /**
     * GETリクエストでの商品削除が無効（405 Method Not Allowed）であることを確認
     */
    public function testGetDeleteMethodIsNotAllowed()
    {
        $response = $this->get('/delete/1');
        $response->assertStatus(405);
    }
}
