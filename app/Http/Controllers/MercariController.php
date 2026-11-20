<?php

namespace App\Http\Controllers;

use App\History;
use App\Item;
use App\Mercari;
use App\Sold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MercariController extends Controller
{
    //コンストラクタ （このクラスが呼ばれると最初にこの処理をする）
    public function __construct()
    {
        // ログインしていなかったらログインページに遷移する
        $this->middleware('auth');
    }

    /**
     * SQL LIKE 検索用のワイルドカード文字をエスケープ
     */
    private function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

    /**
     * DBエンジンに依存せず安全にワイルドカードをエスケープしてLIKE検索を行う
     */
    private function applyLikeQuery($query, string $column, string $value)
    {
        $escaped = $this->escapeLike($value);
        if ($query->getConnection()->getDriverName() === 'sqlite') {
            return $query->whereRaw("{$column} LIKE ? ESCAPE '\\'", ['%'.$escaped.'%']);
        }
        return $query->where($column, 'LIKE', '%'.$escaped.'%');
    }

    //ホーム画面を表示
    public function index()
    {
        return view('mercari/index');
    }

    //商品情報一覧
    public function itemList(Request $request)
    {
        //キーワード受け取り
        $shouhin_id = $request->input('shouhin_id');
        $shouhin_id = mb_convert_kana($shouhin_id, 'n'); //全角数字で入力された場合、半角に変換
        $shouhin_name = $request->input('shouhin_name');
        $item_price = $request->input('item_price');
        $item_price = mb_convert_kana($item_price, 'n');
        $item_stock = $request->input('item_stock');
        $item_stock = mb_convert_kana($item_stock, 'n');

        // 論理削除されていない商品のみ取得
        $query = Item::query()->where('del_flg', 0);

        //商品ID（id または item_id で検索）
        if (!empty($shouhin_id)) {
            $query->where(function ($q) use ($shouhin_id) {
                $q->where('id', $shouhin_id)->orWhere('item_id', $shouhin_id);
            });
        }
        //商品名（ワイルドカードエスケープ適用）
        if (!empty($shouhin_name)) {
            $this->applyLikeQuery($query, 'item_name', $shouhin_name);
        }
        //商品価格（完全一致）
        if (!empty($item_price)) {
            $query->where('item_price', $item_price);
        }
        //在庫数（完全一致）
        if (!empty($item_stock)) {
            $query->where('stock', $item_stock);
        }

        //クエリの実行
        $searchs = $query->paginate(15);

        return view('mercari/item', ['searchs' => $searchs]);
    }

    //商品履歴
    public function historyList($item_id)
    {
        $history = History::where('item_id', $item_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stock = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->first();

        return view('mercari/history', ['history' => $history, 'stock' => $stock]);
    }

    //商品詳細
    public function detail($item_id)
    {
        $itemData = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->where('del_flg', 0)->firstOrFail();

        return view('mercari/detail', ['itemData' => $itemData]);
    }

    //商品編集画面
    public function editList($item_id)
    {
        $itemData = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->where('del_flg', 0)->firstOrFail();

        $cut = 8; //カットしたい文字数
        //商品名に含まれているIDの削除や変更を防ぐため、一旦名前とIDは切り離す
        $item_name_tr = mb_substr($itemData->item_name, 0, -$cut);

        return view('mercari/edit', ['itemData' => $itemData, 'item_name_tr' => $item_name_tr]);
    }

    //商品更新処理
    public function editComplet(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all(), [
            'item_id'      => ['required'],
            'shouhin_name' => ['required', 'string', 'max:255'],
            'item_price'   => ['required', 'integer', 'min:1', 'max:1000000'], // 1〜100万円
            'item_stock'   => ['required', 'integer', 'min:0', 'max:10000'],   // 0〜10000個
            'item_detail'  => ['nullable', 'string', 'max:2000'],
        ]);
        //バリデーションの結果がエラーの場合
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $item_name = $request->input('shouhin_name');
        $item_id = $request->input('item_id');

        $item = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->where('del_flg', 0)->firstOrFail();

        $target_item_id = $item->item_id ?? $item->id;
        $full_item_name = $item_name.'【'.$target_item_id.'】';

        //更新
        $itemUpdate = $item->update([
            'item_name'  => $full_item_name,
            'item_price' => $request->input('item_price'),
            'stock'      => $request->input('item_stock'),
            'detail'     => $request->input('item_detail'),
        ]);

        //更新後のデータを取得
        $itemData = $item->fresh();

        //商品履歴テーブル(histories)に登録（操作者名はセッションから安全に取得）
        $histories = new History();
        $histories->item_name = $full_item_name;
        $histories->item_price = $request->input('item_price');
        $histories->status = 3; //3 編集
        $histories->stock = $request->input('item_stock');
        $histories->item_id = $target_item_id;
        $histories->user_name = Auth::user()->name;

        $histories->save();

        return view('mercari/editComplete', ['itemUpdate' => $itemUpdate, 'itemData' => $itemData]);
    }

    //商品削除処理（POST経由）
    public function delete(Request $request, $item_id)
    {
        $item = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->where('del_flg', 0)->firstOrFail();

        $target_item_id = $item->item_id ?? $item->id;

        //更新（論理削除）
        $item->update([
            'del_flg' => 1,
        ]);

        //商品履歴テーブル(histories)に登録
        $histories = new History();
        $histories->item_name = $item->item_name;
        $histories->status = 5; //5 削除
        $histories->item_id = $target_item_id;
        $histories->user_name = Auth::user()->name;

        $histories->save();

        return redirect('/item');
    }

    //商品在庫数追加画面
    public function stockAdd($item_id)
    {
        //Itemテーブルから情報を取得
        $itemData = Item::where(function ($q) use ($item_id) {
            $q->where('item_id', $item_id)->orWhere('id', $item_id);
        })->where('del_flg', 0)->firstOrFail();

        return view('mercari/addStock', ['itemData' => $itemData]);
    }

    //商品在庫数追加処理
    public function addStockComplet(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all(), [
            'item_id'    => ['required'],
            'item_stock' => ['required', 'integer', 'min:0', 'max:10000'],   // 0〜10000個
        ]);
        //バリデーションの結果がエラーの場合
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $itemId = $request->input('item_id');
        $itemData = Item::where(function ($q) use ($itemId) {
            $q->where('item_id', $itemId)->orWhere('id', $itemId);
        })->where('del_flg', 0)->firstOrFail();

        //在庫数追加
        $itemUpdate = $itemData->increment('stock', $request->input('item_stock'));
        $itemData->refresh();

        //商品履歴テーブル(histories)に登録
        $histories = new History();
        $histories->item_name = $itemData->item_name;
        $histories->item_price = $itemData->item_price;
        $histories->status = 4; //4 在庫数追加
        $histories->stock = $itemData->stock;
        $histories->item_id = $itemData->item_id ?? $itemData->id;
        $histories->user_name = Auth::user()->name;

        $histories->save();

        return view('mercari/editComplete', ['itemUpdate' => $itemUpdate, 'itemData' => $itemData]);
    }

    //商品情報登録画面
    public function addItem()
    {
        return view('mercari/addItem');
    }

    //商品情報登録
    public function addItemInsert(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all(), [
            'item_name'   => ['required', 'string', 'max:255'],
            'item_price'  => ['required', 'integer', 'min:1', 'max:1000000'], // 1〜100万円
            'item_stock'  => ['required', 'integer', 'min:0', 'max:10000'],   // 0〜10000個
            'item_detail' => ['nullable', 'string', 'max:2000'],
        ]);
        //バリデーションの結果がエラーの場合
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        //商品名
        $item_name = $request->input('item_name');

        // 類似商品チェック（DoS防止のため上限50件に絞り込み）
        $query = Item::query()->where('del_flg', 0);
        if (!empty($item_name)) {
            $this->applyLikeQuery($query, 'item_name', $item_name);
        }
        $searchs = $query->limit(50)->get(['item_name']);

        $itemBox = [];
        foreach ($searchs as $item) {
            similar_text($item_name, $item->item_name, $result);
            if ($result > 60) {
                $itemBox[] = $item->item_name;
            }
        }

        $redirect_ck = $request->input('redirect');

        //もし類似の商品があれば警告でリダイレクト
        if ($redirect_ck == null && isset($itemBox[0])) {
            $redirect = true;
            return redirect('/addItem')->withInput()->with('flash_message', '似ている商品があります')->with('item', $itemBox)->with('redirect', $redirect);
        }

        // 暗号学的に安全な乱数で重複のないユニークIDを生成
        do {
            $unique_id = random_int(100000, 999999);
        } while (Item::where('item_id', $unique_id)->exists());

        // Itemモデル作成
        $items = new Item();
        $items->item_name = $item_name.'【'.$unique_id.'】';
        $items->item_price = $request->input('item_price');
        $items->stock = $request->input('item_stock');
        $items->detail = $request->input('item_detail');
        $items->item_id = $unique_id;
        $items->del_flg = 0;

        $insert_ck = $items->save();

        //商品履歴テーブル(histories)に登録
        $histories = new History();
        $histories->item_name = $item_name.'【'.$unique_id.'】';
        $histories->item_price = $request->input('item_price');
        $histories->status = 2; //2 登録
        $histories->stock = $request->input('item_stock');
        $histories->item_id = $unique_id;
        $histories->user_name = Auth::user()->name;

        $histories->save();

        //商品売上テーブル(solds)に登録(先にレコードだけ作っておく)
        $solds = new Sold();
        $solds->item_id = $unique_id;
        $solds->item_name = $item_name;
        $solds->sold_count = 0;

        $solds->save();

        return view('mercari/addItem', ['insert_ck' => $insert_ck]);
    }

    //メルカリの商品管理リスト
    public function orderList()
    {
        return view('mercari/order');
    }

    //メルカリの商品管理リスト
    public function mList(Request $request)
    {
        //全ての検索結果
        $mercaris = Mercari::all();

        //未発送の検索結果
        $mercaris_mihassou = Mercari::where('shorikubun', '1')
            ->orderBy('created_at', 'asc')
            ->paginate(15, ['*'], 'mihassou_page');

        //発送済みの検索結果
        $mercaris_hassouzumi = Mercari::where('shorikubun', '2')
            ->orderBy('created_at', 'asc')
            ->paginate(15, ['*'], 'hassouzumi_page');

        //返品の検索結果
        $mercaris_henpin = Mercari::where('shorikubun', '3')
            ->orderBy('created_at', 'asc')
            ->paginate(15, ['*'], 'henpin_page');

        //キーワード受け取り
        $shouhin_id = $request->input('shouhin_id');
        $shouhin_id = mb_convert_kana($shouhin_id, 'n');
        $shouhin_name = $request->input('shouhin_name');
        $buyer_name = $request->input('buyer_name');
        $buy_date = $request->input('buy_date');

        $query = Mercari::query();

        //商品ID
        if (!empty($shouhin_id)) {
            $query->where('mid', $shouhin_id);
        }
        //商品名（ワイルドカードエスケープ適用）
        if (!empty($shouhin_name)) {
            $this->applyLikeQuery($query, 'item_name', $shouhin_name);
        }
        //購入者名（ワイルドカードエスケープ適用）
        if (!empty($buyer_name)) {
            $this->applyLikeQuery($query, 'buyer_name', $buyer_name);
        }
        //購入日
        if (!empty($buy_date)) {
            $buy_date_formatted = date('Y-m-d', strtotime($buy_date));
            $query->whereDate('created_at', $buy_date_formatted);
        }

        //クエリの実行
        $searchs = $query->paginate(15);

        return view('mercari/m_item', [
            'mercaris'            => $mercaris,
            'mercaris_mihassou'   => $mercaris_mihassou,
            'mercaris_hassouzumi' => $mercaris_hassouzumi,
            'mercaris_henpin'     => $mercaris_henpin,
            'searchs'             => $searchs
        ]);
    }

    // チャート表示用
    public function chart()
    {
        $items = Sold::all();

        return view('mercari/chart', compact('items'));
    }
}
