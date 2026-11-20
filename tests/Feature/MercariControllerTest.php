<?php

namespace Tests\Feature;

use App\History;
use App\Item;
use App\Mercari;
use App\Sold;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MercariControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 共通のテストユーザーを作成
        $this->user = User::create([
            'name'     => 'AdminTester',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * 未ログインユーザーは各管理画面から /login にリダイレクトされることを確認
     */
    public function testUnauthenticatedUserIsRedirectedToLogin()
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/item')->assertRedirect('/login');
        $this->get('/addItem')->assertRedirect('/login');
        $this->get('/mercariOrder')->assertRedirect('/login');
        $this->get('/chart')->assertRedirect('/login');
    }

    /**
     * 商品一覧画面で有効な商品のみが表示され、論理削除された商品が表示されないことを確認
     */
    public function testItemListDisplaysOnlyActiveItems()
    {
        $activeItem = Item::create([
            'item_id'    => 100001,
            'item_name'  => '有効商品【100001】',
            'item_price' => 1500,
            'stock'      => 10,
            'del_flg'    => 0,
        ]);

        $deletedItem = Item::create([
            'item_id'    => 100002,
            'item_name'  => '削除済み商品【100002】',
            'item_price' => 2000,
            'stock'      => 5,
            'del_flg'    => 1,
        ]);

        $response = $this->actingAs($this->user)->get('/item');
        $response->assertStatus(200);
        $response->assertSee('有効商品【100001】');
        $response->assertDontSee('削除済み商品【100002】');
    }

    /**
     * 商品一覧で商品名検索（LIKE検索）が正しく動作することを確認
     */
    public function testItemListSearchFiltersCorrectly()
    {
        Item::create([
            'item_id'    => 100010,
            'item_name'  => 'リンゴジュース【100010】',
            'item_price' => 300,
            'stock'      => 20,
            'del_flg'    => 0,
        ]);

        Item::create([
            'item_id'    => 100011,
            'item_name'  => 'オレンジジュース【100011】',
            'item_price' => 350,
            'stock'      => 15,
            'del_flg'    => 0,
        ]);

        $response = $this->actingAs($this->user)->get('/item?shouhin_name=' . urlencode('リンゴ'));
        $response->assertStatus(200);
        $response->assertSee('リンゴジュース【100010】');
        $response->assertDontSee('オレンジジュース【100011】');
    }

    /**
     * 商品詳細画面で有効な商品が表示され、削除済み商品では404が返ることを確認
     */
    public function testDetailViewReturnsItemOr404WhenDeleted()
    {
        $item = Item::create([
            'item_id'    => 200001,
            'item_name'  => '詳細テスト商品【200001】',
            'item_price' => 5000,
            'stock'      => 3,
            'detail'     => 'これは詳細テストの説明文です。',
            'del_flg'    => 0,
        ]);

        $response = $this->actingAs($this->user)->get('/detail/' . $item->item_id);
        $response->assertStatus(200);
        $response->assertSee('これは詳細テストの説明文です。');

        // 削除後にアクセスした場合は 404
        $item->update(['del_flg' => 1]);
        $notFoundResponse = $this->actingAs($this->user)->get('/detail/' . $item->item_id);
        $notFoundResponse->assertStatus(404);
    }

    /**
     * 商品編集画面および商品更新処理の動作確認（操作者名がAuthから安全に記録されることの検証含む）
     */
    public function testEditItemAndVerifyAuditLogIntegrity()
    {
        $item = Item::create([
            'item_id'    => 300001,
            'item_name'  => '変更前商品【300001】',
            'item_price' => 1000,
            'stock'      => 5,
            'detail'     => '初期詳細',
            'del_flg'    => 0,
        ]);

        // 編集画面へのアクセス
        $this->actingAs($this->user)->get('/edit/' . $item->item_id)->assertStatus(200);

        // 更新処理の送信（悪意あるユーザー名 'Hacker' を渡しても無視されることを確認）
        $response = $this->actingAs($this->user)->post('/editComplet', [
            'item_id'      => $item->item_id,
            'shouhin_name' => '変更後商品',
            'item_price'   => 1200,
            'item_stock'   => 8,
            'item_detail'  => '更新後の詳細',
            'user_name'    => 'HackerFakeName',
        ]);

        $response->assertStatus(200);

        // DBが更新されたことを確認
        $this->assertDatabaseHas('items', [
            'item_id'    => $item->item_id,
            'item_name'  => '変更後商品【300001】',
            'item_price' => 1200,
            'stock'      => 8,
            'detail'     => '更新後の詳細',
        ]);

        // 履歴ログの操作者名がログインユーザー名（AdminTester）になっていることを確認
        $history = History::where('item_id', $item->item_id)->latest()->first();
        $this->assertNotNull($history);
        $this->assertEquals(3, $history->status); // 3: 編集
        $this->assertEquals('AdminTester', $history->user_name);
        $this->assertNotEquals('HackerFakeName', $history->user_name);
    }

    /**
     * POST商品削除処理の動作確認（del_flg更新・履歴登録・Authユーザー名検証）
     */
    public function testDeleteItemViaPostSetsDelFlgAndLogsHistory()
    {
        $item = Item::create([
            'item_id'    => 400001,
            'item_name'  => '削除対象商品【400001】',
            'item_price' => 2000,
            'stock'      => 2,
            'del_flg'    => 0,
        ]);

        $response = $this->actingAs($this->user)->post('/delete/' . $item->item_id);
        $response->assertRedirect('/item');

        // del_flg が 1 に更新されたことを確認
        $this->assertDatabaseHas('items', [
            'item_id' => $item->item_id,
            'del_flg' => 1,
        ]);

        // 履歴に削除ログが記録されたことを確認
        $history = History::where('item_id', $item->item_id)->latest()->first();
        $this->assertNotNull($history);
        $this->assertEquals(5, $history->status); // 5: 削除
        $this->assertEquals('削除対象商品【400001】', $history->item_name);
        $this->assertEquals('AdminTester', $history->user_name);
    }

    /**
     * 在庫追加画面および在庫加算処理の動作確認
     */
    public function testAddStockCompletIncrementsStockCorrectly()
    {
        $item = Item::create([
            'item_id'    => 500001,
            'item_name'  => '在庫追加対象商品【500001】',
            'item_price' => 3000,
            'stock'      => 10,
            'del_flg'    => 0,
        ]);

        // 在庫追加画面表示
        $this->actingAs($this->user)->get('/stockAdd/' . $item->item_id)->assertStatus(200);

        // 在庫5個追加
        $response = $this->actingAs($this->user)->post('/addStockComplet', [
            'item_id'    => $item->item_id,
            'item_stock' => 5,
        ]);
        $response->assertStatus(200);

        // 在庫が 10 + 5 = 15 になったことを確認
        $this->assertDatabaseHas('items', [
            'item_id' => $item->item_id,
            'stock'   => 15,
        ]);

        // 履歴確認（ステータス4: 在庫数追加）
        $history = History::where('item_id', $item->item_id)->latest()->first();
        $this->assertNotNull($history);
        $this->assertEquals(4, $history->status);
        $this->assertEquals(15, $history->stock);
        $this->assertEquals('AdminTester', $history->user_name);
    }

    /**
     * 新規商品登録処理（暗号学的乱数ID・Sold作成・履歴作成）の動作確認
     */
    public function testAddItemInsertCreatesItemSoldAndHistory()
    {
        $this->actingAs($this->user)->get('/addItem')->assertStatus(200);

        $response = $this->actingAs($this->user)->post('/addComplet', [
            'item_name'   => '新規登録商品XYZ',
            'item_price'  => 4500,
            'item_stock'  => 12,
            'item_detail' => '新規登録商品の説明です。',
        ]);

        $response->assertStatus(200);

        // itemsテーブルに対象商品が登録されたことを確認
        $createdItem = Item::where('item_name', 'like', '新規登録商品XYZ【%')->first();
        $this->assertNotNull($createdItem);
        $this->assertEquals(4500, $createdItem->item_price);
        $this->assertEquals(12, $createdItem->stock);
        $this->assertEquals(0, $createdItem->del_flg);
        $this->assertNotNull($createdItem->item_id);

        // soldsテーブルにレコードが作成されたことを確認
        $this->assertDatabaseHas('solds', [
            'item_id'    => $createdItem->item_id,
            'item_name'  => '新規登録商品XYZ',
            'sold_count' => 0,
        ]);

        // historiesテーブルに登録ログが作成されたことを確認
        $history = History::where('item_id', $createdItem->item_id)->first();
        $this->assertNotNull($history);
        $this->assertEquals(2, $history->status); // 2: 登録
        $this->assertEquals('AdminTester', $history->user_name);
    }

    /**
     * 類似商品の重複警告リダイレクト動作確認
     */
    public function testAddItemInsertWarnsWhenSimilarItemExists()
    {
        Item::create([
            'item_id'    => 600001,
            'item_name'  => '完全一致類似テスト商品【600001】',
            'item_price' => 1000,
            'stock'      => 5,
            'del_flg'    => 0,
        ]);

        $response = $this->actingAs($this->user)->post('/addComplet', [
            'item_name'   => '完全一致類似テスト商品',
            'item_price'  => 1000,
            'item_stock'  => 5,
        ]);

        // 重複警告により /addItem へリダイレクトされることを確認
        $response->assertRedirect('/addItem');
        $response->assertSessionHas('flash_message', '似ている商品があります');
    }

    /**
     * Mercari注文一覧画面の動作確認
     */
    public function testMercariOrderList()
    {
        Mercari::create([
            'mid'         => 'M00001',
            'item_name'   => 'メルカリ取引商品A',
            'item_price'  => 2500,
            'buyer_name'  => '購入者山田',
            'shorikubun'  => 1, // 未到着
        ]);

        $response = $this->actingAs($this->user)->get('/mercariOrder');
        $response->assertStatus(200);
        $response->assertSee('M00001');
        $response->assertSee('メルカリ取引商品A');
        $response->assertSee('購入者山田');
    }

    /**
     * チャート表示画面の動作確認
     */
    public function testChartDisplaysData()
    {
        Sold::create([
            'item_id'    => 700001,
            'item_name'  => 'チャートテスト商品',
            'sold_count' => 50,
        ]);

        $response = $this->actingAs($this->user)->get('/chart');
        $response->assertStatus(200);
        $response->assertSee('チャートテスト商品');
    }

    /**
     * チャート画面でXSSペイロードが含まれる商品名が安全にエスケープされることを確認
     */
    public function testChartPreventsXSSInItemName()
    {
        Sold::create([
            'item_id'    => 700002,
            'item_name'  => '</script><script>alert("XSS")</script>',
            'sold_count' => 10,
        ]);

        $response = $this->actingAs($this->user)->get('/chart');
        $response->assertStatus(200);
        // raw な </script> タグが出現せず、\u003C にエスケープされていることを確認
        $response->assertDontSee('</script><script>alert("XSS")</script>', false);
        $response->assertSee('\u003C\/script\u003E', false);
    }

    /**
     * 削除済み商品に対する編集・在庫追加・再削除が404でブロックされることを確認
     */
    public function testDeletedItemCannotBeEditedOrStockAdded()
    {
        $deleted = Item::create([
            'item_id'    => 800001,
            'item_name'  => '削除済み商品【800001】',
            'item_price' => 1000,
            'stock'      => 5,
            'del_flg'    => 1,
        ]);

        // 削除済み商品の編集更新 -> 404
        $this->actingAs($this->user)->post('/editComplet', [
            'item_id'      => $deleted->item_id,
            'shouhin_name' => '不正更新試行',
            'item_price'   => 1500,
            'item_stock'   => 5,
        ])->assertStatus(404);

        // 削除済み商品への在庫追加 -> 404
        $this->actingAs($this->user)->post('/addStockComplet', [
            'item_id'    => $deleted->item_id,
            'item_stock' => 10,
        ])->assertStatus(404);

        // 削除済み商品の再削除 -> 404
        $this->actingAs($this->user)->post('/delete/' . $deleted->item_id)->assertStatus(404);
    }

    /**
     * 不正な入力値（価格0や負数など）がバリデーションで弾かれることを確認
     */
    public function testValidationRejectsInvalidPriceAndStock()
    {
        $response = $this->actingAs($this->user)->post('/addComplet', [
            'item_name'  => 'バリデーションテスト',
            'item_price' => -100, // 不正な価格
            'item_stock' => -5,   // 不正な在庫
        ]);

        $response->assertSessionHasErrors(['item_price', 'item_stock']);
    }

    /**
     * LIKE検索でワイルドカード文字（%）がエスケープされ、部分一致しないことを確認
     */
    public function testLikeQueryEscapesWildcardCharacters()
    {
        Item::create([
            'item_id'    => 900001,
            'item_name'  => '100%天然果汁ジュース【900001】',
            'item_price' => 500,
            'stock'      => 10,
            'del_flg'    => 0,
        ]);

        Item::create([
            'item_id'    => 900002,
            'item_name'  => '1000円均一商品【900002】',
            'item_price' => 1000,
            'stock'      => 10,
            'del_flg'    => 0,
        ]);

        // "100%" で検索（% がエスケープされていれば 100% のみマッチし、1000円均一商品にはマッチしない）
        $response = $this->actingAs($this->user)->get('/item?shouhin_name=' . urlencode('100%'));
        $response->assertStatus(200);
        $response->assertSee('100%天然果汁ジュース【900001】');
        $response->assertDontSee('1000円均一商品【900002】');
    }
}
