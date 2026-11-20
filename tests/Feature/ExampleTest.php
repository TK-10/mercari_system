<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        // 未ログイン時は /login にリダイレクトされることを確認
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // ログイン画面が正常に表示されることを確認
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }
}
